<?php
include '../config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'customer') {
    header("Location: ../index.php");
    exit();
}

$search_location = '';
$search_service = '';
$where_conditions = [];
$params = [];
$types = '';

// Handle search
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET['location'])) {
        $search_location = $_GET['location'];
        $where_conditions[] = "ld.location LIKE ?";
        $params[] = "%$search_location%";
        $types .= "s";
    }
    
    if (!empty($_GET['service'])) {
        $search_service = $_GET['service'];
        $where_conditions[] = "(ld.specialization LIKE ? OR ld.services LIKE ?)";
        $params[] = "%$search_service%";
        $params[] = "%$search_service%";
        $types .= "ss";
    }
}

// Build query
$sql = "SELECT u.id as user_id, u.username, ld.* FROM lawyer_details ld 
        JOIN users u ON ld.user_id = u.id";
        
if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY u.username";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Lawyers</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <h2>Find Lawyers</h2>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Home</a>
                <a href="find-lawyers.php" class="nav-link active">Find Lawyers</a>
                <a href="../logout.php" class="nav-link">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Find the Right Lawyer for You</h1>
        
        <!-- Search Form -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <div class="search-bar">
                    <input type="text" name="location" placeholder="Search by location..." value="<?php echo htmlspecialchars($search_location); ?>">
                    <input type="text" name="service" placeholder="Search by service/specialization..." value="<?php echo htmlspecialchars($search_service); ?>">
                    <button type="submit" class="btn-primary">Search</button>
                    <a href="find-lawyers.php" class="btn-secondary">Clear</a>
                </div>
            </form>
        </div>

        <!-- Lawyers List -->
        <div class="lawyers-section">
            <h2>Available Lawyers</h2>
            
            <?php if ($result->num_rows > 0): ?>
                <div class="lawyers-grid">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="lawyer-card">
                            <img src="../<?php echo $row['image_path'] ?: 'assets/images/default-lawyer.jpg'; ?>" alt="Lawyer Image">
                            <h3><?php echo htmlspecialchars($row['username']); ?></h3>
                            <p class="specialization"><?php echo htmlspecialchars($row['specialization']); ?></p>
                            <p class="location">📍 <?php echo htmlspecialchars($row['location']); ?></p>
                            <p class="experience">💼 <?php echo $row['experience']; ?> years experience</p>
                            <p class="hours">🕒 <?php echo htmlspecialchars($row['working_hours']); ?></p>
                            <a href="lawyer-profile.php?id=<?php echo $row['user_id']; ?>" class="btn-view-profile">View Profile & Book</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-lawyers">
                    <p>No lawyers found matching your criteria.</p>
                    <a href="find-lawyers.php" class="btn-primary">View All Lawyers</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <style>
    .search-section {
        background: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .search-form {
        width: 100%;
    }
    
    .search-bar {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .search-bar input {
        flex: 1;
        min-width: 200px;
    }
    
    .lawyer-card {
        background: white;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .lawyer-card:hover {
        transform: translateY(-5px);
    }
    
    .lawyer-card img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 1rem;
    }
    
    .no-lawyers {
        text-align: center;
        padding: 3rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .nav-link.active {
        background: #3498db;
        color: white;
    }
    </style>
</body>
</html>

