<?php
include 'db.php';

if (isset($_POST['user_id'])) {
    $userID = $_POST['user_id'];

    // Fetch borrowing history
    $historyQuery = $conn->prepare("SELECT * FROM BorrowHistory WHERE UserID = ?");
    $historyQuery->bind_param("i", $userID);
    $historyQuery->execute();
    $borrowingHistory = $historyQuery->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch current transactions
    $transactionQuery = $conn->prepare("SELECT * FROM CurrentTransactions WHERE UserID = ?");
    $transactionQuery->bind_param("i", $userID);
    $transactionQuery->execute();
    $currentTransactions = $transactionQuery->get_result()->fetch_all(MYSQLI_ASSOC);

    // Return JSON response
    echo json_encode(['history' => $borrowingHistory, 'transactions' => $currentTransactions]);
}
?>
