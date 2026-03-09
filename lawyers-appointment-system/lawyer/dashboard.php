<?php
include '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'lawyer') {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        
:root {
    --primary-color: #2563eb;
    --primary-dark: #1d4ed8;
    --primary-light: #dbeafe;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --warning-color: #f59e0b;
    --danger-color: #ef4444;
    --text-dark: #1e293b;
    --text-light: #64748b;
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --border-color: #e2e8f0;
    --sidebar-bg: #1e293b;
    --sidebar-text: #f1f5f9;
    --sidebar-hover: #334155;
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --border-radius: 12px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
/* Modern Sidebar */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    color: var(--text-dark);
    background-color: var(--bg-light);
    overflow-x: hidden;
}
/* Admin Dashboard Layout */
.admin-dashboard {
    display: flex;
    min-height: 100vh;
    background: var(--bg-light);
}

.admin-sidebar {
    width: 250px;
    background: var(--sidebar-bg);
    color: var(--sidebar-text);
    position: fixed;
    height: 100vh;
    transition: var(--transition);
    z-index: 1000;
    box-shadow: var(--shadow-lg);
}

.sidebar-header {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid #334155;
    background: linear-gradient(135deg, #1e293b, #0f172a);
}

.sidebar-header h2 {
    margin-bottom: 0.5rem;
    font-size: 1.4rem;
    font-weight: 700;
    color: white;
}

.sidebar-header p {
    color: #94a3b8;
    font-size: 0.9rem;
}

.sidebar-nav {
    padding: 1.5rem 0;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: #cbd5e1;
    text-decoration: none;
    transition: var(--transition);
    border-left: 4px solid transparent;
    font-weight: 500;
}

.nav-item:hover {
    background: var(--sidebar-hover);
    color: white;
    border-left-color: var(--primary-color);
    padding-left: 2rem;
}

.nav-item.active {
    background: var(--sidebar-hover);
    color: white;
    border-left-color: var(--primary-color);
    position: relative;
}

.nav-item.active::before {
    content: '';
    position: absolute;
    right: -4px;
    top: 50%;
    transform: translateY(-50%);
    width: 8px;
    height: 8px;
    background: var(--primary-color);
    border-radius: 50%;
}

.nav-item span {
    font-size: 1.2rem;
    width: 24px;
    text-align: center;
}

.nav-item.logout {
    color: #fca5a5;
    margin-top: 2rem;
    border-top: 1px solid #334155;
    padding-top: 1.5rem;
}

.nav-item.logout:hover {
    background: #7f1d1d;
    color: #fecaca;
}

form{
    /* display: grid; */
    width: 100%;

}
.profile-form{
    /* display: grid; */
    width: 100%;
    margin: 1rem auto;
    background: var(--bg-white);
    padding: 2.5rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
    /* margin-bottom: 2rem; */
    border: 1px solid var(--border-color);
}
    </style>
</head>
<body class="admin-dashboard">
    


    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="sidebar-header">
            <h2>Lawyer Dashboard</h2>
            <p>Welcome, <?php echo $_SESSION['username']; ?></p>
            
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <span>📊</span> Dashboard
            </a>
            
            <a href="profile.php" class="nav-item">
                <span>👤</span> View Profile
            </a>
            <a href="appointments.php" class="nav-item">
                <span>📅</span> Appointments
            </a>
            <a href="../logout.php" class="nav-item logout">
                <span>🚪</span> Logout
            </a>
        </nav>
    </div>


    <div class="container" style="margin-top: 0;padding-top: 0;">
       
        
    
        <div class="profile-form" style="grid-template-columns: auto 2fr;">
            <h2>Update Your Profile</h2>
            <form action="profile.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="specialization" placeholder="Specialization" required>
                <input type="text" name="location" placeholder="Location" required>
                <input type="number" name="experience" placeholder="Years of Experience" required>
                <input type="text" name="working_hours" placeholder="Working Hours (e.g., 9 AM - 5 PM)" required>
                <textarea name="description" placeholder="Professional Description" required></textarea>
                <textarea name="services" placeholder="Services Offered" required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit" class="btn-primary">Update Profile</button>
            </form>
        </div>

          
    </div>
</body>
</html>

