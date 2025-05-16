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

if (isset($_POST['reject'])) {
    $id = $_POST['id'];  
    $reason = $_POST['reject_reason']; 

    
    $query = "SELECT expense_entry.expense_id, users.email, users.username FROM expense_entry JOIN users ON expense_entry.user_id = users.id 
              WHERE expense_entry.id = ?";
    
    // Prepare the query
    $stmt = $connection->prepare($query);
    
    if ($stmt === false) {
        // Error preparing the query
        die('Query error: ' . $connection->error);
    }
    
    $stmt->bind_param("i", $id); // Bind the ID parameter to the query
    $stmt->execute();  // Execute the query
    
    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the data from the result
        $expense = $result->fetch_assoc();
        $userEmail = $expense['email'];
        $userName = $expense['username'];
        $expenseId = $expense['expense_id'];

        // Update the status of the expense entry to "Rejected"
        $updateQuery = "UPDATE expense_entry SET status = 'Rejected', rejected_description = ? WHERE id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        
        if ($updateStmt === false) {
            die('Query error: ' . $connection->error);
        }

        $updateStmt->bind_param("si", $reason, $id); // Bind parameters
        $updateStmt->execute(); // Execute the update query

        // Send rejection email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vijaylakshman2422@gmail.com';  // Your email
            $mail->Password = 'tlrhyyyshfvbazcz';  // Your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            // Sender and recipient email
            $mail->setFrom('vijaylakshman2422@gmail.com', 'Tidy Digital Solutions');
            $mail->addAddress($userEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = "Expense Rejected - ID: $expenseId";
            $mail->Body = "
                <h2>Hello $userName,</h2>
                <p>Your expense request with ID <strong>$expenseId</strong> has been <span style='color:red;'>rejected</span>.</p>
                <p><strong>Reason:</strong> $reason</p>
                <br>
                <p>If you have any questions, please contact support.</p>
                <p>Regards,<br>Tidy Digital Solutions</p>
            ";

            $mail->send();  // Send the email

        } catch (Exception $e) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }

        // Redirect back after success
        header("Location: admin_dashbord.php?msg=rejected");
        // echo"<script>Mail was rejected..</script>";
        exit();
    } else {
        die('No expense found with that ID');
    }
}
?>
