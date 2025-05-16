<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//   header("Location: index.php");
//   exit();
// }
require 'vendor/autoload.php';
include('db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];

    if ($status === "Approved" || $status === "Rejected") {
        
        // Update status in database////
        $sql = "UPDATE expense_entry SET status = '$status' WHERE id = $id";
        if (mysqli_query($connection, $sql)) {

            // Get email info if status is Approved
            if ($status === "Approved") {
                $stmt = $connection->prepare("SELECT expense_entry.expense_id, users.username, users.email FROM expense_entry JOIN users ON expense_entry.user_id = users.id WHERE expense_entry.id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $expense = $result->fetch_assoc();
                    $userEmail = $expense['email'];
                    $userName = $expense['username'];
                    $expenseId = $expense['expense_id'];

                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'vijaylakshman2422@gmail.com';
                        $mail->Password = 'tlrhyyyshfvbazcz';  // Use env file in production!
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 587;

                        $mail->setFrom('vijaylakshman2422@gmail.com', 'Tidy Digital Solutions');
                        $mail->addAddress($userEmail, $userName);
                        $mail->isHTML(true);
                        $mail->Subject = "Expense Approved - ID: $expenseId";
                        $mail->Body = "
                            <h2>Hello $userName,</h2>
                            <p>Your expense request with ID : <strong>$expenseId</strong> 
                            <br>has been <span style='color:green;'>Approved</span>.</p>
                            <br>
                             <p>If you have any questions, please contact support.</p>
                             <p>Regards,<br>Tidy Digital Solutions</p>";

                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Mailer Error: " . $mail->ErrorInfo);
                    }
                }
            }

            header("Location: admin_dashbord.php?msg=$status");
            exit;
        } else {
            echo "SQL Error: " . mysqli_error($connection);
        }
    }
}
?>
