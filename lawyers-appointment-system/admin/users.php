<?php
// session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Delete user
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];
    $conn->query("DELETE FROM users WHERE id = $user_id");
    header("Location: users.php?success=User deleted successfully");
    exit();
}

$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-dashboard">
    <div class="admin-sidebar">
        <!-- Same sidebar as dashboard -->
        <div class="sidebar-header">
            <h2>All Users Management</h2>
            <p>Welcome, <?php echo $_SESSION['admin_name']; ?></p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <span>📊</span> Dashboard
            </a>
            <a href="users.php" class="nav-item active">
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

    <div class="admin-main">
        <div class="admin-header">
            <h1>All Users Management</h1>
            <div class="header-actions">
                <button onclick="exportUsers()" class="btn-secondary">Export CSV</button>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success"><?php echo $_GET['success']; ?></div>
        <?php endif; ?>

        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <span class="user-type <?php echo $user['user_type']; ?>">
                                <?php echo ucfirst($user['user_type']); ?>
                            </span>
                        </td>
                        <td><?php echo $user['created_at']; ?></td>
                        <td class="actions">
                            <?php if($user['user_type'] != 'admin'): ?>
                                <a href="?delete_user=<?php echo $user['id']; ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function exportUsers() {
        alert('Export feature will be implemented soon!');

    }
    

    // for download dada 
    function exportUsers() {
        // Create CSV content
        let csvContent = "data:text/csv/pdf/;charset=utf-5,";
        csvContent += "id,username,email,user_type,created_at\n";
        
        // Add appointment data
        <?php
        
        $user_data = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
        
        while($appt = $users_data->fetch_assoc()):
        ?>
        csvContent += "<?php echo $appt['id']; ?>,"
                    + "<?php echo $appt['user_name']; ?>,"
                    + "<?php echo $appt['email']; ?>,"
                    + "<?php echo $appt['user_type']; ?>,"
                    + "<?php echo $appt['created_at']; ?>";
        <?php endwhile; ?>
        
        // Create download link
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "users_data_<?php echo date('Y-m-d'); ?>.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    </script>
</body>
</html>