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

    
        exit();
    } else {
        header("Location: index.php?error=Registration failed");
        exit();
    }
}
?>

