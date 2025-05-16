<?php
$servername="localhost";
$username="root";
$password="";
$database="project";

$connection=new mysqli($servername,$username,$password,$database);
if(!$connection){
    die("Connection FailedL:".$connection->$connection_error);
}




?>