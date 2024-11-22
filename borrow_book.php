<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $resourceID = $_POST['resource_id'];
    $userID = $_SESSION['UserID'];
    $borrowDate = date("Y-m-d");
    $dueDate = date("Y-m-d", strtotime("+14 days"));

    $sql = "INSERT INTO Transactions (UserID, ResourceID, BorrowDate, DueDate) 
            VALUES ($userID, $resourceID, '$borrowDate', '$dueDate')";

    if ($conn->query($sql) === TRUE) {
        echo "Book borrowed successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<form method="POST">
    Resource ID: <input type="text" name="resource_id" required><br>
    <input type="submit" value="Borrow">
</form>
