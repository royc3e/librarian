<?php
session_start();

include 'db.php';
include 'navigation.php';


// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Handle return book submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transactionID = $_POST['transaction_id'] ?? null;
    $resourceID = $_POST['resource_id'] ?? null;

    // Validate inputs
    if (!$transactionID || !$resourceID) {
        echo "<div class='error-message'>Error: Missing transaction or resource ID.</div>";
    } else {
        $returnDate = date("Y-m-d");

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update transaction status to "Returned"
            $stmt = $conn->prepare(
                "UPDATE Transactions 
                 SET ReturnDate = ?, Status = 'Returned' 
                 WHERE TransactionID = ? AND Status = 'Borrowed'"
            );
            $stmt->bind_param('si', $returnDate, $transactionID);

            if (!$stmt->execute()) {
                throw new Exception("Error updating transaction: " . $stmt->error);
            }

            // Update book availability
            $stmt = $conn->prepare(
                "UPDATE Books 
                 SET AvailableQuantity = AvailableQuantity + 1 
                 WHERE ResourceID = ?"
            );
            $stmt->bind_param('i', $resourceID);

            if (!$stmt->execute()) {
                throw new Exception("Error updating book availability: " . $stmt->error);
            }

            $conn->commit();
            echo "<div class='success-message'>Book returned successfully!</div>";
        } catch (Exception $e) {
            $conn->rollback();
            echo "<div class='error-message'>Error: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return a Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #4CAF50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .no-results {
            text-align: center;
            color: #888;
        }

        .success-message, .error-message {
            font-weight: bold;
            text-align: center;
            padding: 10px;
            margin: 10px 0;
        }

        .success-message {
            color: green;
        }

        .error-message {
            color: red;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Return a Book</h2>

        <form method="POST">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $userID = $_SESSION['UserID'];
                    $sql = "SELECT T.TransactionID, T.ResourceID, B.Title, T.DueDate 
                            FROM Transactions T 
                            JOIN Books B ON T.ResourceID = B.ResourceID 
                            WHERE T.UserID = $userID AND T.Status = 'Borrowed'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Format the DueDate to show the full day, month, day of the month, and year
                            $formattedDueDate = date("l, F j, Y", strtotime($row['DueDate'])); // Full day (e.g., "Monday, November 5, 2024")
                            
                            echo "<tr>
                                    <td>{$row['Title']}</td>
                                    <td>{$formattedDueDate}</td> <!-- Showing formatted DueDate -->
                                    <td>
                                        <input type='hidden' name='transaction_id' value='{$row['TransactionID']}'>
                                        <input type='hidden' name='resource_id' value='{$row['ResourceID']}'>
                                        <button type='submit' class='btn btn-primary'>Return</button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='no-results'>No books to return.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </form>
    </div>

</body>
</html>