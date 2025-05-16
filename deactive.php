<?php
include('db.php');


   if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    if(isset($_POST['deactive'])) {
        $id = $_POST['id'];
        $Action=$_POST['deactive'];
        $changeActionValue =  ($Action == "Active") ? "Deactivate" : "Active";
        $sql="UPDATE users SET `Action`='$changeActionValue' WHERE `id` = '$id'";
        
    if(mysqli_query($connection,$sql)){
        echo '<script>location.replace("admin_dashbord.php")</script>';
    }
    else{
        echo "Somthing Error".$connection->error;
    }
}


?>