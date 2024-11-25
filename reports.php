<?php
session_start();
include 'db.php';
include 'navigation.php';

if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch statistics from the database
$totalUsers = $conn->query("SELECT COUNT(*) AS count FROM Users")->fetch_assoc()['count'];
$totalBooks = $conn->query("SELECT COUNT(*) AS count FROM Books")->fetch_assoc()['count'];
$totalBorrowedBooks = $conn->query("SELECT COUNT(*) AS count FROM Transactions WHERE Status = 'Borrowed'")->fetch_assoc()['count'];

// Fetch borrowed books details
$sql = "SELECT T.TransactionID, U.Name AS UserName, B.Title AS BookTitle, 
               DATE_FORMAT(T.BorrowDate, '%W, %M %d, %Y') AS BorrowDate, 
               DATE_FORMAT(T.DueDate, '%W, %M %d, %Y') AS DueDate, 
               IFNULL(DATE_FORMAT(T.ReturnDate, '%W, %M %d, %Y'), 'Not Returned Yet') AS ReturnDate, 
               T.Fine, T.Status
        FROM Transactions T
        JOIN Users U ON T.UserID = U.UserID
        JOIN Books B ON T.ResourceID = B.ResourceID
        WHERE T.Status = 'Borrowed'";


$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJv3o6f9gW9h3cbmMZ7KhhJ9pAq1Ao6rjT59SIQ6jYwWmt73Z3d4N0gmOgLJ" crossorigin="anonymous">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 40px;
        }

        .stat {
            display: flex;
            align-items: center;
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat i {
            font-size: 30px;
            color: #007BFF;
            margin-right: 20px;
        }

        .stat div {
            font-size: 20px;
            color: #333;
        }

        .stat strong {
            font-weight: bold;
            color: #007BFF;
        }

        .table th, .table td {
            text-align: center;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>System Reports</h1>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat">
                    <i class="fas fa-users"></i>
                    <div>
                        <strong>Total Users:</strong> <?php echo $totalUsers; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat">
                    <i class="fas fa-book"></i>
                    <div>
                        <strong>Total Books:</strong> <?php echo $totalBooks; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="stat">
                    <i class="fas fa-bookmark"></i>
                    <div>
                        <strong>Total Borrowed Books:</strong> <?php echo $totalBorrowedBooks; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrowed Books Table -->
        <h2 class="text-center">Borrowed Books</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Book Title</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Fine</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['TransactionID']; ?></td>
                            <td><?php echo $row['UserName']; ?></td>
                            <td><?php echo $row['BookTitle']; ?></td>
                            <td><?php echo date('l, F j, Y', strtotime($row['BorrowDate'])); ?></td>
                            <td><?php echo date('l, F j, Y', strtotime($row['DueDate'])); ?></td>
                            <td>
                                <?php 
                                    if ($row['ReturnDate'] != 'Not Returned Yet') {
                                        echo date('l, F j, Y', strtotime($row['ReturnDate']));
                                    } else {
                                        echo $row['ReturnDate']; // "Not Returned Yet"
                                    }
                                ?>
                            </td>
                            <td><?php echo number_format($row['Fine'], 2); ?></td>
                            <td><?php echo $row['Status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No borrowed books found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybP9pK1z14zXGoY8tPoPbzYboHphg8zVVYfVlFkw7XHxmVtXhK" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0VvvfWXkVSJfS3s+K1hB4wF/8tnWX5KnM1g3Zs6WcDGM15r2R" crossorigin="anonymous"></script>
</body>
</html>
<?php
$conn->close();
?>
