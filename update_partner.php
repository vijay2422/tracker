<?php
    include('db.php');
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    if(isset($_POST['btn-submit'])) {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $roll = $_POST['roll'];
        
        $sql = "UPDATE users SET `username` = '$username', `email` = '$email', `roll` = '$roll' WHERE `id` = '$id'";
        
    if(mysqli_query($connection,$sql)){
        echo '<script>location.replace("admin_dashbord.php")</script>';
    }
    else{
        echo "Somthing Error".$connection->error;
    }
    }

    ?>