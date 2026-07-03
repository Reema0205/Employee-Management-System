<?php 
require_once 'config/database.php'; 

$message = "";

// Handle Intelligent Attendance Logging (Check-In / Check-Out automatic routing)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_attendance') {
    try {
        $emp_id = $_POST['employee_id'];
        $attendance_date = $_POST['attendance_date'];
        $current_time = date('H:i:s');
        $status = $_POST['status'];

        if (!empty($emp_id) && !empty($attendance_date)) {
            
            // 1. Check if there is already an existing record for this employee today
            $checkSql = "SELECT id, check_in, check_out FROM attendance_records 
                         WHERE emp_id = :emp_id AND attendance_date = :attendance_date LIMIT 1";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([
                ':emp_id' => $emp_id,
                ':attendance_date' => $attendance_date
            ]);
            $existingRecord = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingRecord) {
                // Record exists -> This is an Evening Check-Out action
                $updateSql = "UPDATE attendance_records 
                              SET check_out = :check_out, remarks = 'Auto Checked Out via Dashboard' 
                              WHERE id = :id";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->execute([
                    ':check_out' => $current_time,
                    ':id' => $existingRecord['id']
                ]);
                $message = "Check-Out updated successfully at " . date("h:i A", strtotime($current_time));
            } else {
                // No record today -> This is a Morning Check-In action
                $insertSql = "INSERT INTO attendance_records (emp_id, attendance_date, check_in, check_out, status, remarks) 
                              VALUES (:emp_id, :attendance_date, :check_in, '00:00:00', :status, 'Auto Checked In via Dashboard')";
                
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->execute([
                    ':emp_id' => $emp_id,
                    ':attendance_date' => $attendance_date,
                    ':check_in' => $current_time,
                    ':status' => $status
                ]);
                $message = "Check-In registered successfully at " . date("h:i A", strtotime($current_time));
            }

            // Redirect to refresh and show notification alert
            echo "<script>alert('" . addslashes($message) . "'); window.location.href='index.php';</script>";
            exit();
        }
    } catch (Exception $e) {
        echo "<script>alert('Error processing request: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Dashboard</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <style>
        /* Modern Dark Neon Core UI Tokens */
        :root {
            --bg-dark: #0f172a;
            --panel-bg: #1e293b;
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #2563eb;
            --accent-hover: #1d4ed8;
            --success-color: #10b981;
            --success-hover: #059669;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
            --warning-color: #f59e0b;
            --warning-hover: #d97706;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        /* Container Layout */
        .container {
            width: 100%;
            max-width: 1000px;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.4);
            padding-bottom: 24px;
            overflow: hidden;
        }

        /* Window Header Bar */
        .window-header {
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        .window-header h2 {
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: var(--text-main);
        }

        .close-icon {
            color: var(--text-muted);
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close-icon:hover {
            color: var(--danger-color);
        }

        /* Navigation Links */
        .nav-links {
            padding: 15px 24px;
            background: rgba(0, 0, 0, 0.1);
            font-size: 13px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
        }

        .nav-links a {
            color: var(--text-muted);
            text-decoration: none;
            margin: 0 5px;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: var(--text-main);
        }

        .nav-links a.active-nav {
            color: var(--accent-primary);
            font-weight: 600;
        }

        /* Twin Columns Dashboard Grid Setup */
        .main-form-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            padding: 24px;
        }

        @media (max-width: 850px) {
            .main-form-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Form Controls Styling */
        .form-inputs-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        .form-row {
            display: flex;
            gap: 16px;
            margin-bottom: 16px;
        }

        @media (max-width: 480px) {
            .form-row {
                flex-direction: column;
                gap: 16px;
            }
        }

        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full-width {
            flex: none;
            width: 100%;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        select {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 14px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            width: 100%;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="time"]:focus,
        select:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        select option {
            background: #1e293b;
            color: var(--text-main);
        }

        /* Terminal Sidebar & Tabs Styling */
        .scanner-terminal-box {
            background: rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
        }

        .scanner-terminal-box h3 {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 15px;
        }

        .scanner-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            background: rgba(255, 255, 255, 0.05);
            padding: 5px;
            border-radius: 6px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .scan-tab-btn {
            flex: 1;
            background: transparent;
            color: var(--text-muted);
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .scan-tab-btn.active {
            background: var(--accent-primary);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .file-upload-zone {
            border: 2px dashed #475569;
            padding: 40px 20px;
            text-align: center;
            border-radius: 6px;
            background: rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-upload-zone:hover {
            border-color: var(--accent-primary);
            background: rgba(37, 99, 235, 0.03);
        }

        #interactive-reader {
            width: 100%; 
            background: #000; 
            border-radius: 6px; 
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.05);
        }

        /* Stream Log Selection Status */
        .stream-log-panel {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            padding: 12px;
            border-radius: 6px;
        }
        .stream-log-panel h4 {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .stream-log-panel p {
            font-size: 13px;
        }
        .stream-log-panel.success {
            border-color: rgba(16, 185, 129, 0.3);
            background: rgba(16, 185, 129, 0.05);
        }
        .stream-log-panel.error {
            border-color: rgba(239, 68, 68, 0.3);
            background: rgba(239, 68, 68, 0.05);
        }

        /* Action Controls Layout Bar */
        .btn-group-control-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 0 24px 24px 24px;
        }

        /* Custom Unified Buttons Design System */
        .btn {
            padding: 11px 20px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s ease;
            color: white;
        }

        .btn-success { background: var(--success-color); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15); }
        .btn-success:hover { background: var(--success-hover); }
        
        .btn-primary { background: var(--accent-primary); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15); }
        .btn-primary:hover { background: var(--accent-hover); }

        .btn-danger { background: var(--danger-color); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15); }
        .btn-danger:hover { background: var(--danger-hover); }

        .btn-warning { background: var(--warning-color); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15); }
        .btn-warning:hover { background: var(--warning-hover); }

        /* Section Layout Dividers */
        .section-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 10px 24px 24px 24px;
        }

        .container > h3 {
            font-size: 15px;
            font-weight: 600;
            padding: 0 24px;
            margin-bottom: 16px;
            letter-spacing: 0.5px;
        }

        /* Modern Grid Responsive Data Table Architecture */
        .table-responsive-wrapper {
            padding: 0 24px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        th {
            background: rgba(0, 0, 0, 0.25);
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: #e2e8f0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        tr.record-row:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Badges status framework styles */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-present {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-late {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-absent {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .remark-text {
            color: var(--text-muted);
            font-size: 12px;
        }

        .empty-table-msg, .error-table-msg {
            text-align: center;
            color: var(--text-muted);
            padding: 30px !important;
            font-style: italic;
        }

        /* Animation utilities tokens */
        .selected-flash {
            animation: flashHighlight 1s ease-out;
        }
        @keyframes flashHighlight {
            0% { background-color: rgba(37, 99, 235, 0.25); }
            100% { background-color: transparent; }
        }

        .animate-fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<a href="../index.php" style="position:fixed;top:14px;left:14px;z-index:9999;background:#1d4ed8;color:#fff;padding:8px 14px;border-radius:8px;font-family:sans-serif;font-size:14px;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,.25);">⬅ Dashboard</a>

    <div class="container animate-fade-in">
        <div class="window-header">
            <h2>Attendance Management Dashboard</h2>
            <span class="close-icon" onclick="window.location.href='index.php'">&times;</span>
        </div>

        <div class="nav-links">
            <a href="generate-qr.php">📋 Register & Generate QR</a> | 
            <a href="index.php" class="active-nav">🔄 Refresh Dashboard</a>
        </div>
        
        <div class="main-form-layout">
            
            <div class="form-inputs-section" id="form_inputs_container">
                <form id="attendanceManualForm" method="POST" action="index.php">
                    <input type="hidden" name="action" value="submit_attendance">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Employee:</label>
                            <select name="employee_id" id="emp_select">
                                <option value="">Select Employee</option>
                                <?php
                                try {
                                    $empSql = "SELECT emp_id, name FROM employees ORDER BY name ASC";
                                    $empStmt = $conn->query($empSql);
                                    while($emp = $empStmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='{$emp['emp_id']}'>{$emp['name']}</option>";
                                    }
                                } catch(Exception $e) {}
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date:</label>
                            <input type="date" name="attendance_date" id="manual_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <!-- Note: Check-In/Out Inputs are hidden from form because the system injects the server current time dynamically -->
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Status Framework:</label>
                            <select name="status" id="status_select">
                                <option value="Present">Present (Default)</option>
                                <option value="Late">Late</option>
                                <option value="Absent">Absent</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="scanner-terminal-box">
                <h3>📷 Terminal QR Scanner (Auto Actions)</h3>
                
                <div class="scanner-tabs">
                    <button type="button" class="scan-tab-btn active" onclick="switchScannerTab('camera-tab', this)">📷 Camera Live Scan</button>
                    <button type="button" class="scan-tab-btn" onclick="switchScannerTab('upload-tab', this)">📁 Upload QR Pic</button>
                </div>

                <div id="camera-tab" class="tab-content active">
                    <div id="interactive-reader"></div>
                </div>

                <div id="upload-tab" class="tab-content">
                    <div class="file-upload-zone" onclick="document.getElementById('qr_image_file').click()">
                        <p style="font-size: 32px; margin: 0 0 10px 0;">🖼️</p>
                        <p style="font-size: 13px; color: #e2e8f0; margin: 0;">Click to browse saved QR Image</p>
                        <p style="font-size: 11px; color: #64748b; margin: 5px 0 0 0;">Supports: PNG, JPG, JPEG</p>
                        <input type="file" id="qr_image_file" accept="image/*" style="display:none;" onchange="processUploadedQRFile(this)">
                    </div>
                </div>
                
                <div id="resultPanel" class="stream-log-panel" style="display:none; margin-top: 15px;">
                    <h4>🎯 Selection Status</h4>
                    <p id="log_status">Ready to scan...</p>
                </div>
            </div>
        </div>

        <div class="btn-group-control-bar">
            <button type="button" class="btn btn-success" onclick="submitManualAttendance()">Submit Attendance</button>
            <button type="button" class="btn btn-primary" onclick="refreshDashboardLogs()">Update</button>
            <button type="button" class="btn btn-danger" onclick="triggerDeleteLockNotify()">Delete</button>
            <button type="button" class="btn btn-warning" onclick="clearInterfaceSelection()">Clear</button>
        </div>

        <hr class="section-divider">

        <h3>Attendance Records</h3>
        
        <div class="table-responsive-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="attendance_table_body">
                    <?php
                    try {
                        $sql = "SELECT r.id, e.name, r.attendance_date, r.check_in, r.check_out, r.status, r.remarks 
                                FROM attendance_records r 
                                JOIN employees e ON r.emp_id = e.emp_id 
                                ORDER BY r.attendance_date DESC, r.id DESC";
                        $stmt = $conn->query($sql);
                        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if(count($records) > 0) {
                            foreach($records as $row) {
                                $status_lower = strtolower($row['status']);
                                $badgeClass = 'badge-' . $status_lower;
                                
                                $inTime = (!empty($row['check_in']) && $row['check_in'] != '00:00:00') ? date("h:i A", strtotime($row['check_in'])) : '-';
                                $outTime = (!empty($row['check_out']) && $row['check_out'] != '00:00:00') ? date("h:i A", strtotime($row['check_out'])) : '-';
                                $formattedDate = date("d/m/Y", strtotime($row['attendance_date']));
                                
                                echo "<tr class='record-row' data-id='{$row['id']}'>";
                                echo "<td>" . $row['id'] . "</td>";
                                echo "<td class='emp-name-cell'>" . htmlspecialchars($row['name']) . "</td>";
                                echo "<td>" . $formattedDate . "</td>";
                                echo "<td>" . $inTime . "</td>";
                                echo "<td>" . $outTime . "</td>";
                                echo "<td><span class='badge {$badgeClass}'>" . $row['status'] . "</span></td>";
                                echo "<td><span class='remark-text'>" . htmlspecialchars($row['remarks']) . "</span></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='empty-table-msg'>No attendance activity tracked inside database yet.</td></tr>";
                        }
                    } catch(Exception $e) {
                        echo "<tr><td colspan='7' class='error-table-msg'>Query Error: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        let html5QrcodeScanner;

        document.addEventListener("DOMContentLoaded", function() {
            startLiveCameraEngine();
        });

        function switchScannerTab(tabId, btn) {
            document.querySelectorAll('.scan-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            btn.classList.add('active');
            document.getElementById(tabId).classList.add('active');

            if(tabId === 'camera-tab') {
                startLiveCameraEngine();
            } else {
                stopLiveCameraEngine();
            }
        }

        function startLiveCameraEngine() {
            if(!html5QrcodeScanner) {
                html5QrcodeScanner = new Html5Qrcode("interactive-reader");
            }
            const config = { fps: 15, qrbox: { width: 230, height: 230 }, aspectRatio: 1.0 };
            
            html5QrcodeScanner.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                document.getElementById('interactive-reader').innerHTML = `
                    <div style="padding:20px; text-align:center; color:#e2e8f0; font-size:12px; background:#1e293b;">
                        ⚠️ Live Feed Idle / Cam permission required.
                    </div>`;
            });
        }

        function stopLiveCameraEngine() {
            if(html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                html5QrcodeScanner.stop().catch(err => console.error(err));
            }
        }

        function onScanSuccess(decodedText) {
            selectEmployeeFromQR(decodedText);
        }

        function processUploadedQRFile(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileScanner = new Html5Qrcode("interactive-reader");
                
                fileScanner.scanFile(file, true)
                .then(decodedText => {
                    selectEmployeeFromQR(decodedText);
                    input.value = "";
                })
                .catch(err => {
                    alert("❌ Could not read QR data matrix from this image.");
                    input.value = "";
                });
            }
        }

        function selectEmployeeFromQR(empIdData) {
            const empSelect = document.getElementById('emp_select');
            const resultPanel = document.getElementById('resultPanel');
            const statusText = document.getElementById('log_status');
            
            let found = false;
            for (let i = 0; i < empSelect.options.length; i++) {
                if (empSelect.options[i].value === empIdData.trim()) {
                    empSelect.selectedIndex = i;
                    found = true;
                    break;
                }
            }

            resultPanel.style.display = 'block';

            if(found) {
                resultPanel.className = "stream-log-panel success";
                statusText.innerHTML = `🎯 <strong>Selected:</strong> Employee <b>"${empSelect.options[empSelect.selectedIndex].text}"</b> recognized.<br><small>Click 'Submit Attendance' to auto-detect Check-In or Check-Out.</small>`;
                
                const formContainer = document.getElementById('form_inputs_container');
                formContainer.classList.add('selected-flash');
                setTimeout(() => formContainer.classList.remove('selected-flash'), 1000);
            } else {
                resultPanel.className = "stream-log-panel error";
                statusText.innerHTML = `❌ <strong>Error:</strong> Employee ID (${empIdData}) not found!`;
            }
        }

        function submitManualAttendance() {
            const emp = document.getElementById('emp_select').value;
            if(!emp) { alert("Please select or scan an employee first!"); return; }
            
            document.getElementById('attendanceManualForm').submit();
        }
        
        function refreshDashboardLogs() { window.location.reload(); }
        function triggerDeleteLockNotify() { alert("🔒 System Admin levels required to mutate structure layers."); }
        function clearInterfaceSelection() { window.location.reload(); }
    </script>
</body>
</html>