<?php
include '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'lawyer') {
    header("Location: ../index.php");
    exit();
}

$lawyer_id = $_SESSION['user_id'];

// Handle appointment status update
if (isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE appointments SET status = ? WHERE id = ? AND lawyer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $status, $appointment_id, $lawyer_id);
    $stmt->execute();
}

// Fetch appointments
$sql = "SELECT a.*, u.username as customer_name, u.email as customer_email 
        FROM appointments a 
        JOIN users u ON a.customer_id = u.id 
        WHERE a.lawyer_id = ? 
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h2>My Appointments</h2>
            <div class="nav-buttons">
                <a href="dashboard.php" class="btn-secondary">Back to Dashboard</a>
                <a href="../logout.php" class="btn-primary">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Appointment Management</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo $_GET['success']; ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error"><?php echo $_GET['error']; ?></div>
        <?php endif; ?>

        <div class="appointments-list">
            <h2>All Appointments</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="appointments-grid">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="appointment-card status-<?php echo $row['status']; ?>">
                            <div class="appointment-header">
                                <h3>Appointment #<?php echo $row['id']; ?></h3>
                                <span class="status-badge <?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </div>
                            
                            <div class="appointment-details">
                                <p><strong>Customer:</strong> <?php echo $row['customer_name']; ?></p>
                                <p><strong>Email:</strong> <?php echo $row['customer_email']; ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($row['appointment_date'])); ?></p>
                                <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($row['appointment_time'])); ?></p>
                                <p><strong>Case Details:</strong></p>
                                <div class="case-details"><?php echo nl2br($row['case_details']); ?></div>
                            </div>
                            
                            <div class="appointment-actions">
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="appointment_id" value="<?php echo $row['id']; ?>">
                                    <select name="status" class="status-select">
                                        <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-primary">Update</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-appointments">
                    <p>No appointments found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .appointments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }
    
    .appointment-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #95a5a6;
    }
    
    .appointment-card.status-pending {
        border-left-color: #f39c12;
    }
    
    .appointment-card.status-confirmed {
        border-left-color: #27ae60;
    }
    
    .appointment-card.status-cancelled {
        border-left-color: #e74c3c;
    }
    
    .appointment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    
    .status-badge.pending {
        background: #f39c12;
        color: white;
    }
    
    .status-badge.confirmed {
        background: #27ae60;
        color: white;
    }
    
    .status-badge.cancelled {
        background: #e74c3c;
        color: white;
    }
    
    .case-details {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 4px;
        margin-top: 0.5rem;
    }
    
    .status-form {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    
    .status-select {
        flex: 1;
    }
    
    .alert {
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    </style>
</body>
</html>

