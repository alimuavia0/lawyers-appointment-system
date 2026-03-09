<?php
include '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'lawyer') {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $specialization = $_POST['specialization'];
    $location = $_POST['location'];
    $experience = $_POST['experience'];
    $working_hours = $_POST['working_hours'];
    $description = $_POST['description'];
    $services = $_POST['services'];
    
    // Image upload handling
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "assets/images/" . $image_name;
            }
        }
    }

    // Check if profile already exists
    $check_sql = "SELECT id FROM lawyer_details WHERE user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing profile
        if ($image_path) {
            $sql = "UPDATE lawyer_details SET specialization=?, location=?, experience=?, working_hours=?, description=?, services=?, image_path=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssissssi", $specialization, $location, $experience, $working_hours, $description, $services, $image_path, $user_id);
        } else {
            $sql = "UPDATE lawyer_details SET specialization=?, location=?, experience=?, working_hours=?, description=?, services=? WHERE user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisssi", $specialization, $location, $experience, $working_hours, $description, $services, $user_id);
        }
    } else {
        // Insert new profile
        $sql = "INSERT INTO lawyer_details (user_id, specialization, location, experience, working_hours, description, services, image_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssisss", $user_id, $specialization, $location, $experience, $working_hours, $description, $services, $image_path);
    }
    
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=Profile updated successfully");
    } else {
        header("Location: dashboard.php?error=Profile update failed");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>
    
            <?php
            $sql = "SELECT * FROM lawyer_details WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $profile = $result->fetch_assoc(); 
            }
            ?>
        </div>
    </div>

    <!-- new  -->
    <nav class="navbar">
        <div class="nav-container">
            <h2>Lawyer Profile</h2>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="appointments.php" class="nav-link">Appointments</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        
        <div class="profile-layout">
            <!-- Lawyer Details Section -->
            <div class="lawyer-details">
                <div class="profile-header">
                    <img src="../<?php echo $profile['image_path'] ?: 'assets/images/default-lawyer.jpg'; ?>" alt="Lawyer Image" class="profile-image">
                    <div class="profile-info">
                        <h1><?php 
                        // echo htmlspecialchars($lawyer['username']); 
                        ?></h1>
                        <p class="specialization"><?php echo htmlspecialchars($profile['specialization']); ?></p>
                        <p class="location">📍 <?php echo htmlspecialchars($profile['location']); ?></p>
                    </div>
                </div>

                <div class="profile-sections">
                    <div class="profile-section">
                        <h3>📋 Professional Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <strong>Experience:</strong>
                                <span><?php echo $profile['experience']; ?> years</span>
                            </div>
                            <div class="info-item">
                                <strong>Working Hours:</strong>
                                <span><?php echo htmlspecialchars($profile['working_hours']); ?></span>
                            </div>
                            <div class="info-item">
                                <!-- <strong>Email:</strong> -->
                                <span><?php 
                                // echo htmlspecialchars($profile['email']); 
                                ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section">
                        <h3>📝 About</h3>
                        <p><?php echo nl2br(htmlspecialchars($profile['description'])); ?></p>
                    </div>

                    <div class="profile-section">
                        <h3>⚖️ Services Offered</h3>
                        <p><?php echo nl2br(htmlspecialchars($profile['services'])); ?></p>
                    </div>
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