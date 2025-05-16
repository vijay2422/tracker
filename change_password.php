<?php
session_start();
include('db.php');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;

$message = "";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['user_id'];
    $oldPassword = md5($_POST['old_password']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $message = "New password and confirm password do not match.";
    } elseif ($oldPassword === md5($newPassword)) {
        $message = "New password must be different from old password.";
    } elseif (strlen($newPassword) < 8) {
        $message = "Password must be at least 8 characters.";
    } else {
        $stmt = $connection->prepare("SELECT email, password FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($email, $dbPassword);
        $stmt->fetch();
        $stmt->close();

        if ($oldPassword === $dbPassword) {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['pending_new_password'] = md5($newPassword);

            // Send Email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'vijaylakshman2422@gmail.com';
                $mail->Password = 'tlrhyyyshfvbazcz'; // use app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('vijaylakshman2422@gmail.com', 'Your App');
                $mail->addAddress($email);
                $mail->Subject = "Your OTP for Password Change";
                $mail->Body = "Your OTP code is: $otp";

                $mail->send();
                header("Location: send_reset.php");
                exit;
            } catch (Exception $e) {
                $message = "Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>

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
    <input type="password" name="old_password" required placeholder="Old Password">
    <input type="password" name="new_password" required placeholder="New Password">
    <input type="password" name="confirm_password" required placeholder="Confirm Password">
    <button type="submit">Send OTP to Email</button>
    <p><?= $message ?></p>
</form>
</body>
</html>
