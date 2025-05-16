<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['otp']) || !isset($_SESSION['pending_new_password'])) {
    header("Location: change_password.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredOtp = $_POST['otp'];

    if ($enteredOtp == $_SESSION['otp']) {
        $newPassword = $_SESSION['pending_new_password'];
        $userId = $_SESSION['user_id'];

        $stmt = $connection->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $newPassword, $userId);
        if ($stmt->execute()) {
            $message = "Password changed successfully!";
            unset($_SESSION['otp']);
            unset($_SESSION['pending_new_password']);
        } else {
            $message = "Failed to update password.";
        }
        $stmt->close();
    } else {
        $message = "Invalid OTP.";
    }
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/change_password.css">
</head>
<body>
    <form method="POST">
    <input type="text" name="otp" required placeholder="Enter OTP">
    <button type="submit">Verify & Change Password</button><br>
  <p style="margin-left: 120px;"><a href="partner_dashboard.php" style="text-decoration: none; color:royalblue">Go to Login</a></p>

    <p><?= $message ?></p>
</form>
</body>
</html>