<?php 
require_once 'config/database.php'; 

$generated_emp_id = '';
$registration_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_employee') {
    try {
        $emp_id = trim($_POST['emp_id']);
        $name = trim($_POST['name']);
        $age = trim($_POST['age']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);

        if (!empty($emp_id) && !empty($name)) {
            $checkSql = "SELECT COUNT(*) FROM employees WHERE emp_id = :emp_id";
            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([':emp_id' => $emp_id]);
            
            if ($checkStmt->fetchColumn() > 0) {
                echo "<script>alert('Error: Employee ID already exists inside the database!');</script>";
            } else {
                $insertSql = "INSERT INTO employees (emp_id, name, age, email, phone) 
                              VALUES (:emp_id, :name, :age, :email, :phone)";
                
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->execute([
                    ':emp_id' => $emp_id,
                    ':name' => $name,
                    ':age' => $age,
                    ':email' => $email,
                    ':phone' => $phone
                ]);

                $generated_emp_id = $emp_id;
                $registration_success = true;
            }
        }
    } catch (Exception $e) {
        echo "<script>alert('Database insertion failure: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Employee QR</title>
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
            max-width: 960px;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.4);
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
            color: #ef4444;
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
            grid-template-columns: 1fr 380px;
            gap: 24px;
            padding: 24px;
        }

        @media (max-width: 768px) {
            .main-form-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Form Controls styling */
        .form-inputs-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .form-row {
            display: flex;
            gap: 16px;
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
        input[type="number"],
        input[type="email"] {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 14px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        /* Buttons Styling */
        .btn {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--accent-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
        }

        /* Terminal Sidebar Styling */
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

        /* QR Frame Box and States */
        .qr-display-box {
            background: rgba(255, 255, 255, 0.01);
            border: 2px dashed #475569;
            border-radius: 8px;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 280px;
            transition: all 0.3s ease;
        }

        .qr-display-box.generated {
            border-color: var(--success-color);
            background: rgba(16, 185, 129, 0.02);
        }

        .qr-wrapper {
            background: #ffffff;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            display: inline-block;
        }

        .success-notify {
            color: var(--success-color);
            font-weight: 600;
            font-size: 13px;
            margin-top: 15px;
            text-align: center;
            background: rgba(16, 185, 129, 0.1);
            padding: 8px 12px;
            border-radius: 4px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .btn-download {
            background: var(--success-color);
            color: #fff;
            margin-top: 12px;
            padding: 8px 15px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background 0.2s ease;
        }

        .btn-download:hover {
            background: var(--success-hover);
        }

        /* Utility Entry Animations */
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

    <div class="container animate-fade-in">
        <div class="window-header">
            <h2>Employee Registration & QR Generator</h2>
            <span class="close-icon" onclick="window.location.href='index.php'">&times;</span>
        </div>

        <div class="nav-links">
            <a href="index.php">📊 Records Board</a> | 
            <a href="generate-qr.php" class="active-nav">📋 Register Station</a>
        </div>
        
        <div class="main-form-layout">
            
            <div class="form-inputs-section">
                <form id="qrForm" method="POST" action="generate-qr.php">
                    <input type="hidden" name="action" value="register_employee">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Employee ID:</label>
                            <input type="text" name="emp_id" id="emp_id" required placeholder="e.g. EMP101" value="<?php echo htmlspecialchars($generated_emp_id); ?>">
                        </div>
                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" name="name" id="name" required placeholder="e.g. Meflitha Iffath">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Age:</label>
                            <input type="number" name="age" id="age" required placeholder="23">
                        </div>
                        <div class="form-group">
                            <label>Email Address:</label>
                            <input type="email" name="email" id="email" required placeholder="name@company.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Phone Number:</label>
                            <input type="text" name="phone" id="phone" required placeholder="+94 7xxxxxxxx">
                        </div>
                    </div>
                    
                    <div style="margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">
                            ⚙️ Generate QR & Save Record
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="scanner-terminal-box">
                <h3>🖼️ System Output Frame</h3>
                
                <div id="qr_box_container" class="qr-display-box <?php echo $registration_success ? 'generated' : ''; ?>">
                    
                    <?php if (!$registration_success): ?>
                        <div id="qr_placeholder" style="text-align: center; color: #64748b;">
                            <span style="font-size: 40px; display: block; margin-bottom: 10px;">📊</span>
                            <p style="font-size: 13px;">Fill form details and submit to compile security QR matrix code context.</p>
                        </div>
                    <?php else: ?>
                        <div id="qr_matrix_view" style="text-align: center;">
                            <div class="qr-wrapper">
                                <?php 
                                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" . urlencode($generated_emp_id);
                                ?>
                                <img id="qr_img" src="<?php echo $qrUrl; ?>" style="width:160px; height:160px; display: block;" alt="Employee System identity QR">
                            </div>
                            
                            <p id="downloadMsg" class="success-notify">✅ Employee Registered Inside Database!</p>
                            
                            <button type="button" class="btn-download" onclick="downloadQRMatrix()">
                                📥 Download QR Identity
                            </button>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <script>
    function downloadQRMatrix() {
        const qrImg = document.getElementById('qr_img');
        if (!qrImg) return;
        
        const qrImgSrc = qrImg.src;
        const empId = document.getElementById('emp_id').value;

        fetch(qrImgSrc)
            .then(response => response.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `QR_${empId || 'Employee'}.png`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);
            })
            .catch(() => alert('Failed to download QR code image.'));
    }
    </script>
</body>
</html>