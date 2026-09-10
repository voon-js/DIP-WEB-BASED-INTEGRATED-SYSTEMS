<?php
require "_base.php";
require "database.php";

// 添加登录检查
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// 获取用户信息
$sql = "SELECT email, username, age, gender, contact_no, addresss, profile_pic FROM customer WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']); // 使用session中的user_id
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $current_profile_pic = !empty($user['profile_pic']) ? $user['profile_pic'] : 'images/default.png';
    $current_email = htmlspecialchars($user['email']);
    $current_username = htmlspecialchars($user['username']);
    $current_age = htmlspecialchars($user['age']);
    $current_gender = htmlspecialchars($user['gender']);
    $current_contact = htmlspecialchars($user['contact_no']);
    $current_addresss = htmlspecialchars($user['addresss']);
    $current_profile_pic = $user['profile_pic'] ? htmlspecialchars($user['profile_pic']) : 'images/default.png';
} else {
    die("User not found");
}

$conn->close();

$_title = "Member Profile";
include "_head.php";
?>

<!DOCTYPE html>
<html>
</header>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="css/memProfile.css">
    <title>Profile</title>

    <style>
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <main class="memProfile">

        <a href="profile.php">
            <butto class="btn" style="position: absolute; left: 15%; float:left">Back</button>
        </a>

        <h2>Profile</h2>

        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <!-- 照片 -->
            <div class="upload-area" id="dropZone">
                <div class="upload-content">
                    <img src="<?php echo $current_profile_pic; ?>" alt="Profile Picture" class="profile-pic" id="previewImage">
                    <label for="profile_pic" class="custom-file-upload">
                        Choose File
                    </label>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" class="picPro">
                    <span id="file-name">No file selected</span>
                </div>
            </div>
            <br><br>

            <!-- Email -->
            <label for="email">Email</label><br>
            <input class="input1" type="text" id="email" name="email" value="<?php echo $current_email ?>" disabled><br><br>

            <!-- Username -->
            <label for="username">Username</label><br>
            <input class="input1" type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? $current_username); ?>"
                class="<?php echo isset($errors['username']) ? 'error-field' : ''; ?>">
            <?php if (isset($errors['username'])): ?>
                <div class="field-error"><?php echo $errors['username']; ?></div>
            <?php else: ?>
                <br><br>
            <?php endif; ?>

            

            <!-- 电话号码 -->
            <label for="contact_no">Contact Number</label><br>
            <input class="input1" type="tel" id="contact_no" name="contact_no"
    value="<?php echo htmlspecialchars($_POST['contact_no'] ?? $current_contact); ?>"
    placeholder="+60">
            <?php if (isset($errors['contact_no'])): ?>
                <div class="field-error"><?php echo $errors['contact_no']; ?></div>
            <?php else: ?>
                <br><br>
            <?php endif; ?>

            <!-- Address 变为可选 -->
            <label for="addresss">Address</label><br>
            <input class="input1" type="text" id="addresss" name="addresss" value="<?php echo htmlspecialchars($_POST['addresss'] ?? $current_addresss); ?>"
                class="<?php echo isset($errors['addresss']) ? 'error-field' : ''; ?>">
            <?php if (isset($errors['addresss'])): ?>
                <div class="field-error"><?php echo $errors['addresss']; ?></div>
            <?php else: ?>
                <br><br>
            <?php endif; ?>

            <input type="submit" name="submit" value="Update Profile" class="btn">
        </form>
    </main>
    <script src="js/editProfile.js"></script>
</body>

</html>