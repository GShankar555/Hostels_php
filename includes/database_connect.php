<?php
$conn = mysqli_connect('127.0.0.1','root','Aishugowri@8309','pglife');
$x =mysqli_connect_error();
echo $x;
if (mysqli_connect_errno()) {
    // Throw error message based on ajax or not
    echo $x;
    return;
}