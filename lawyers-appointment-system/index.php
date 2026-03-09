<?php 


include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];

    $sql = "INSERT INTO users (username, email, password, user_type) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $user_type);
    
    if ($stmt->execute()) {


        echo "<script>alert('Register successfully')</script>";

    
        
    } else {
        echo "<script>alert('Register successfully')</script>";
        
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Appointment System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <h2>Lawyer Appointment System</h2>
            </div>
            <div class="nav-buttons">
                <button onclick="showRegister()" class="btn-primary">Register</button>
                <button onclick="showLogin()" class="btn-secondary">Login</button>
            </div>
        </div>
    </nav>
        <!-- <div class="hero-section">
            <h1>Find the Right Lawyer for Your Case</h1>
            <p>Connect with experienced legal professionals in your area</p>
        </div> -->


<!-- Updated Hero Section in index.php -->
<div class="hero-section">
    <div class="hero-content">
        <h1>Expert Legal Solutions at Your Fingertips</h1>
        <p>Connect with verified lawyers, book appointments instantly, and get the legal help you deserve</p>
        <div class="hero-buttons">
            <button onclick="showRegister()" class="btn-primary">Get Started</button>
            <button onclick="showLogin()" class="btn-secondary">Login</button>
        </div>
    </div>
</div>

<!-- Features Section Add karein -->
<div class="container">
    <div class="feature-grid">
        <div class="feature-card">
            <div class="feature-icon">⚖️</div>
            <h3>Verified Lawyers</h3>
            <p>All lawyers are verified professionals with proven expertise</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📅</div>
            <h3>Easy Booking</h3>
            <p>Book appointments instantly with your preferred lawyer</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure & Private</h3>
            <p>Your data and consultations are completely secure</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💼</div>
            <h3>Multiple Specializations</h3>
            <p>Find lawyers for all your legal needs</p>
        </div>
    </div>
</div>
    <div class="container">

        <!-- Featured Lawyers Section -->
        <div class="featured-lawyers">
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
                        echo '<img src="'.($row['image_path'] ?: 'assets/images/default-lawyer.jpg').'" alt="Lawyer Image">';
                        echo '<h3>'.$row['username'].'</h3>';
                        echo '<p class="specialization">'.$row['specialization'].'</p>';
                        echo '<p class="location">'.$row['location'].'</p>';
                        // echo '<button class="btn-view-profile">View Profile</button>';
                        echo '<button onclick="viewLawyerProfile('.$row['user_id'].')" class="btn-view-profile">View Profile</button>';
                        echo '</div>';
                        

                    }
                } else {
                    echo '<p>No lawyers available at the moment.</p>';
                }
                ?>
            </div>
        </div>
    </div>
<script>
    
function viewLawyerProfile(lawyerId) {
        // Always redirect to login page when not logged in
        showLogin();
        
        // Store the lawyer ID in sessionStorage to redirect after login
        sessionStorage.setItem('target_lawyer_id', lawyerId);
        
        // Show message to user
        alert('Please login as a customer to view lawyer profiles and book appointments.');
    }

</script>
    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>


    
    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Register</h2>
            <form action="" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="user_type" required>
                    <option value="">Select User Type</option>
                    <option value="lawyer">Lawyer</option>
                    <option value="customer">Customer</option>
                </select>
                <button type="submit" class="btn-primary">Register</button>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

