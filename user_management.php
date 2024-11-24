<?php
include 'db.php';

// Handle Add, Edit, Delete Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        // Add User Logic
        $name = $_POST['name'];
        $userType = $_POST['user_type'];
        $membershipID = $_POST['membership_id'];
        $contactDetails = $_POST['contact_details'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $borrowingLimit = ($userType == 'student') ? 3 : 5;

        // Check if membership_id already exists
        $checkSql = "SELECT * FROM Users WHERE MembershipID = '$membershipID'";
        $result = $conn->query($checkSql);
        if ($result->num_rows > 0) {
            $message = "Error: Membership ID already exists!";
        } else {
            // Insert the new user
            $sql = "INSERT INTO Users (Name, UserType, MembershipID, ContactDetails, Password, BorrowingLimit) 
                    VALUES ('$name', '$userType', '$membershipID', '$contactDetails', '$password', $borrowingLimit)";
            if ($conn->query($sql) === TRUE) {
                $message = "User added successfully!";
            } else {
                $message = "Error adding user: " . $conn->error;
            }
        }
    } elseif (isset($_POST['edit_user'])) {
        // Edit User Logic
        $userID = $_POST['user_id'];
        $name = $_POST['name'];
        $userType = $_POST['user_type'];
        $contactDetails = $_POST['contact_details'];
        $membershipID = $_POST['membership_id'];
        $borrowingLimit = ($userType == 'student') ? 3 : 5;
        $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

        // Check if membership_id already exists for a different user
        $checkSql = "SELECT * FROM Users WHERE MembershipID = '$membershipID' AND UserID != $userID";
        $result = $conn->query($checkSql);
        if ($result->num_rows > 0) {
            $message = "Error: Membership ID already exists for another user!";
        } else {
            // Update the user
            $sql = "UPDATE Users SET Name = '$name', UserType = '$userType', ContactDetails = '$contactDetails', 
                    BorrowingLimit = $borrowingLimit, MembershipID = '$membershipID'";

            // Include password update if it's provided
            if (!empty($password)) {
                $sql .= ", Password = '$password'";
            }

            $sql .= " WHERE UserID = $userID";

            if ($conn->query($sql) === TRUE) {
                $message = "User updated successfully!";
            } else {
                $message = "Error updating user: " . $conn->error;
            }
        }
    } elseif (isset($_POST['delete_user'])) {
        // Delete User Logic
        $userID = $_POST['user_id'];

        $sql = "DELETE FROM Users WHERE UserID = $userID";
        if ($conn->query($sql) === TRUE) {
            $message = "User deleted successfully!";
        } else {
            $message = "Error deleting user: " . $conn->error;
        }
    }
}

// Fetch All Users
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
        .form-container, .table-container {
            margin: 20px auto;
            max-width: 900px;
        }
        .form-container {
            padding: 20px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center my-4">User Management</h1>

        <!-- Add User Form -->
        <div class="form-container">
            <h2>Add New User</h2>
            <?php if (isset($message)) echo "<div class='alert alert-info'>$message</div>"; ?>
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
    </script>
</body>
</html>
