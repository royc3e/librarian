<?php
include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the selected resource_id from the form submission
    $resourceID = $_POST['resource_id'];
    $userID = $_SESSION['UserID'];
    $borrowDate = date("Y-m-d");
    $dueDate = date("Y-m-d", strtotime("+14 days"));

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Insert the borrowing transaction into the Transactions table
        $sql = "INSERT INTO Transactions (UserID, ResourceID, BorrowDate, DueDate) 
                VALUES ($userID, $resourceID, '$borrowDate', '$dueDate')";
        if (!$conn->query($sql)) {
            throw new Exception("Error inserting transaction: " . $conn->error);
        }

        // Update the available copies of the book in the Books table
        $updateSql = "UPDATE Books SET AvailableCopies = AvailableCopies - 1 
                      WHERE ResourceID = $resourceID AND AvailableCopies > 0";
        if (!$conn->query($updateSql)) {
            throw new Exception("Error updating book availability: " . $conn->error);
        }

        // Commit the transaction if everything is successful
        $conn->commit();
        echo "Book borrowed successfully!";
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}
?>

<!-- Form to borrow a book -->
<form method="POST">
    <div class="mb-3">
        <label for="resource_id" class="form-label">Select Book</label>
        <select name="resource_id" id="resource_id" class="form-control" required>
            <option value="">-- Choose a Book --</option>
            <?php
            // Query the database to get available books
            $sql = "SELECT ResourceID, Title FROM Books WHERE AvailableCopies > 0";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Loop through the books and display them in the dropdown
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['ResourceID'] . "'>" . $row['Title'] . "</option>";
                }
            } else {
                echo "<option value=''>No books available</option>";
            }
            ?>
        </select>
    </div>

    <input type="submit" value="Borrow Book" class="btn btn-primary">
</form>
