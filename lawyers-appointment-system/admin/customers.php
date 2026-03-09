<?php
// session_start();
include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Search functionality
$search = '';
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
}

// Delete customer
if (isset($_GET['delete_customer'])) {
    $customer_id = $_GET['delete_customer'];
    $conn->query("DELETE FROM users WHERE id = $customer_id AND user_type = 'customer'");
    header("Location: customers.php?success=Customer deleted successfully");
    exit();
}

// Get customers with search
$where_clause = "WHERE u.user_type = 'customer'";
if (!empty($search)) {
    $where_clause .= " AND (u.username LIKE '%$search%' OR u.email LIKE '%$search%')";
}

$customers = $conn->query("
    SELECT u.id, u.username, u.email, u.created_at,
           COUNT(a.id) as total_appointments,
           MAX(a.created_at) as last_appointment
    FROM users u 
    LEFT JOIN appointments a ON u.id = a.customer_id
    $where_clause
    GROUP BY u.id
    ORDER BY u.created_at DESC
");

$total_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'customer'")->fetch_assoc()['total'];
$active_customers = $conn->query("SELECT COUNT(DISTINCT customer_id) as total FROM appointments")->fetch_assoc()['total'];
$new_customers = $conn->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'customer' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-dashboard">

    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Customers Management</h2>
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

    <div class="admin-main">
        <div class="admin-header">
            <h1>Customers Management</h1>
            <div class="header-actions">
                <span>Total Customers: <?php echo $total_customers; ?></span>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success"><?php echo $_GET['success']; ?></div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>

        <!-- Customer Statistics -->
        <div class="customer-stats">
            <div class="stat-card">
                <div class="stat-icon customers">👤</div>
                <div class="stat-info">
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Total Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">✅</div>
                <div class="stat-info">
                    <h3><?php echo $active_customers; ?></h3>
                    <p>Active Customers</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon new">🆕</div>
                <div class="stat-info">
                    <h3><?php echo $new_customers; ?></h3>
                    <p>New This Week</p>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="search-box">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search customers by name or email..." 
                       value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn-primary">Search</button>
                <?php if(!empty($search)): ?>
                    <a href="customers.php" class="btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Customers Table -->
        <div class="customers-table">
            <div class="table-header">
                <h2>All Customers</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Appointments</th>
                        <th>Last Activity</th>
                        <th>Registration Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($customers->num_rows > 0): ?>
                        <?php while($customer = $customers->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $customer['id']; ?></strong></td>
                            <td>
                                <strong><?php echo htmlspecialchars($customer['username']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                            <td>
                                <?php if($customer['total_appointments'] > 0): ?>
                                    <span class="appointment-badge">
                                        <?php echo $customer['total_appointments']; ?> appointments
                                    </span>
                                <?php else: ?>
                                    <span class="no-appointments">No appointments</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($customer['last_appointment']): ?>
                                    <?php echo date('M j, Y', strtotime($customer['last_appointment'])); ?>
                                <?php else: ?>
                                    <span style="color: var(--text-light);">Never</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                            <td class="actions">
                                <button class="btn-view" onclick="toggleCustomerDetails(<?php echo $customer['id']; ?>)">
                                    📋 Details
                                </button>
                                <a href="?delete_customer=<?php echo $customer['id']; ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this customer? This will also delete their appointments.')">
                                    🗑️ Delete
                                </a>
                            </td>
                        </tr>
                        <tr id="customer-details-<?php echo $customer['id']; ?>" class="customer-details">
                            <td colspan="7">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                    <div>
                                        <h4>📋 Customer Information</h4>
                                        <p><strong>Username:</strong> <?php echo htmlspecialchars($customer['username']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
                                        <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($customer['created_at'])); ?></p>
                                        <p><strong>Total Appointments:</strong> <?php echo $customer['total_appointments']; ?></p>
                                    </div>
                                    <div>
                                        <h4>📅 Recent Appointments</h4>
                                        <?php
                                        $appointments = $conn->query("
                                            SELECT a.*, u.username as lawyer_name 
                                            FROM appointments a 
                                            JOIN users u ON a.lawyer_id = u.id 
                                            WHERE a.customer_id = {$customer['id']} 
                                            ORDER BY a.created_at DESC 
                                            LIMIT 3
                                        ");
                                        
                                        if($appointments->num_rows > 0):
                                            while($appt = $appointments->fetch_assoc()):
                                        ?>
                                            <div style="background: white; padding: 1rem; margin-bottom: 1rem; border-radius: 8px; border-left: 4px solid var(--primary-color);">
                                                <strong>Lawyer:</strong> <?php echo $appt['lawyer_name']; ?><br>
                                                <strong>Date:</strong> <?php echo $appt['appointment_date']; ?> <?php echo $appt['appointment_time']; ?><br>
                                                <strong>Status:</strong> 
                                                <span class="status-<?php echo $appt['status']; ?>">
                                                    <?php echo ucfirst($appt['status']); ?>
                                                </span>
                                            </div>
                                        <?php 
                                            endwhile;
                                        else:
                                        ?>
                                            <p style="color: var(--text-light);">No appointment history</p>
                                        <?php endif; ?>
                                        
                                        <?php if($customer['total_appointments'] > 3): ?>
                                            <a href="appointments.php?customer_id=<?php echo $customer['id']; ?>" 
                                               class="btn-view" style="margin-top: 1rem;">
                                                View All Appointments
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="icon">👤</div>
                                    <h3>No Customers Found</h3>
                                    <p>
                                        <?php if(!empty($search)): ?>
                                            No customers found matching "<?php echo htmlspecialchars($search); ?>"
                                        <?php else: ?>
                                            No customers have registered in the system yet.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Export Section -->
        <div style="text-align: center; margin-top: 2rem;">
            <button onclick="exportCustomers()" class="btn-primary">
                📊 Export Customers Data
            </button>
        </div>
    </div>

    <script>
    function toggleCustomerDetails(customerId) {
        const details = document.getElementById('customer-details-' + customerId);
        details.classList.toggle('show');
    }
    
    function exportCustomers() {
        alert('Export feature will download CSV file with all customers data.');
        // CSV export implementation here
    }
    
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-error');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
    </script>
</body>
</html>

