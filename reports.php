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

    // Popular Books (borrowed more than once)
    $popularBooksSql = "SELECT B.Title, COUNT(T.TransactionID) AS BorrowCount
                        FROM Transactions T
                        JOIN Books B ON T.ResourceID = B.ResourceID
                        WHERE T.Status = 'Borrowed'
                        GROUP BY B.ResourceID
                        ORDER BY BorrowCount DESC
                        LIMIT 5"; // Top 5 popular books

    // Overdue Books and fines (with condition on the number of borrowed books)
    $overdueBooksSql = "
        SELECT T.TransactionID, U.Name AS UserName, B.Title AS BookTitle, 
            DATE_FORMAT(T.DueDate, '%W, %M %d, %Y') AS DueDate, 
            T.Fine,
            DATEDIFF(CURDATE(), T.DueDate) AS OverdueDays
        FROM Transactions T
        JOIN Users U ON T.UserID = U.UserID
        JOIN Books B ON T.ResourceID = B.ResourceID
        WHERE T.Status = 'Borrowed' 
        AND T.DueDate < CURDATE() 
        AND T.ReturnDate IS NULL
        AND (
            (U.UserType = 'student' AND 
                (SELECT COUNT(*) FROM Transactions T2 WHERE T2.UserID = U.UserID AND T2.Status = 'Borrowed') > 3)
            OR 
            (U.UserType = 'faculty' AND 
                (SELECT COUNT(*) FROM Transactions T2 WHERE T2.UserID = U.UserID AND T2.Status = 'Borrowed') > 5)
        )";


    // Inventory Summary (by category)
    $inventorySummarySql = "SELECT B.Genre, COUNT(*) AS TotalBooks, SUM(CASE WHEN B.AvailableQuantity > 0 THEN 1 ELSE 0 END) AS AvailableBooks
                            FROM Books B
                            GROUP BY B.Genre";

    // Execute queries
    $result = $conn->query($sql);
    $popularBooksResult = $conn->query($popularBooksSql);
    $overdueBooksResult = $conn->query($overdueBooksSql);
    $inventorySummaryResult = $conn->query($inventorySummarySql);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reports</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
            
            <!-- System Stats -->
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
                                            echo "Returned";  // If the book is returned, show "Returned"
                                        } else {
                                            echo $row['ReturnDate']; // Display "Not Returned Yet" if still not returned
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

            <!-- Popular Books -->
            <h2 class="text-center mt-5">Popular Books</h2>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Book Title</th>
                        <th>Times Borrowed</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($popularBooksResult->num_rows > 0): ?>
                        <?php while ($row = $popularBooksResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['Title']; ?></td>
                                <td><?php echo $row['BorrowCount']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2" class="text-center">No popular books found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Overdue Books -->
            <h2 class="text-center mt-5">Overdue Books</h2>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>User</th>
                        <th>Book Title</th>
                        <th>Due Date</th>
                        <th>Overdue Days</th>
                        <th>Fine</th>
                        <th>Alert</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($overdueBooksResult->num_rows > 0): ?>
                        <?php while ($row = $overdueBooksResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['TransactionID']; ?></td>
                                <td><?php echo $row['UserName']; ?></td>
                                <td><?php echo $row['BookTitle']; ?></td>
                                <td><?php echo date('l, F j, Y', strtotime($row['DueDate'])); ?></td>
                                <td><?php echo $row['OverdueDays']; ?> days</td>
                                <td><?php echo number_format($row['Fine'], 2); ?></td>
                                <td>
                                    <?php 
                                        // Provide an alert if the fine is particularly high or overdue days exceed a threshold
                                        if ($row['OverdueDays'] > 10) {
                                            echo '<span class="text-danger"><strong>Critical!</strong></span>';
                                        } elseif ($row['Fine'] > 20) {
                                            echo '<span class="text-warning"><strong>High Fine!</strong></span>';
                                        } else {
                                            echo 'Normal';
                                        }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No overdue books found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>


            <!-- Inventory Summary -->
            <h2 class="text-center mt-5">Inventory Summary</h2>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total Books</th>
                        <th>Available Books</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($inventorySummaryResult->num_rows > 0): ?>
                        <?php while ($row = $inventorySummaryResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['Genre']; ?></td>
                                <td><?php echo $row['TotalBooks']; ?></td>
                                <td><?php echo $row['AvailableBooks']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">No inventory data available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bootstrap JS and Popper.js -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    </body>
    </html>

    <?php
    $conn->close();
    ?>
