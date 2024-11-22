<?php
include 'db.php';

$sql = "SELECT * FROM LibraryResources";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Title: " . $row['Title'] . " | Accession Number: " . $row['AccessionNumber'] . "<br>";
    }
} else {
    echo "No books found.";
}
?>
