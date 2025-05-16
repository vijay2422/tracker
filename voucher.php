


<?php

include('db.php');

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

require_once __DIR__ . '/vendor/autoload.php';


// $sql = "SELECT * FROM expense_entry ORDER BY expense_id DESC LIMIT 1";

// $sql = "SELECT expense_entry.*, admin_record.name, admin_record.email 
// FROM expense_entry 
// JOIN admin_record ON expense_entry.user_id = admin_record.id WHERE expense_entry.user_id = {$_SESSION['user_id']}";


$expenseId = $_GET['expense_id'];

// $sql = "SELECT expense_entry.*, users.username, users.email 
// FROM expense_entry 
// JOIN users ON expense_entry.user_id = users.id WHERE expense_entry.user_id = {$_SESSION['user_id']}";

// $expenseId = $_GET['expense_id'];




$sql = "SELECT expense_entry.*, users.username, users.email 
FROM expense_entry JOIN users ON expense_entry.user_id = users.id WHERE expense_entry.expense_id='$expenseId'";

$result = $connection->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
   
    $expenseId = $row['expense_id'];
    $voucherId = $row['voucher_id'];
    $username=$row['username'];
    $amount = $row['amount'];
    $date = $row['expense_date'];
    $correct_format_date = date("d-m-Y", strtotime($date));
    $category = $row['category'];
    $description = $row['description'];
    $status = $row['status'];
    $partnerName = $row['personname']; 
    $email = $row['email']; 

    // Start PDF generation
    $mpdf = new \Mpdf\Mpdf();
    ob_start();
    include 'voucher_template.php'; 
    $html = ob_get_clean();

    $mpdf->WriteHTML($html);
    $mpdf->Output("Voucher-$expenseId.pdf", 'D'); 
} else {
    echo "No expense entries found.";
}

$connection->close();
?>
