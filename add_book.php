<?php
session_start();

include 'db.php';
include 'navigation.php';


// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle Add Book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];
    $resourceId = $_POST['resourceId']; // Get the resourceId from the form

    // Check for duplicate ISBN
    $stmt = $conn->prepare("SELECT * FROM Books WHERE ISBN = ?");
    $stmt->bind_param("s", $isbn);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "<div class='error-message'>Error: A book with this ISBN already exists.</div>";
    } else {
        // Check for duplicate ResourceID
        $stmt = $conn->prepare("SELECT * FROM Books WHERE ResourceID = ?");
        $stmt->bind_param("s", $resourceId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "<div class='error-message'>Error: A book with this Resource ID already exists.</div>";
        } else {
            // Generate a unique ResourceID if not provided
            if (empty($resourceId)) {
                $resourceId = strtoupper(uniqid('RES', true));  // Create a unique ResourceID
            }

            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO Books (Title, Author, Publisher, Genre, ISBN, AccessionNumber, Quantity, AvailableQuantity, resourceId) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Bind parameters to the prepared statement
            if ($stmt) {
                $accessionNumber = strtoupper(uniqid());  // Generate unique accession number

                $stmt->bind_param("ssssssiii", $title, $author, $publisher, $genre, $isbn, $accessionNumber, $quantity, $quantity, $resourceId);

                // Execute the statement
                if ($stmt->execute()) {
                    $message = "<div class='success-message'>Book added successfully!</div>";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    $message = "<div class='error-message'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            } else {
                $message = "<div class='error-message'>Error preparing statement: " . $conn->error . "</div>";
            }
        }
    }
    $stmt->close();
}

// Handle Edit Book
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_book'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];
    $resourceId = $_POST['resourceId']; // Get the resourceId from the form

    $sql = "UPDATE Books 
            SET Title='$title', Author='$author', Publisher='$publisher', Genre='$genre', ISBN='$isbn', Quantity='$quantity', AvailableQuantity='$quantity', resourceId='$resourceId' 
            WHERE BookID=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='success-message'>Book updated successfully!</div>";
    } else {
        $message = "<div class='error-message'>Error: " . $conn->error . "</div>";
    }
}

// Handle Delete Book
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM Books WHERE BookID=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "<div class='success-message'>Book deleted successfully!</div>";
    } else {
        $message = "<div class='error-message'>Error: " . $conn->error . "</div>";
    }
}

