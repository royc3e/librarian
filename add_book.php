<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publisher = $_POST['publisher'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $quantity = $_POST['quantity'];
    $accessionNumber = "ACC" . strtoupper(uniqid());  // Example: Generate unique Accession Number

    // Insert book into database
    $sql = "INSERT INTO Books (Title, Author, Publisher, Genre, ISBN, AccessionNumber, Quantity, AvailableQuantity) 
            VALUES ('$title', '$author', '$publisher', '$genre', '$isbn', '$accessionNumber', '$quantity', '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message'>Book added successfully!</div>";
    } else {
        echo "<div class='error-message'>Error: " . $conn->error . "</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #4CAF50;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: inline-block;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
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
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Book</h2>
        <form method="POST">
            <label for="title">Title:</label>
            <input type="text" name="title" required><br>

            <label for="author">Author:</label>
            <input type="text" name="author" required><br>

            <label for="publisher">Publisher:</label>
            <input type="text" name="publisher"><br>

            <label for="genre">Genre:</label>
            <input type="text" name="genre"><br>

            <label for="isbn">ISBN:</label>
            <input type="text" name="isbn"><br>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" required><br>

            <input type="submit" value="Add Book">
        </form>
    </div>
</body>
</html>
