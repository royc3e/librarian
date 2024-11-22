<?php
include 'db.php';
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch statistics from the database
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM Users")->fetch_assoc()['count'];
$totalBooks = $conn->query("SELECT COUNT(*) AS count FROM Books")->fetch_assoc()['count'];
$totalBorrowedBooks = $conn->query("SELECT COUNT(*) AS count FROM BorrowedBooks")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .stat {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>System Reports</h1>
    <div class="stat">
        <strong>Total Users:</strong> <?php echo $totalUsers; ?>
    </div>
    <div class="stat">
        <strong>Total Books:</strong> <?php echo $totalBooks; ?>
    </div>
    <div class="stat">
        <strong>Total Borrowed Books:</strong> <?php echo $totalBorrowedBooks; ?>
    </div>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
<?php
$conn->close();
?>
