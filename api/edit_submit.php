<?php
session_start();
require("../includes/database_connect.php");

$name = $_POST['full_name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$user_id = $_SESSION['user_id'];

$sql = "UPDATE users SET full_name='$name' , phone='$phone' , email='$email' where id=$user_id;";
$result = mysqli_query($conn,$sql);
echo "Update Successful";
header("location: ".$_SERVER['HTTP_REFERER']);
?>