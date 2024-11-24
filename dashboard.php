<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Dynamic greeting based on user type
$userName = htmlspecialchars($_SESSION['Name']);
$userType = htmlspecialchars($_SESSION['UserType']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ucfirst($userType); ?> Dashboard</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            background-color: #343a40;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            align-items: start;
        }
        .sidebar h2 {
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1em;
            margin: 10px 0;
            padding: 10px;
            width: 100%;
            text-align: left;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .logout-btn {
            margin-top: auto;
            text-decoration: none;
            color: #ffffff;
            background-color: #dc3545;
            padding: 10px 15px;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        .content {
            margin-left: 270px; /* Sidebar width + margin */
            padding: 20px;
        }
        .welcome-message {
            font-size: 1.2em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><?php echo ucfirst($userType); ?> Dashboard</h2>
        <p>Welcome, <strong><?php echo $userName; ?></strong></p>
        <p>User Type: <strong><?php echo ucfirst($userType); ?></strong></p>
        <?php
        // Dynamically generate sidebar links based on user type
        if ($userType == 'admin') {
            echo '<a href="user_management.php">Manage Users</a>';
            echo '<a href="reports.php">Generate Reports</a>';
        } elseif ($userType == 'student') {
            echo '<a href="view_books.php">View Books</a>';
            echo '<a href="borrow_book.php">Borrow Book</a>';
            echo '<a href="my_borrowed_books.php">My Borrowed Books</a>';
        } elseif ($userType == 'staff') {
            echo '<a href="add_book.php">Add Book</a>';
            echo '<a href="manage_books.php">Manage Books</a>';
            echo '<a href="issue_book.php">Issue Book</a>';
        } elseif ($userType == 'faculty') {
            echo '<a href="recommend_books.php">Recommend Books</a>';
            echo '<a href="borrow_book.php">Borrow Book</a>';
            echo '<a href="my_borrowed_books.php">My Borrowed Books</a>';
        } else {
            echo '<p>Invalid User Type</p>';
        }
        ?>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
    <div class="content">
        <h2>Dashboard</h2>
        <p class="welcome-message">
            Welcome to the <?php echo ucfirst($userType); ?> Dashboard. Use the sidebar to navigate.
        </p>
        <!-- Add additional dashboard content here -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo ucfirst($userType); ?> Overview</h5>
                <p class="card-text">
                    <?php
                    // Display a message based on user type
                    if ($userType == 'admin') {
                        echo 'Manage users, system settings, and view reports.';
                    } elseif ($userType == 'student') {
                        echo 'Browse available books and manage your borrowed items.';
                    } elseif ($userType == 'staff') {
                        echo 'Add and manage books, and assist with book issuance.';
                    } elseif ($userType == 'faculty') {
                        echo 'Recommend books and manage your borrowing activities.';
                    }
                    ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS for optional interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
