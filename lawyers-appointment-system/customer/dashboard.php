<?php
include '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h2>Lawyers-Management-System</h2>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Home</a>
                <a href="find-lawyers.php" class="nav-link">Find Lawyers</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="hero-section customer-hero">
        <div class="hero-content">
            <h1>Find Your Perfect Legal Match</h1>
            <p>Connect with experienced lawyers who understand your needs</p>
            <a href="find-lawyers.php" class="btn-primary">Find Lawyers Now</a>
        </div>
    </div>

    <div class="container">
        <h2>Featured Lawyers</h2>
        <div class="lawyers-grid">
            <?php
            $sql = "SELECT u.username, ld.* FROM lawyer_details ld 
                    JOIN users u ON ld.user_id = u.id 
                    LIMIT 9";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="lawyer-card">';
                    echo '<img src="../'.($row['image_path'] ?: 'assets/images/default-lawyer.jpg').'" alt="Lawyer Image">';
                    echo '<h3>'.$row['username'].'</h3>';
                    echo '<p class="specialization">'.$row['specialization'].'</p>';
                    echo '<p class="location">'.$row['location'].'</p>';
                    echo '<a href="lawyer-profile.php?id='.$row['user_id'].'" class="btn-view-profile">View Profile</a>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</body>
</html>

