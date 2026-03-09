<?php
    // session_start();
    include '../config.php';
    
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit();
    }
    
    // Search and filter functionality
    $search = '';
    $status_filter = '';
    $date_filter = '';
    
    $where_conditions = [];
    $query_params = [];
    
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $conn->real_escape_string($_GET['search']);
        $where_conditions[] = "(c.username LIKE '%$search%' OR l.username LIKE '%$search%' OR a.case_details LIKE '%$search%')";
    }
    
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $status_filter = $conn->real_escape_string($_GET['status']);
        $where_conditions[] = "a.status = '$status_filter'";
    }
    
    if (isset($_GET['date']) && !empty($_GET['date'])) {
        $date_filter = $conn->real_escape_string($_GET['date']);
        $where_conditions[] = "a.appointment_date = '$date_filter'";
    }
    
    // Build WHERE clause
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }
    
    // Update appointment status
    if (isset($_POST['update_status'])) {
        $appointment_id = $_POST['appointment_id'];
        $new_status = $_POST['status'];
        
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $appointment_id);
        
        if ($stmt->execute()) {
            header("Location: appointments.php?success=Appointment status updated successfully");
            exit();
        } else {
            header("Location: appointments.php?error=Failed to update appointment status");
            exit();
        }
    }
    
    // Delete appointment
    if (isset($_GET['delete_appointment'])) {
        $appointment_id = $_GET['delete_appointment'];
        $conn->query("DELETE FROM appointments WHERE id = $appointment_id");
        header("Location: appointments.php?success=Appointment deleted successfully");
        exit();
    }
    
    // Get appointments with filters
    $appointments_query = "
        SELECT a.*, 
               c.username as customer_name, 
               c.email as customer_email,
               l.username as lawyer_name,
               l.email as lawyer_email,
               ld.specialization as lawyer_specialization
        FROM appointments a
        JOIN users c ON a.customer_id = c.id
        JOIN users l ON a.lawyer_id = l.id
        LEFT JOIN lawyer_details ld ON l.id = ld.user_id
        $where_clause
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";
    
    $appointments = $conn->query($appointments_query);
    
    // Get statistics
    $total_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments")->fetch_assoc()['total'];
    $pending_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'pending'")->fetch_assoc()['total'];
    $confirmed_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'confirmed'")->fetch_assoc()['total'];
    $cancelled_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status = 'cancelled'")->fetch_assoc()['total'];
    $today_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE appointment_date = CURDATE()")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management - Admin</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
        }
        
        .appointment-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .status-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .status-select {
            padding: 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.8rem;
            background: white;
        }
        
        .case-details {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .case-details.expanded {
            white-space: normal;
            text-overflow: unset;
        }
        
        .upcoming-badge {
            background: #fffbeb;
            color: #d97706;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid #fcd34d;
        }
        
        .past-badge {
            background: #f3f4f6;
            color: #6b7280;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid #d1d5db;
        }
        
        .today-badge {
            background: #d1fae5;
            color: #065f46;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid #a7f3d0;
        }
    </style>
</head>
<body class="admin-dashboard">
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Appointment-Management</h2>
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
            <a href="customers.php" class="nav-item">
                <span>👤</span> Customers
            </a>
            <a href="appointments.php" class="nav-item active">
                <span>📅</span> Appointments
            </a>
            <a href="../logout.php" class="nav-item logout">
                <span>🚪</span> Logout
            </a>
        </nav>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1>Appointments Management</h1>
            <div class="header-actions">
                <span>Total Appointments: <?php echo $total_appointments; ?></span>
            </div>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert-success"><?php echo $_GET['success']; ?></div>
        <?php endif; ?>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="alert-error"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>

        <!-- Appointment Statistics -->
        <div class="stats-grid">
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
                    <p>Pending</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon active">✅</div>
                <div class="stat-info">
                    <h3><?php echo $confirmed_appointments; ?></h3>
                    <p>Confirmed</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon today">📌</div>
                <div class="stat-info">
                    <h3><?php echo $today_appointments; ?></h3>
                    <p>Today's</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon cancelled">❌</div>
                <div class="stat-info">
                    <h3><?php echo $cancelled_appointments; ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="search-box">
            <form method="GET" class="search-form">
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; width: 100%; align-items: end;">
                    <div class="filter-group">
                        <label>Search Appointments</label>
                        <input type="text" name="search" placeholder="Search by customer, lawyer, or case details..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    
                    <div class="filter-group">
                        <label>Status</label>
                        <select name="status" class="status-select">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label>Date</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                    </div>
                    
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn-primary">Apply Filters</button>
                        <a href="appointments.php" class="btn-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Appointments Table -->
        <div class="users-table">
            <div class="table-header">
                <h2>All Appointments</h2>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <span style="color: var(--text-light); font-size: 0.9rem;">
                        Showing <?php echo $appointments->num_rows; ?> appointments
                    </span>
                    <button onclick="exportAppointments()" class="btn-secondary" style="padding: 0.5rem 1rem;">
                        📊 Export
                    </button>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Lawyer</th>
                        <th>Date & Time</th>
                        <th>Case Details</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($appointments->num_rows > 0): ?>
                        <?php while($appointment = $appointments->fetch_assoc()): 
                            $appointment_date = new DateTime($appointment['appointment_date']);
                            $today = new DateTime();
                            $is_today = $appointment_date->format('Y-m-d') == $today->format('Y-m-d');
                            $is_upcoming = $appointment_date > $today;
                            $is_past = $appointment_date < $today;
                        ?>
                        <tr>
                            <td><strong>#<?php echo $appointment['id']; ?></strong></td>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($appointment['customer_name']); ?></strong>
                                    <div style="font-size: 0.8rem; color: var(--text-light);">
                                        <?php echo htmlspecialchars($appointment['customer_email']); ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong><?php echo htmlspecialchars($appointment['lawyer_name']); ?></strong>
                                    <div style="font-size: 0.8rem; color: var(--text-light);">
                                        <?php echo $appointment['lawyer_specialization'] ?: 'No specialization'; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong><?php echo date('M j, Y', strtotime($appointment['appointment_date'])); ?></strong>
                                    <div style="font-size: 0.8rem; color: var(--text-light);">
                                        <?php echo date('h:i A', strtotime($appointment['appointment_time'])); ?>
                                    </div>
                                    <div style="margin-top: 0.25rem;">
                                        <?php if($is_today): ?>
                                            <span class="today-badge">Today</span>
                                        <?php elseif($is_upcoming): ?>
                                            <span class="upcoming-badge">Upcoming</span>
                                        <?php else: ?>
                                            <span class="past-badge">Past</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="case-details" id="case-details-<?php echo $appointment['id']; ?>">
                                    <?php echo htmlspecialchars($appointment['case_details'] ?: 'No details provided'); ?>
                                </div>
                                <?php if(strlen($appointment['case_details']) > 50): ?>
                                    <button onclick="toggleCaseDetails(<?php echo $appointment['id']; ?>)" 
                                            class="btn-view" 
                                            style="padding: 0.25rem 0.5rem; font-size: 0.7rem; margin-top: 0.25rem;">
                                        View More
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                    <select name="status" class="status-select" onchange="this.form.submit()">
                                        <option value="pending" <?php echo $appointment['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $appointment['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $appointment['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>
                            </td>
                            <td>
                                <?php echo date('M j, Y', strtotime($appointment['created_at'])); ?>
                                <div style="font-size: 0.8rem; color: var(--text-light);">
                                    <?php echo date('h:i A', strtotime($appointment['created_at'])); ?>
                                </div>
                            </td>
                            <td class="actions">
                                <button class="btn-view" onclick="viewAppointmentDetails(<?php echo $appointment['id']; ?>)">
                                    👁️ View
                                </button>
                                <a href="?delete_appointment=<?php echo $appointment['id']; ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this appointment? This action cannot be undone.')">
                                    🗑️ Delete
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <div class="icon">📅</div>
                                    <h3>No Appointments Found</h3>
                                    <p>
                                        <?php if(!empty($search) || !empty($status_filter) || !empty($date_filter)): ?>
                                            No appointments match your current filters.
                                        <?php else: ?>
                                            No appointments have been scheduled yet.
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Appointment Details Modal -->
        <div id="appointmentModal" class="modal" style="display: none;">
            <div class="modal-content" style="max-width: 600px;">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2>Appointment Details</h2>
                <div id="modalContent" style="padding: 1rem 0;">
                    <!-- Content will be loaded via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleCaseDetails(appointmentId) {
        const details = document.getElementById('case-details-' + appointmentId);
        details.classList.toggle('expanded');
        
        const button = details.nextElementSibling;
        if (details.classList.contains('expanded')) {
            button.textContent = 'Show Less';
        } else {
            button.textContent = 'View More';
        }
    }
    
    function viewAppointmentDetails(appointmentId) {
        // In a real application, you would fetch this data via AJAX
        // For now, we'll show a simple alert with the ID
        alert('Viewing details for appointment #' + appointmentId + '\n\nIn a complete implementation, this would show:\n- Full case details\n- Customer contact info\n- Lawyer details\n- Appointment history\n- Status timeline');
        
        // Example of what AJAX implementation would look like:
        
        fetch('get_appointment_details.php?id=' + appointmentId)
            .then(response => response.json())
            .then(data => {
                document.getElementById('modalContent').innerHTML = `
                    <div style="display: grid; gap: 1rem;">
                        <div><strong>Appointment ID:</strong> #${data.id}</div>
                        <div><strong>Customer:</strong> ${data.customer_name} (${data.customer_email})</div>
                        <div><strong>Lawyer:</strong> ${data.lawyer_name} - ${data.lawyer_specialization}</div>
                        <div><strong>Date & Time:</strong> ${data.appointment_date} ${data.appointment_time}</div>
                        <div><strong>Status:</strong> ${data.status}</div>
                        <div><strong>Case Details:</strong> ${data.case_details}</div>
                        <div><strong>Created:</strong> ${data.created_at}</div>
                    </div>
                `;
                document.getElementById('appointmentModal').style.display = 'block';
            });
        
    }
    
    function closeModal() {
        document.getElementById('appointmentModal').style.display = 'none';
    }
    
    function exportAppointments() {
        // Create CSV content
        let csvContent = "data:text/csv/pdf/;charset=utf-8,";
        csvContent += "ID,Customer,Lawyer,Date,Time,Status,Case Details,Created Date\n";
        
        // Add appointment data
        <?php
        $export_appointments = $conn->query("
            SELECT a.*, c.username as customer_name, l.username as lawyer_name
            FROM appointments a
            JOIN users c ON a.customer_id = c.id
            JOIN users l ON a.lawyer_id = l.id
            ORDER BY a.appointment_date DESC
        ");
        
        while($appt = $export_appointments->fetch_assoc()):
        ?>
        csvContent += "<?php echo $appt['id']; ?>,"
                    + "<?php echo $appt['customer_name']; ?>,"
                    + "<?php echo $appt['lawyer_name']; ?>,"
                    + "<?php echo $appt['appointment_date']; ?>,"
                    + "<?php echo $appt['appointment_time']; ?>,"
                    + "<?php echo $appt['status']; ?>,"
                    + "\"<?php echo addslashes($appt['case_details']); ?>\","
                    + "<?php echo $appt['created_at']; ?>\n";
        <?php endwhile; ?>
        
        // Create download link
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "appointments_data_<?php echo date('Y-m-d'); ?>.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('appointmentModal');
        if (event.target == modal) {
            closeModal();
        }
    }
    
    // Auto-close alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success, .alert-error');
        alerts.forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
    
    // Add some sample data for demonstration
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Appointments management loaded');
        // You can add more interactive features here
    });
    </script>
</body>
</html>

