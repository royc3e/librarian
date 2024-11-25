<?php
session_start();

include 'db.php';
include 'navigation.php';

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
        $updateSql = "UPDATE Books SET AvailableQuantity = AvailableQuantity - 1 
                      WHERE ResourceID = $resourceID AND AvailableQuantity > 0";
        if (!$conn->query($updateSql)) {
            throw new Exception("Error updating book availability: " . $conn->error);
        }

        // Commit the transaction if everything is successful
        $conn->commit();
        echo "<div class='success-message'>Book borrowed successfully!</div>";
    } catch (Exception $e) {
        // Rollback the transaction if there was an error
        $conn->rollback();
        echo "<div class='error-message'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!-- Form to borrow a book -->
<div class="container">
    <h2 class="text-center my-4">Borrow a Book</h2>
    <form method="POST" class="borrow-form">
        <div class="mb-3">
            <label for="resource_id" class="form-label">Select Book</label>
            <select name="resource_id" id="resource_id" class="form-control" required>
                <option value="">-- Choose a Book --</option>
                <?php
                // Query the database to get available books
                $sql = "SELECT ResourceID, Title, AvailableQuantity FROM Books WHERE AvailableQuantity > 0";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Loop through the books and display them in the dropdown
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['ResourceID'] . "'>" . $row['Title'] . " (Available: " . $row['AvailableQuantity'] . ")</option>";
                    }
                } else {
                    echo "<option value=''>No books available</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-success btn-lg w-100">Borrow Book</button>
    </form>

    <h3 class="text-center my-4">Available Books</h3>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Genre</th>
                <th>ISBN</th>
                <th>Available Copies</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to fetch available books
            $booksSql = "SELECT Title, Author, Publisher, Genre, ISBN, AvailableQuantity FROM Books WHERE AvailableQuantity > 0";
            $booksResult = $conn->query($booksSql);

            if ($booksResult->num_rows > 0) {
                while ($book = $booksResult->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $book['Title'] . "</td>
                            <td>" . $book['Author'] . "</td>
                            <td>" . $book['Publisher'] . "</td>
                            <td>" . $book['Genre'] . "</td>
                            <td>" . $book['ISBN'] . "</td>
                            <td>" . $book['AvailableQuantity'] . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No books available for borrowing</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Add Bootstrap CDN for styling -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* Custom Styles for the page */
    body {
        background-color: #f8f9fa;
        font-family: 'Arial', sans-serif;
    }

    .container {
        max-width: 1200px;
        margin-top: 30px;
    }

    h2, h3 {
        color: #343a40;
        font-weight: bold;
    }

    .form-label {
        font-size: 1.1em;
        font-weight: bold;
        color: #495057;
    }

    .btn {
        background-color: #007bff;
        color: white;
        padding: 12px;
        font-size: 1.1em;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .table {
        width: 100%;
        margin-top: 30px;
    }

    .table-striped tbody tr:nth-child(odd) {
        background-color: #f2f2f2;
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #dee2e6;
    }

    .table th {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }

    /* Message Style */
    .success-message {
        color: green;
        font-weight: bold;
        margin-top: 10px;
    }

    .error-message {
        color: red;
        font-weight: bold;
        margin-top: 10px;
    }

    /* Centering buttons and form */
    .borrow-form {
        max-width: 500px;
        margin: 0 auto;
    }
</style>

<!-- Add Bootstrap JS (optional for dropdowns, modals, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>