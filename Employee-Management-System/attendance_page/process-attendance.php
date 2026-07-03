<?php
require_once 'config/database.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

// 1. Employee Registration Logic Area
if ($action === 'register_employee') {
    try {
        $stmt = $conn->prepare("INSERT INTO attendance_employees (emp_id, name, age, email, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['emp_id'], $_POST['name'], $_POST['age'], $_POST['email'], $_POST['phone']]);
        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

// 2. Scan & Attendance Logging Core Logic
if ($action === 'scan_and_log_attendance') {
    
    $emp_id = null;
    $mode = $_POST['mode'] ?? '';
    $current_date = date('Y-m-d');
    $current_time = date('H:i:s');

    // --- HYBRID QR DECODING COUPLER LAYER ---
    
    // முறை A: ஜாவாஸ்கிரிப்ட் மூலம் நேரடியாகப் படிக்கப்பட்ட QR உரை வந்தால் (Live Cam / Browser Scan)
    if (isset($_POST['qr_data']) && !empty(trim($_POST['qr_data']))) {
        $emp_id = trim($_POST['qr_data']);
    } 
    // முறை B: பழையபடி முழுப் படமாக (Image Stream) அப்லோட் செய்யப்பட்டு வந்தால்
    elseif (isset($_FILES['qr_image']) && $_FILES['qr_image']['error'] === 0) {
        $image_path = $_FILES['qr_image']['tmp_name'];

        // Secure Online Free Decoder API call logic
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.qrserver.com/v1/read-qr-code/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'file' => new CURLFile($image_path)
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        $emp_id = $result[0]['symbol'][0]['data'] ?? null;
    }

    // இரண்டு முறைகளிலும் ID கிடைக்கவில்லை என்றால் எரர் அனுப்பும்
    if (empty($emp_id)) {
        echo json_encode(["success" => false, "message" => "No QR Data string or valid Image stream received. Try again."]);
        exit;
    }

    // --- DATABASE ATTENDANCE ACTION TRANSACTION ---
    try {
        // செக் செய்யப் போகும் பயனர் ஐடி உள்ளதா எனத் தேடுதல்
        $empCheck = $conn->prepare("SELECT name FROM attendance_employees WHERE emp_id = ?");
        $empCheck->execute([$emp_id]);
        $employee = $empCheck->fetch(PDO::FETCH_ASSOC);

        if (!$employee) {
            echo json_encode(["success" => false, "message" => "Decoded ID ($emp_id) matches no registered profile."]);
            exit;
        }

        $employee_name = $employee['name'];
        $formatted_display_time = date('h:i A', strtotime($current_time));

        // காலையில் செக்-இன் செய்யும் லாஜிக்
        if ($mode === 'CheckIn') {
            $status = 'Present';
            $remarks = 'On time';
            
            // காலை 09:00 மணிக்கு மேல் வந்தால் லேட் என்ட்ரி
            if (strtotime($current_time) > strtotime('09:00:00')) {
                $status = 'Late';
                $remarks = 'Traffic / Personal Entry';
            }

            // ஏற்கனவே அன்று செக்-இன் செய்திருந்தால் அப்டேட் செய்யும் அல்லது புதிதாக இன்செர்ட் செய்யும்
            $stmt = $conn->prepare("INSERT INTO attendance_records (emp_id, attendance_date, check_in, status, remarks) 
                                    VALUES (?, ?, ?, ?, ?) 
                                    ON DUPLICATE KEY UPDATE check_in = VALUES(check_in), status = VALUES(status), remarks = VALUES(remarks)");
            $stmt->execute([$emp_id, $current_date, $current_time, $status, $remarks]);
            
            echo json_encode([
                "success" => true, 
                "employee_name" => $employee_name, 
                "message" => "Check-In Success! Marked Status: " . $status, 
                "time" => $formatted_display_time
            ]);
        } 
        
        else if ($mode === 'CheckOut') {
            // செக்-அவுட் செய்யும்போது ஸ்டேட்டஸ் மற்றும் ரிமார்க்ஸையும் சேர்த்து அப்டேட் செய்கிறோம்
            $stmt = $conn->prepare("UPDATE attendance_records 
                                    SET check_out = ?, status = ?, remarks = ? 
                                    WHERE emp_id = ? AND attendance_date = ?");
            
            $checkout_status = 'Present'; // அல்லது நீங்கள் 'Left' / 'Completed' என வைக்கலாம்
            $checkout_remarks = 'Checked Out';
            
            $stmt->execute([$current_time, $checkout_status, $checkout_remarks, $emp_id, $current_date]);
            
            echo json_encode([
                "success" => true, 
                "employee_name" => $employee_name, 
                "message" => "Check-Out recorded successfully at " . $formatted_display_time, 
                "time" => $formatted_display_time
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $e->getMessage()]);
    }
    exit;
}
?>