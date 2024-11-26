<?php
session_start();
include 'db.php';
include 'navigation.php';

// Ensure that the user is a staff member (admin)
if (!isset($_SESSION['UserID']) || $_SESSION['UserType'] != 'staff') {
    header('Location: login.php');
    exit();
}

// Handle the approval of a pending borrow request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transactionID = $_POST['transaction_id'] ?? null;
    $resourceID = $_POST['resource_id'] ?? null;
    
    // Validate the inputs
    if (!$transactionID || !$resourceID) {
        echo "<div class='error-message'>Error: Missing transaction or resource ID.</div>";
    } else {
        // Set the status to 'Borrowed' when staff approves the request
        $status = 'Borrowed';
        $approvalDate = date("Y-m-d");

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Update the transaction status to 'Borrowed'
            $stmt = $conn->prepare(
                "UPDATE Transactions 
                 SET Status = ?, ApprovalDate = ? 
                 WHERE TransactionID = ? AND Status = 'Pending'"
            );
            $stmt->bind_param('ssi', $status, $approvalDate, $transactionID);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating transaction: " . $stmt->error);
            }

            // Update book availability (decrease by 1)
            $stmt = $conn->prepare(
                "UPDATE Books 
                 SET AvailableQuantity = AvailableQuantity - 1 
                 WHERE ResourceID = ?"
            );
            $stmt->bind_param('i', $resourceID);
            
            if (!$stmt->execute()) {
                throw new Exception("Error updating book availability: " . $stmt->error);
            }

            $conn->commit();
            echo "<div class='success-message'>Borrow request approved successfully!</div>";
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
    <title>Issue Book Requests</title>
    <style>
        /* Your custom styles for the page */
    </style>
</head>
<body>

<div class="container">
    <h2>Issue Book Requests</h2>

    <form method="POST">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>User</th>
                    <th>Borrow Date</th>
                    <th>Due Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query to fetch pending borrow requests
                $sql = "SELECT T.TransactionID, T.ResourceID, B.Title, U.Name as UserName, T.BorrowDate, T.DueDate 
                        FROM Transactions T 
                        JOIN Books B ON T.ResourceID = B.ResourceID 
                        JOIN Users U ON T.UserID = U.UserID 
                        WHERE T.Status = 'Pending'";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Format the BorrowDate and DueDate to show full date
                        $formattedBorrowDate = date("l, F j, Y", strtotime($row['BorrowDate']));
                        $formattedDueDate = date("l, F j, Y", strtotime($row['DueDate']));
                        
                        echo "<tr>
                                <td>{$row['Title']}</td>
                                <td>{$row['UserName']}</td>
                                <td>{$formattedBorrowDate}</td>
                                <td>{$formattedDueDate}</td>
                                <td>
                                    <input type='hidden' name='transaction_id' value='{$row['TransactionID']}'>
                                    <input type='hidden' name='resource_id' value='{$row['ResourceID']}'>
                                    <button type='submit' class='btn btn-primary'>Approve</button>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='no-results'>No pending borrow requests.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </form>
</div>

</body>
</html>
