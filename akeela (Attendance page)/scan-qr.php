<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Entry Scanner</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="index.php">📊 Records Board</a> | <a href="generate-qr.php">📋 Register Employee</a>
        </div>
        <h2>2. Dynamic Terminal QR Code Attendance Input</h2>
        
        <div class="grid-2">
            <div>
                <h3>Terminal Input Action</h3>
                <div class="form-group">
                    <label>Terminal Action Mode Selection:</label>
                    <select id="scan_mode">
                        <option value="CheckIn">Check-In Action (Morning Entry Point)</option>
                        <option value="CheckOut">Check-Out Action (Evening Exit Point)</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-top: 20px; background: #eaecf0; padding: 20px; border-radius: 5px; text-align: center;">
                    <label style="margin-bottom:10px;">Click Button Below to Capture/Upload QR</label>
                    <input type="file" id="qr_file_input" accept="image/*" capture="environment" style="display:none;" onchange="processCapturedQR()">
                    <button class="btn btn-success" onclick="document.getElementById('qr_file_input').click()">📷 Open Camera / Scan QR</button>
                </div>
            </div>
            
            <div id="resultPanel" style="background: #f1f3f9; padding: 20px; border-radius: 8px; display:none;">
                <h3>System Logs Stream Response</h3>
                <p><strong>Employee Name:</strong> <span id="log_name" style="color:var(--primary); font-weight:bold; font-size:20px;">-</span></p>
                <p><strong>Status Message:</strong> <span id="log_status">-</span></p>
                <p><strong>Timestamp Logged:</strong> <span id="log_time">-</span></p>
            </div>
        </div>
    </div>

    <script>
    function processCapturedQR() {
        const fileInput = document.getElementById('qr_file_input');
        if (fileInput.files.length === 0) return;

        const mode = document.getElementById('scan_mode').value;
        const formData = new FormData();
        formData.append('action', 'scan_and_log_attendance');
        formData.append('qr_image', fileInput.files[0]);
        formData.append('mode', mode);

        // Notify user processing is happening
        document.getElementById('resultPanel').style.display = 'block';
        document.getElementById('log_name').innerText = "Processing Image Scan...";
        document.getElementById('log_status').innerText = "Reading QR parameters Matrix...";

        fetch('process-attendance.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                document.getElementById('log_name').innerText = data.employee_name;
                document.getElementById('log_status').innerText = data.message;
                document.getElementById('log_time').innerText = data.time;
            } else {
                alert("Operation Flag Fault: " + data.message);
                document.getElementById('resultPanel').style.display = 'none';
            }
            // Clear file input cache tracking sequence
            fileInput.value = "";
        }).catch(err => {
            alert("Error parsing image. Ensure QR code is clear.");
            document.getElementById('resultPanel').style.display = 'none';
        });
    }
    </script>
</body>
</html>