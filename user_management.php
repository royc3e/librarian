<?php
session_start();

include 'db.php';
include 'navigation.php';

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

$message = ''; // To hold success or error messages

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $name = $_POST['name'];
        $userType = $_POST['user_type'];
        $membershipID = $_POST['membership_id'];
        $contactDetails = $_POST['contact_details'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure password hashing
        $borrowingLimit = ($userType === 'student') ? 3 : 5;

        try {
            $sql = "INSERT INTO Users (Name, UserType, MembershipID, ContactDetails, Password, BorrowingLimit) 
                    VALUES ('$name', '$userType', '$membershipID', '$contactDetails', '$password', $borrowingLimit)";
            $conn->query($sql);
            $message = "User added successfully!";
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) { // Duplicate entry error code
                $message = "Error: Membership ID '$membershipID' already exists. Please use a different ID.";
            } else {
                $message = "Error adding user: " . $e->getMessage();
            }
        }
    }


    // Handle Edit User
    if (isset($_POST['edit_user'])) {
        $userID = $_POST['user_id'];
        $name = $_POST['name'];
        $userType = $_POST['user_type'];
        $membershipID = $_POST['membership_id'];
        $contactDetails = $_POST['contact_details'];
        $password = $_POST['password'];

        $passwordClause = '';
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $passwordClause = ", Password = '$passwordHash'";
        }

        $sql = "UPDATE Users 
                SET Name = '$name', UserType = '$userType', MembershipID = '$membershipID', 
                    ContactDetails = '$contactDetails' $passwordClause 
                WHERE UserID = $userID";

        if ($conn->query($sql)) {
            $message = "User updated successfully!";
        } else {
            if ($conn->errno === 1062) {
                $message = "Error: Membership ID '$membershipID' already exists. Please use a different ID.";
            } else {
                $message = "Error updating user: " . $conn->error;
            }
        }
    }

    // Handle Delete User
    if (isset($_POST['delete_user'])) {
        $userID = $_POST['user_id'];
        $sql = "DELETE FROM Users WHERE UserID = $userID";
        if ($conn->query($sql)) {
            $message = "User deleted successfully!";
        } else {
            $message = "Error deleting user: " . $conn->error;
        }
    }
}

// Fetch all users
$users = $conn->query("SELECT * FROM Users");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-container {
            margin: 20px auto;
            max-width: 900px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">User Management</h1>

            <!--Error and Success message -->
            <?php if (!empty($message)): ?>
                <div id="message" class="alert alert-info">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>


        <!-- Add User Button -->
        <div class="text-end mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add New User</button>
        </div>

        <!-- Add User Modal -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="add_user" value="1">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_type" class="form-label">User Type</label>
                                <select name="user_type" id="user_type" class="form-select" required>
                                    <option value="student">Student</option>
                                    <option value="faculty">Faculty</option>
                                    <option value="staff">Staff</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="membership_id" class="form-label">Membership ID</label>
                                <input type="text" name="membership_id" id="membership_id" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="contact_details" class="form-label">Contact Details</label>
                                <input type="text" name="contact_details" id="contact_details" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-success">Add User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Users -->
        <div class="table-container">
            <h2>Existing Users</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Name</th>
                        <th>User Type</th>
                        <th>Membership ID</th>
                        <th>Contact Details</th>
                        <th>Borrowing Limit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['UserID']; ?></td>
                        <td><?php echo htmlspecialchars($user['Name']); ?></td>
                        <td><?php echo htmlspecialchars($user['UserType']); ?></td>
                        <td><?php echo htmlspecialchars($user['MembershipID']); ?></td>
                        <td><?php echo htmlspecialchars($user['ContactDetails']); ?></td>
                        <td><?php echo $user['BorrowingLimit']; ?></td>
                        <td>
                            <!-- Edit Button -->
                            <button class="btn btn-primary btn-sm" onclick="editUser(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars($user['Name']); ?>', '<?php echo htmlspecialchars($user['UserType']); ?>', '<?php echo htmlspecialchars($user['ContactDetails']); ?>', '<?php echo htmlspecialchars($user['MembershipID']); ?>')">Edit</button>
                            <!-- Delete Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_user" value="1">
                                <input type="hidden" name="user_id" value="<?php echo $user['UserID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit User Script -->
    <script>
        function editUser(userID, name, userType, contactDetails, membershipID) {
            const formHTML = `
                <div class="form-container">
                    <h2>Edit User</h2>
                    <form method="POST">
                        <input type="hidden" name="edit_user" value="1">
                        <input type="hidden" name="user_id" value="${userID}">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="${name}" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_type" class="form-label">User Type</label>
                            <select name="user_type" id="user_type" class="form-select" required>
                                <option value="student" ${userType === 'student' ? 'selected' : ''}>Student</option>
                                <option value="faculty" ${userType === 'faculty' ? 'selected' : ''}>Faculty</option>
                                <option value="staff" ${userType === 'staff' ? 'selected' : ''}>Staff</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="membership_id" class="form-label">Membership ID</label>
                            <input type="text" name="membership_id" id="membership_id" class="form-control" value="${membershipID}" required>
                        </div>
                        <div class="mb-3">
                            <label for="contact_details" class="form-label">Contact Details</label>
                            <input type="text" name="contact_details" id="contact_details" class="form-control" value="${contactDetails}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (leave blank if not changing)</label>
                            <input type="password" name="password" id="password" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update User</button>
                    </form>
                </div>`;
            document.querySelector('.container').innerHTML = formHTML;
        }

        setTimeout(() => {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                messageDiv.style.transition = "opacity 0.5s"; // Smooth fade-out
                messageDiv.style.opacity = "0"; // Set to transparent
                setTimeout(() => messageDiv.remove(), 500); // Remove after fade-out
            }
        }, 3000); // 
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
