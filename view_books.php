<?php
include 'db.php'; // Database connection

// Initialize searchTerm to prevent undefined variable error
$searchTerm = "";

// Fetch Books from the Database
$sql = "SELECT * FROM Books";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchTerm = htmlspecialchars($_GET['search']);  // Sanitizing input
    $sql .= " WHERE Title LIKE ? OR Author LIKE ? OR Genre LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTermWithWildcards = "%" . $searchTerm . "%";
    $stmt->bind_param("sss", $searchTermWithWildcards, $searchTermWithWildcards, $searchTermWithWildcards);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Resources</title>
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

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input[type="text"] {
            padding: 10px;
            width: 50%;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .search-bar input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .search-bar input[type="submit"]:hover {
            background-color: #45a049;
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

        .book-actions a {
            color: #007BFF;
            text-decoration: none;
            margin: 0 5px;
        }

        .book-actions a:hover {
            text-decoration: underline;
        }

        .no-results {
            text-align: center;
            color: #888;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Library Resources</h2>
        
        <!-- Search Bar for filtering books -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Search by title, author, or genre" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <input type="submit" value="Search">
            </form>
        </div>

        <!-- Books Table -->
        <table>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Publisher</th>
                <th>Genre</th>
                <th>ISBN</th>
                <th>Accession Number</th>
                <th>Quantity</th>
                <th>Available Quantity</th>
            </tr>
            
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Title']); ?></td>
                        <td><?php echo htmlspecialchars($row['Author']); ?></td>
                        <td><?php echo htmlspecialchars($row['Publisher']); ?></td>
                        <td><?php echo htmlspecialchars($row['Genre']); ?></td>
                        <td><?php echo htmlspecialchars($row['ISBN']); ?></td>
                        <td><?php echo htmlspecialchars($row['AccessionNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['Quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['AvailableQuantity']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="no-results">No books found</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
