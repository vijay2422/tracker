<?php
session_start();
// if (!isset($_SESSION['user'])) {
//   header("Location: index.php");
//   exit();
// }
include('db.php');

if (isset($_POST['update'])) {
    // $expense_id = $_POST['expense_id'];
    // $amount = $_POST['amount'];
    // $date = $_POST['date'];
    // $category = $_POST['category'];
    // $description = $_POST['description'];

$expense_id = mysqli_real_escape_string($connection, $_POST['expense_id']);
$amount = mysqli_real_escape_string($connection, $_POST['amount']);
$date = mysqli_real_escape_string($connection, $_POST['date']);
$category = mysqli_real_escape_string($connection, $_POST['category']);
$description = mysqli_real_escape_string($connection, $_POST['description']);


    $upload_files = [];

    // Handle new file uploads
    if (!empty($_FILES['file']['name'][0])) {
        foreach ($_FILES['file']['name'] as $key => $name) {
            $tmp_name = $_FILES['file']['tmp_name'][$key];
            $unique_name = time() . '-' . uniqid() . '-' . basename($name);
            $upload_folder = 'upload/';
            $upload_path = $upload_folder . $unique_name;

            if (move_uploaded_file($tmp_name, $upload_path)) {
                $upload_files[] = $unique_name;
            }
        }
    }
    if (!empty($upload_files)) {
        $file_string = implode(',', $upload_files);

         $sql = "UPDATE expense_entry  SET amount = '$amount', expense_date = '$date', category = '$category', description = '$description',`filename`='$file_string'
            WHERE expense_id = '$expense_id' ";

        // $sql = "UPDATE expense_entry  SET amount = '$amount', expense_date = '$date', category = '$category', description = '$description',`filename`='$file_string'
        //     WHERE user_id = '$user_id' ";
          
    }
    else{
        $sql = "UPDATE expense_entry SET amount='$amount', expense_date='$date', category='$category', description='$description' 
        WHERE expense_id='$expense_id'";

        // $sql = "UPDATE expense_entry SET amount='$amount', expense_date='$date', category='$category', description='$description' 
        // WHERE user_id='$user_id'";
    }
    

    if (mysqli_query($connection, $sql)) {
        echo "<script>alert('Expense updated successfully'); window.location.href='partner_dashboard.php#expense';</script>";
    } else {
        echo "Error: " . mysqli_error($connection);
    }
}
?>
