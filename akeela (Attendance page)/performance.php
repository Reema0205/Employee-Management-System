<?php 
// 1. Start session to persist success messages across redirects
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php'; 

// Variables for Form Data
$id = '';
$employee_name = '';
$review_period = '';
$kpi_score = '';
$reviewed_by = '';
$feedback = '';

$message = '';

// Retrieve message from session if it exists (set from redirect)
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']); // Clear it so it only shows once
}

// Fetch Registered Employees from database for the dropdown selection
$registered_employees = [];
try {
    $empStmt = $conn->query("SELECT name FROM employees ORDER BY name ASC");
    $registered_employees = $empStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $message = "Error fetching employees: " . $e->getMessage();
}

// Handle POST actions (Add, Update, Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    $id = $_POST['id'] ?? '';
    $employee_name = trim($_POST['employee_name'] ?? '');
    $review_period = trim($_POST['review_period'] ?? '');
    $kpi_score = trim($_POST['kpi_score'] ?? '');
    $reviewed_by = trim($_POST['reviewed_by'] ?? '');
    $feedback = trim($_POST['feedback'] ?? '');
    $review_date = date('Y-m-d'); // Current Date

    try {
        if ($action === 'add' && !empty($employee_name)) {
            $sql = "INSERT INTO performance_reviews (employee_name, review_period, kpi_score, reviewed_by, feedback, review_date) 
                    VALUES (:employee_name, :review_period, :kpi_score, :reviewed_by, :feedback, :review_date)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':employee_name' => $employee_name,
                ':review_period' => $review_period,
                ':kpi_score' => $kpi_score,
                ':reviewed_by' => $reviewed_by,
                ':feedback' => $feedback,
                ':review_date' => $review_date
            ]);
            
            // Set message in session and redirect to prevent duplicate submission on refresh
            $_SESSION['flash_message'] = "Record added successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } 
        
        elseif ($action === 'update' && !empty($id)) {
            $sql = "UPDATE performance_reviews SET 
                        employee_name = :employee_name, 
                        review_period = :review_period, 
                        kpi_score = :kpi_score, 
                        reviewed_by = :reviewed_by, 
                        feedback = :feedback 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':employee_name' => $employee_name,
                ':review_period' => $review_period,
                ':kpi_score' => $kpi_score,
                ':reviewed_by' => $reviewed_by,
                ':feedback' => $feedback,
                ':id' => $id
            ]);
            
            $_SESSION['flash_message'] = "Record updated successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } 
        
        elseif ($action === 'delete' && !empty($id)) {
            $sql = "DELETE FROM performance_reviews WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $_SESSION['flash_message'] = "Record deleted successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch all records to show in the table
$records = [];
try {
    $stmt = $conn->query("SELECT * FROM performance_reviews ORDER BY id DESC");
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Handle error silently or log
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Review</title>
    <style>
        /* Modern Dark Neon UI Style */
        :root {
            --bg-dark: #0f172a;
            --panel-bg: #1e293b;
            --border-color: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent-primary: #2563eb;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
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

        .container {
            width: 100%;
            max-width: 1100px;
            background: var(--panel-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

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
        }

        /* Twin Columns Layout */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 24px;
            padding: 24px;
        }

        @media (max-width: 850px) {
            .main-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Form Styling */
        .form-section {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        input, select, textarea {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px;
            color: var(--text-main);
            font-size: 14px;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent-primary);
        }

        textarea {
            resize: none;
            height: 80px;
        }

        /* Action Buttons Grid */
        .btn-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .btn {
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: white;
            text-align: center;
            transition: opacity 0.2s;
        }

        .btn:hover { opacity: 0.9; }
        .btn-add { background: var(--success-color); }
        .btn-update { background: var(--accent-primary); }
        .btn-delete { background: var(--danger-color); }
        .btn-clear { background: var(--warning-color); }

        /* Table Section Styling */
        .table-section {
            background: rgba(0, 0, 0, 0.15);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 16px;
            overflow-x: auto;
        }

        .table-section h3 {
            font-size: 14px;
            margin-bottom: 12px;
            color: var(--text-main);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            color: var(--text-muted);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
        }

        tbody tr {
            cursor: pointer;
            transition: background 0.2s;
        }

        tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* Modern Success Toast Alert Styling */
        .alert-container {
            margin-bottom: 20px;
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .alert-msg-success {
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(16, 185, 129, 0.06);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-left: 4px solid var(--success-color);
            color: #e2e8f0;
            padding: 14px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.05);
        }

        .alert-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(16, 185, 129, 0.15);
            color: var(--success-color);
            width: 26px;
            height: 26px;
            border-radius: 50%;
            font-size: 14px;
            font-weight: bold;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="window-header">
        <h2>Performance Review Management</h2>
    </div>

    <div class="main-layout">
        <div>
            <?php if (!empty($message)): ?>
                <div class="alert-container" id="success_alert">
                    <div class="alert-msg-success">
                        <div class="alert-icon">✓</div>
                        <div>
                            <span style="color: var(--success-color); font-weight: 600;">Notification:</span> 
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form id="reviewForm" method="POST" action="">
                <input type="hidden" name="id" id="row_id" value="<?php echo htmlspecialchars($id); ?>">
                <input type="hidden" name="action" id="form_action" value="add">

                <div class="form-section">
                    <div class="form-group">
                        <label>Employee:</label>
                        <select name="employee_name" id="employee_name" required>
                            <option value="">Select Employee</option>
                            <?php foreach ($registered_employees as $emp): ?>
                                <option value="<?php echo htmlspecialchars($emp['name']); ?>" <?php echo $employee_name == $emp['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($emp['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Review Period:</label>
                        <select name="review_period" id="review_period" required>
                            <option value="Q1 2026" <?php echo $review_period == 'Q1 2026' ? 'selected' : ''; ?>>Q1 2026</option>
                            <option value="Q4 2025" <?php echo $review_period == 'Q4 2025' ? 'selected' : ''; ?>>Q4 2025</option>
                            <option value="Q3 2025" <?php echo $review_period == 'Q3 2025' ? 'selected' : ''; ?>>Q3 2025</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>KPI Score (0-100):</label>
                        <input type="number" name="kpi_score" id="kpi_score" step="0.01" min="0" max="100" required placeholder="85.50" value="<?php echo htmlspecialchars($kpi_score); ?>">
                    </div>

                    <div class="form-group">
                        <label>Reviewed By:</label>
                        <select name="reviewed_by" id="reviewed_by" required>
                            <option value="Admin" <?php echo $reviewed_by == 'Admin' ? 'selected' : ''; ?>>Admin</option>
                            <option value="HR Manager" <?php echo $reviewed_by == 'HR Manager' ? 'selected' : ''; ?>>HR Manager</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Feedback:</label>
                        <textarea name="feedback" id="feedback" placeholder="Enter performance feedback..."><?php echo htmlspecialchars($feedback); ?></textarea>
                    </div>

                    <div class="btn-grid">
                        <button type="submit" onclick="setAction('add')" class="btn btn-add">Add</button>
                        <button type="submit" onclick="setAction('update')" class="btn btn-update">Update</button>
                        <button type="submit" onclick="setAction('delete')" class="btn btn-delete">Delete</button>
                        <button type="button" onclick="clearForm()" class="btn btn-clear">Clear</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h3>Performance Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Period</th>
                        <th>KPI Score</th>
                        <th>Reviewed By</th>
                        <th>Review Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted);">No records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($records as $index => $row): ?>
                            <tr onclick="populateForm(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <td><?php echo count($records) - $index; ?></td>
                                <td><?php echo htmlspecialchars($row['review_period']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['kpi_score'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($row['reviewed_by']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['review_date']))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Function to hide the success alert message
function hideAlert() {
    const alertBox = document.getElementById('success_alert');
    if (alertBox) {
        alertBox.style.opacity = '0';
        alertBox.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            alertBox.style.display = 'none';
        }, 300);
    }
}

// Event Listeners to monitor user interactions inside the form
document.getElementById('reviewForm').addEventListener('input', hideAlert);
document.getElementById('reviewForm').addEventListener('change', hideAlert);

// Auto-hide alert after 4 seconds automatically
setTimeout(hideAlert, 4000);

// Button action control
function setAction(actionName) {
    document.getElementById('form_action').value = actionName;
}

// Populate Form when clicking a table row
function populateForm(data) {
    document.getElementById('row_id').value = data.id;
    document.getElementById('employee_name').value = data.employee_name;
    document.getElementById('review_period').value = data.review_period;
    document.getElementById('kpi_score').value = data.kpi_score;
    document.getElementById('reviewed_by').value = data.reviewed_by;
    document.getElementById('feedback').value = data.feedback;
    hideAlert();
}

// Clear Form function
function clearForm() {
    document.getElementById('row_id').value = '';
    document.getElementById('form_action').value = 'add';
    document.getElementById('employee_name').value = '';
    document.getElementById('review_period').value = 'Q1 2026';
    document.getElementById('kpi_score').value = '';
    document.getElementById('reviewed_by').value = 'Admin';
    document.getElementById('feedback').value = '';
    hideAlert();
}
</script>
</body>
</html>