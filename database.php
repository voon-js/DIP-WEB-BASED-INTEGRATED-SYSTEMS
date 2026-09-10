<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();  // 仅在会话没有启动时调用 session_start()
}
$user_id = $_SESSION['user_id'];
$staff_id = "S001";

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "xburger";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$error = '';
$success_message = '';
$error_message = '';

if (isset($_POST['password_submit'])) {
    $errors = [];
    $user_id = $_SESSION['user_id'];

    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    if ($new_password !== $confirm_password) {
        $errors[] = "New passwords do not match.";
    }

    // Check password length
if (strlen($new_password) < 8) {
    $errors[] = "Password must be at least 8 characters long.";
}

// Check first character is uppercase letter
if (!preg_match('/^[A-Z]/', $new_password)) {
    $errors[] = "Password must start with an uppercase letter (A-Z).";
}


    if (empty($errors)) {
        // Get current password from database
        $sql = "SELECT log_pass FROM customer WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $current_password = $user['log_pass'];

            if (password_verify($old_password, $current_password)) {
                // Hash the new password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password in database
                $update_sql = "UPDATE customer SET log_pass = ? WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $new_password_hash, $user_id);

                if ($update_stmt->execute()) {
                    $success_message = "Password updated successfully!";
                } else {
                    $error_message = "Error updating password: " . $conn->error;
                }
            } else {
                $error_message = "Incorrect current password.";
            }
        } else {
            $error_message = "User not found.";
        }
    } else {
        $error_message = implode("<br>", $errors);
    }

    $conn->close();
}

if (isset($_POST['submit'])) {
    $errors = [];
    $user_id = $_SESSION['user_id'];

    // Validate Username
    $new_username = trim($_POST['username']);
    if (empty($new_username)) {
        $errors['username'] = "Username is required";
    } elseif (strlen($new_username) < 4 || strlen($new_username) > 20) {
        $errors['username'] = "Username must be between 4-20 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
        $errors['username'] = "Username can only contain letters, numbers, and underscores";
    }

    // Age can be empty
    $new_age = $_POST['age'] ?? null;

    // Gender can be empty
    $new_gender = $_POST['gender'] ?? null;

    // Validate Contact Number
    $new_contact = trim($_POST['contact_no']);
    if (empty($new_contact)) {
        $errors['contact_no'] = "Contact number is required";
    } elseif (!preg_match('/^[0-9]{9,14}$/', $new_contact)) {
        $errors['contact_no'] = "Contact number must be 9-14 digits (without +60)";
    }

    // Address can be empty
    $new_addresss = trim($_POST['addresss']) ?? null;

    // Only proceed if no validation errors
    if (empty($errors)) {
        // [Previous profile picture upload code remains the same...]

        // Handle profile picture upload
        $profile_pic_path = null;
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/profile_pics/';

            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $file = $_FILES['profile_pic'];

            if (!in_array($file['type'], $allowedTypes)) {
                $error_message = "Error: Only JPG, PNG, and GIF files are allowed.";
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $error_message = "Error: File size must be less than 2MB.";
            } else {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $newFilename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $extension;
                $destination = $uploadDir . $newFilename;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $profile_pic_path = $destination;

                    $sql = "SELECT profile_pic FROM customer WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $oldPic = $result->fetch_assoc()['profile_pic'];

                    if ($oldPic && file_exists($oldPic) && $oldPic != 'images/default.png') {
                        unlink($oldPic);
                    }

                    $sql = "UPDATE customer SET profile_pic = ? WHERE user_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $profile_pic_path, $_SESSION['user_id']);

                    if ($stmt->execute()) {
                        $_SESSION['profile_pic'] = $profile_pic_path;
                        $success_message = "Profile picture updated successfully!";
                    } else {
                        $error_message = "Error updating profile: " . $conn->error;
                    }
                }
            }
        }

        $sql = "UPDATE customer SET username = ?, age = ?, gender = ?, contact_no = ?, addresss = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sisisi", $new_username, $new_age, $new_gender, $new_contact, $new_addresss, $user_id);

        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile: " . $conn->error;
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}

if (isset($_POST['staff_submit'])) {
    $staff_id = $_POST['staff_id'];

    $new_sname = trim($_POST['sname']);
    $new_birth = ($_POST['birth']);
    $new_pass = ($_POST['pass']);

    $sql = "UPDATE staff SET staff_name = ?, birth_date = ?, log_pass = ? WHERE staff_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $new_sname, $new_birth, $new_pass, $staff_id);

    if ($stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}

if (isset($_POST['status_submit'])) {
    $order_id = $_POST['order_id'];
    $selected_status = $_POST['status'];

    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $selected_status, $order_id);

    if ($stmt->execute()) {
        $success_message = "Order status updated successfully!";
    } else {
        $error_message = "Error updating order status: " . $conn->error;
    }
}
?>