// Fetch Books
$result = $conn->query("SELECT * FROM Books");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management</title>
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
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ccc;
            width: 400px;
            border-radius: 8px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 24px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: inline-block;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .success-message, .error-message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
        }

        #addBookBtn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #4CAF50; /* Green */
            border: none;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        #addBookBtn:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

        #addBookBtn:active {
            background-color: #3e8e41; /* Even darker green when clicked */
            transform: scale(0.95); /* Slightly smaller when clicked */
        }

        /* Edit Button */
        button.edit-btn {
            background-color: #4CAF50; /* Green */
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button.edit-btn:hover {
            background-color: #45a049; /* Darker green on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

        button.edit-btn:active {
            background-color: #3e8e41; /* Even darker green when clicked */
            transform: scale(0.95); /* Slightly smaller when clicked */
        }

        /* Delete Button */
        button.delete-btn {
            background-color: #f44336; /* Red */
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        button.delete-btn:hover {
            background-color: #e53935; /* Darker red on hover */
            transform: scale(1.05); /* Slightly larger on hover */
        }

        button.delete-btn:active {
            background-color: #c62828; /* Even darker red when clicked */
            transform: scale(0.95); /* Slightly smaller when clicked */
        }


    </style>
</head>
<body>
    <div class="container">
        <h2>All Books</h2>
        <?php if (isset($message)) echo $message; ?>
        <button id="addBookBtn">Add Book</button>
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Genre</th>
                <th>ISBN</th>
                <th>Accession Number</th>
                <th>Quantity</th>
                <th>Resource ID</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr id="book-<?php echo $row['BookID']; ?>">
                        <td><?php echo $row['Title']; ?></td>
                        <td><?php echo $row['Author']; ?></td>
                        <td><?php echo $row['Publisher']; ?></td>
                        <td><?php echo $row['Genre']; ?></td>
                        <td><?php echo $row['ISBN']; ?></td>
                        <td><?php echo $row['AccessionNumber']; ?></td>
                        <td><?php echo $row['Quantity']; ?></td>
                        <td><?php echo $row['ResourceID']; ?></td>
                        <td>
                            <button class="edit-btn" onclick="editBook(<?php echo $row['BookID']; ?>)">Edit</button>
                            <a href="?delete=<?php echo $row['BookID']; ?>" onclick="return confirm('Are you sure you want to delete this book?')">
                                <button class="delete-btn">Delete</button>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9">No books found.</td></tr>
            <?php endif; ?>
        </table>

        <!-- Add Book Modal -->
        <div id="addBookModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h2>Add Book</h2>
                <form method="POST">
                    <label for="title">Title:</label>
                    <input type="text" name="title" id="title" required><br>

                    <label for="author">Author:</label>
                    <input type="text" name="author" id="author" required><br>

                    <label for="publisher">Publisher:</label>
                    <input type="text" name="publisher" id="publisher" required><br>

                    <label for="genre">Genre:</label>
                    <input type="text" name="genre" id="genre" required><br>

                    <label for="isbn">ISBN:</label>
                    <input type="text" name="isbn" id="isbn" required><br>

                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" required><br>

                    <label for="resourceId">Resource ID:</label>
                    <input type="text" name="resourceId" id="resourceId" value="RES<?php echo strtoupper(uniqid()); ?>" required><br>

                    <input type="submit" name="add_book" value="Add Book">
                </form>
            </div>
        </div>


    <!-- Modal for Edit Book -->
    <div id="editBookModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Book</h2>
            <form method="POST">
                <input type="hidden" name="id" id="editBookId">

                <label for="editTitle">Title:</label>
                <input type="text" name="title" id="editTitle" required><br>

                <label for="editAuthor">Author:</label>
                <input type="text" name="author" id="editAuthor" required><br>

                <label for="editPublisher">Publisher:</label>
                <input type="text" name="publisher" id="editPublisher" required><br>

                <label for="editGenre">Genre:</label>
                <input type="text" name="genre" id="editGenre" required><br>

                <label for="editIsbn">ISBN:</label>
                <input type="text" name="isbn" id="editIsbn" required><br>

                <label for="editQuantity">Quantity:</label>
                <input type="number" name="quantity" id="editQuantity" required><br>

                <!-- Visible and editable resourceId field -->
                <label for="editResourceId">Resource ID:</label>
                <input type="text" name="resourceId" id="editResourceId" required><br>

                <input type="submit" name="edit_book" value="Update Book">
            </form>
        </div>
    </div>


    <script>
        // Modal functionality
        var modal = document.getElementById("addBookModal");
        var btn = document.getElementById("addBookBtn");
        var span = document.getElementsByClassName("close")[0];

        var editModal = document.getElementById("editBookModal");

        // Open Add Book Modal
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // Close Add Book Modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal || event.target == editModal) {
                modal.style.display = "none";
                editModal.style.display = "none";
            }
        }

        // Edit Book Modal - fill with current book details
        function editBook(bookId) {
            var bookRow = document.getElementById('book-' + bookId);
            var title = bookRow.cells[0].textContent;
            var author = bookRow.cells[1].textContent;
            var publisher = bookRow.cells[2].textContent;
            var genre = bookRow.cells[3].textContent;
            var isbn = bookRow.cells[4].textContent;
            var quantity = bookRow.cells[6].textContent;
            var resourceId = bookRow.cells[7].textContent;

            document.getElementById('editBookId').value = bookId;
            document.getElementById('editTitle').value = title;
            document.getElementById('editAuthor').value = author;
            document.getElementById('editPublisher').value = publisher;
            document.getElementById('editGenre').value = genre;
            document.getElementById('editIsbn').value = isbn;
            document.getElementById('editQuantity').value = quantity;
            document.getElementById('editResourceId').value = resourceId;

            document.getElementById('editBookModal').style.display = "block";
        }

        // Close Edit Book Modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        window.onload = function() {
        const messages = document.querySelectorAll('.success-message, .error-message');
        
        messages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0'; // Fade out
                setTimeout(() => {
                    message.style.display = 'none'; // Remove from DOM after fade-out
                }, 1000); // Matches the fade-out duration (1 second)
            }, 3000); // Wait for 3 seconds before starting fade-out
        });
    }
    </script>
</body>
</html>
