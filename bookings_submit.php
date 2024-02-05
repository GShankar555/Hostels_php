<?php
session_start();
require "includes/database_connect.php";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $property_id = $_GET['property_id'];
    // Check if the form fields are set before accessing them
    $first_name = $_POST['name'];
    $last_name = $_POST['lname'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $zipcode = $_POST['zip'];
    $phone = $_POST['number'];
    $email = strtolower($_POST['email']);
    $tenure = $_POST['months'];
    $gender = $_POST['gender'];
    $user_id = $_SESSION['user_id'];
    $sql = "INSERT INTO bookings_users (first_name, last_name, address, city, state, zip_code, mobile_number, email, tenure, gender,property_id,user_id) VALUES ('$first_name', '$last_name', '$address', '$city', '$state', '$zipcode', '$phone', '$email', '$tenure', '$gender','$property_id',$user_id)";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $response = array("success" => false, "message" => "Something went wrong!");
        echo json_encode($response);
        return;
    } else {
        header("location: dashboard.php");
        mysqli_close($conn);
    }
}