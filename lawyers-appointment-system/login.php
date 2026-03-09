<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['username'] = $user['username'];
            
            if ($user['user_type'] == 'lawyer') {
                header("Location: lawyer/dashboard.php");
            } else {
                header("Location: customer/dashboard.php");
            }
            exit();
        }
    }
    
    header("Location: index.php?error=Invalid credentials");
    exit();
}
?>

