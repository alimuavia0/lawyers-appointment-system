<?php
include '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: find-lawyers.php");
    exit();
}

$lawyer_id = $_GET['id'];
$customer_id = $_SESSION['user_id'];

// Fetch lawyer details
$sql = "SELECT u.username, u.email, ld.* FROM lawyer_details ld 
        JOIN users u ON ld.user_id = u.id 
        WHERE ld.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lawyer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: find-lawyers.php?error=Lawyer not found");
    exit();
}

$lawyer = $result->fetch_assoc();

// Handle appointment booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $case_details = $_POST['case_details'];
    
    // Check if appointment already exists
    $check_sql = "SELECT id FROM appointments WHERE lawyer_id = ? AND customer_id = ? AND appointment_date = ? AND appointment_time = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iiss", $lawyer_id, $customer_id, $appointment_date, $appointment_time);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $error = "You already have an appointment at this date and time.";
    } else {
        // Insert new appointment
        $insert_sql = "INSERT INTO appointments (customer_id, lawyer_id, appointment_date, appointment_time, case_details, status) 
                      VALUES (?, ?, ?, ?, ?, 'pending')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("iisss", $customer_id, $lawyer_id, $appointment_date, $appointment_time, $case_details);
        
        if ($insert_stmt->execute()) {
            $success = "Appointment booked successfully! The lawyer will confirm your appointment soon.";
        } else {
            $error = "Failed to book appointment. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($lawyer['username']); ?> - Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h2>Lawyer Profile</h2>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Home</a>
                <a href="find-lawyers.php" class="nav-link">Find Lawyers</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="profile-layout">
            <!-- Lawyer Details Section -->
            <div class="lawyer-details">
                <div class="profile-header">
                    <img src="../<?php echo $lawyer['image_path'] ?: 'assets/images/default-lawyer.jpg'; ?>" alt="Lawyer Image" class="profile-image">
                    <div class="profile-info">
                        <h1><?php echo htmlspecialchars($lawyer['username']); ?></h1>
                        <p class="specialization"><?php echo htmlspecialchars($lawyer['specialization']); ?></p>
                        <p class="location">📍 <?php echo htmlspecialchars($lawyer['location']); ?></p>
                    </div>
                </div>

                <div class="profile-sections">
                    <div class="profile-section">
                        <h3>📋 Professional Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Experience:</strong>
                                <span><?php echo $lawyer['experience']; ?> years</span>
                            </div>
                            <div class="info-item">
                                <strong>Working Hours:</strong>
                                <span><?php echo htmlspecialchars($lawyer['working_hours']); ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Email:</strong>
                                <span><?php echo htmlspecialchars($lawyer['email']); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h3>📝 About</h3>
                        <p><?php echo nl2br(htmlspecialchars($lawyer['description'])); ?></p>
                    </div>

                    <div class="profile-section">
                        <h3>⚖️ Services Offered</h3>
                        <p><?php echo nl2br(htmlspecialchars($lawyer['services'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Appointment Booking Section -->
            <div class="appointment-booking">
                <div class="booking-form">
                    <h2>Book Appointment</h2>
                    <form method="POST">
                        <div class="form-group">
                            <label for="appointment_date">Appointment Date:</label>
                            <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_time">Appointment Time:</label>
                            <input type="time" id="appointment_time" name="appointment_time" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="case_details">Case Details:</label>
                            <textarea id="case_details" name="case_details" rows="6" placeholder="Describe your legal issue..." required></textarea>
                        </div>
                        
                        <button type="submit" name="book_appointment" class="btn-primary btn-book">Book Appointment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
    .profile-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        align-items: start;
    }
    
    .lawyer-details {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .profile-header {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #eee;
    }
    
    .profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .profile-info h1 {
        margin: 0 0 0.5rem 0;
        color: #2c3e50;
    }
    
    .specialization {
        font-size: 1.2rem;
        color: #3498db;
        font-weight: bold;
        margin: 0 0 0.5rem 0;
    }
    
    .location {
        color: #7f8c8d;
        margin: 0;
    }
    
    .profile-section {
        margin-bottom: 2rem;
    }
    
    .profile-section h3 {
        color: #2c3e50;
        margin-bottom: 1rem;
        border-bottom: 2px solid #3498db;
        padding-bottom: 0.5rem;
    }
    
    .info-grid {
        display: grid;
        gap: 1rem;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .appointment-booking {
        position: sticky;
        top: 2rem;
    }
    
    .booking-form {
        background: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .booking-form h2 {
        text-align: center;
        margin-bottom: 2rem;
        color: #2c3e50;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
        color: #2c3e50;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }
    
    .btn-book {
        width: 100%;
        padding: 1rem;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .profile-layout {
            grid-template-columns: 1fr;
        }
        
        .profile-header {
            flex-direction: column;
            text-align: center;
        }
    }
    </style>
</body>
</html>

