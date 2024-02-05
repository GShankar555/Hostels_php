<?php
error_reporting(E_ALL); // Add this line for error reporting

// Include your database connection logic here
require "includes/database_connect.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = $_POST['property_id'];
    $user_id = $_POST['user_id'];
    echo $property_id;
    error_log("Property ID: $property_id, User ID: $user_id");
    // Update the interested count in the database
    try {
        $sql = "SELECT interested_count as num_rows FROM interested_users_properties where user_id = '$user_id' and property_id = '$property_id'
        ";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $numRows = $row['num_rows'];

        
        if($numRows==0){
            $insert_query = "INSERT INTO interested_users_properties(user_id,property_id,interested_count) values('$user_id','$property_id',1)";
            $result_insert = mysqli_query($conn, $insert_query);
            
        }
        else{
            $update_query = "DELETE FROM interested_users_properties WHERE property_id = '$property_id' and user_id = '$user_id'";
            $result_update = mysqli_query($conn,$update_query);
        }
        $stmt = "SELECT sum(interested_count) as interested_count FROM interested_users_properties WHERE property_id = '$property_id'";
        $response = mysqli_query($conn,$stmt);
        $response_1 = mysqli_fetch_assoc($response);
        echo $response_1['interested_count'];
    } catch (PDOException $e) {
        echo "Error updating the interested count: " . $e->getMessage();
    }
}
?>