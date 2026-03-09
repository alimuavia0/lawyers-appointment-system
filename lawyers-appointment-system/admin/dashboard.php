<?php
// session_start();
include '../config.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$total_lawyers = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type='lawyer'")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type='customer'")->fetch_assoc()['total'];
$total_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments")->fetch_assoc()['total'];
$pending_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status='pending'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-dashboard">
    
    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo $_SESSION['admin_name']; ?></p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <span>📊</span> Dashboard
            </a>
            <a href="users.php" class="nav-item">
                <span>👥</span> All Users
            </a>
            <a href="lawyers.php" class="nav-item">
                <span>⚖️</span> Lawyers
            </a>
            <a href="customers.php" class="nav-item">
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

    <!-- Main Content -->
    <div class="admin-main">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="header-actions">
                <span>Last login: <?php echo date('Y-m-d H:i:s'); ?></span>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon users">👥</div>
                <div class="stat-info">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon lawyers">⚖️</div>
                <div class="stat-info">
                    <h3><?php echo $total_lawyers; ?></h3>
                    <p>Total Lawyers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon customers">👤</div>
                <div class="stat-info">
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon appointments">📅</div>
                <div class="stat-info">
                    <h3><?php echo $total_appointments; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon pending">⏳</div>
                <div class="stat-info">
                    <h3><?php echo $pending_appointments; ?></h3>
                    <p>Pending Appointments</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="recent-activity">
            <h2>Recent Appointments</h2>
            <div class="activity-table">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Lawyer</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT a.*, c.username as customer_name, l.username as lawyer_name 
                                FROM appointments a
                                JOIN users c ON a.customer_id = c.id
                                JOIN users l ON a.lawyer_id = l.id
                                ORDER BY a.created_at DESC LIMIT 10";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>#'.$row['id'].'</td>';
                                echo '<td>'.$row['customer_name'].'</td>';
                                echo '<td>'.$row['lawyer_name'].'</td>';
                                echo '<td>'.$row['appointment_date'].' '.$row['appointment_time'].'</td>';
                                echo '<td><span class="status-'.$row['status'].'">'.$row['status'].'</span></td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5">No appointments found</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>