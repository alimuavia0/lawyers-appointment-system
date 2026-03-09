<?php
// session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$lawyers = $conn->query("
    SELECT u.id, u.username, u.email, u.created_at, 
           ld.specialization, ld.location, ld.experience
    FROM users u 
    LEFT JOIN lawyer_details ld ON u.id = ld.user_id 
    WHERE u.user_type = 'lawyer'
    ORDER BY u.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyers Management - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-dashboard">
    <div class="admin-sidebar">
        <!-- Same sidebar structure -->
         
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Lawyers Management</h2>
            <p>Welcome, <?php echo $_SESSION['admin_name']; ?></p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <span>📊</span> Dashboard
            </a>
            <a href="users.php" class="nav-item">
                <span>👥</span> All Users
            </a>
            <a href="lawyers.php" class="nav-item">
                <span>⚖️</span> Lawyers
            </a>
            <a href="customers.php" class="nav-item active">
                <span>👤</span> Customers
            </a>
            <a href="appointments.php" class="nav-item">
                <span>📅</span> Appointments
            </a>
            <a href="../logout.php" class="nav-item logout">
                <span>🚪</span> Logout
            </a>
        </nav>
    </div>

    </div>




    <div class="admin-main">
        <div class="admin-header">
            <h1>Lawyers Management</h1>
            <div class="header-actions">
                <span>Total Lawyers: <?php echo $lawyers->num_rows; ?></span>
            </div>
        </div>

        <div class="lawyers-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Location</th>
                        <th>Experience</th>
                        <th>Registration Date</th>
                        <th>Profile Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($lawyer = $lawyers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $lawyer['id']; ?></td>
                        <td><?php echo $lawyer['username']; ?></td>
                        <td><?php echo $lawyer['email']; ?></td>
                        <td><?php echo $lawyer['specialization'] ?: 'Not set'; ?></td>
                        <td><?php echo $lawyer['location'] ?: 'Not set'; ?></td>
                        <td><?php echo $lawyer['experience'] ? $lawyer['experience'].' years' : 'Not set'; ?></td>
                        <td><?php echo $lawyer['created_at']; ?></td>
                        <td>
                            <span class="status-<?php echo $lawyer['specialization'] ? 'complete' : 'incomplete'; ?>">
                                <?php echo $lawyer['specialization'] ? 'Complete' : 'Incomplete'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>