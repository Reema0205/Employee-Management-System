<?php
require_once 'auth_check.php'; // redirects to login.html if not logged in
require_once 'db.php';

// pull employees straight from the unified employees table for the table below
$employees = [];
$res = mysqli_query($conn, "SELECT * FROM employees ORDER BY id ASC LIMIT 10");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $employees[] = $row;
    }
}
$countRes = mysqli_query($conn, "SELECT COUNT(*) c FROM employees");
$totalEmployees = $countRes ? (mysqli_fetch_assoc($countRes)['c'] ?? 0) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS Dashboard</title>

    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">EMS</div>

        <div class="menu">
            <ul>
                <li class="active"><a href="index.php">🏠 Dashboard</a></li>
                <li><a href="vidarth/employee_Page/Page.html">👥 Employees</a></li>
                <li><a href="attendance_page/index.php">📅 Attendance</a></li>
                <li><a href="Sanfara/Leavemanagemant/index.html">📝 Leave</a></li>
                <li><a href="vidarth/payroll_page/Page.html">💲 Payroll</a></li>
                <li><a href="attendance_page/performance.php">📊 KPI</a></li>
                <li><a href="Sanfara/Reportpage/Report.html">📄 Reports</a></li>
                <li><a href="settings.php">⚙ Settings</a></li>
                <li><a href="logout.php">↩ Logout</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">

        <div class="topbar">
            <div>
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></h2>
                <p>Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'Admin'); ?></p>
            </div>

            <div class="date-time">
                <?php echo date("l, j F Y"); ?><br>
                <?php echo date("h:i:s A"); ?>
            </div>
        </div>

        <!-- Cards -->
        <div class="cards">
            <div class="card red">
                <h3>Total Employees</h3>
                <p><?php echo (int)$totalEmployees; ?></p>
            </div>

            <div class="card orange">
                <h3>Present Today</h3>
                <p>18</p>
            </div>

            <div class="card green">
                <h3>On Leave</h3>
                <p>3</p>
            </div>

            <div class="card blue">
                <h3>Pending Payroll</h3>
                <p>2</p>
            </div>
        </div>

        <!-- Table -->
        <div class="table-section">
            <h2>Recent Employees</h2>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Job Role</th>
                    <th>Salary</th>
                    <th>Status</th>
                </tr>

                <?php if (empty($employees)): ?>
                <tr>
                    <td colspan="6">No employees found. Add some from the Employees page.</td>
                </tr>
                <?php else: foreach ($employees as $emp): ?>
                <tr>
                    <td><?php echo htmlspecialchars($emp['id']); ?></td>
                    <td><?php echo htmlspecialchars($emp['name']); ?></td>
                    <td><?php echo htmlspecialchars($emp['dept']); ?></td>
                    <td><?php echo htmlspecialchars($emp['role']); ?></td>
                    <td><?php echo number_format($emp['salary']); ?></td>
                    <td><span class="status"><?php echo htmlspecialchars($emp['status']); ?></span></td>
                </tr>
                <?php endforeach; endif; ?>
            </table>
        </div>

    </div>
</div>

</body>
</html>
