<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMS Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>
<body>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>EMS</h2>
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="vidarth/employee_Page/Page.html">Employees</a></li>
            <li><a href="attendance_page/index.php">Attendance</a></li>
            <li><a href="Sanfara/Leavemanagemant/index.html">Leave</a></li>
            <li><a href="vidarth/payroll_page/Page.html">Payroll</a></li>
            <li><a href="attendance_page/performance.php">KPI</a></li>
            <li><a href="Sanfara/Reportpage/Report.html">Reports</a></li>
            <li class="active"><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main">

        <div class="top-bar">
            <h2>Settings</h2>
        </div>

        <div class="content">

            <div class="section-title">Accounting Settings</div>

            <div class="form-container">

                <!-- Edit Profile -->
                <div class="card">
                    <h3>Edit Profile</h3>

                    <label>First Name</label>
                    <input type="text">

                    <label>Last Name</label>
                    <input type="text">

                    <label>Email Address</label>
                    <input type="email">

                    <label>Phone Number</label>
                    <input type="text">

                    <label>Address</label>
                    <textarea></textarea>
                </div>

                <!-- Change Password -->
                <div class="card">
                    <h3>Change Password</h3>

                    <label>Current Password</label>
                    <input type="password">

                    <div class="row">
                        <div>
                            <label>New Password</label>
                            <input type="password">
                        </div>

                        <div>
                            <label>Confirm Password</label>
                            <input type="password">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Buttons -->
            <div class="buttons">
                <button class="save">Save</button>
                <button class="cancel">Cancel</button>
            </div>

        </div>

    </div>

</div>

</body>
</html>