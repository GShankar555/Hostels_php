<?php
session_start();
require "includes/database_connect.php";
$property_id = $_GET['property_id'];
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$sql_1 = "SELECT *, p.id AS property_id, p.name AS property_name, c.name AS city_name FROM properties p INNER JOIN cities c ON p.city_id = c.id WHERE      p.id = $property_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) {
    echo "Something went wrong!";
    return;
}
$property = mysqli_fetch_assoc($result_1);
if ($user_id) {
    $sql_5 = "Select count(property_id) as count from bookings_users where property_id = $property_id and user_id=$user_id;";
    $result_5 = mysqli_query($conn, $sql_5);
    $count = mysqli_fetch_assoc($result_5);
} else {
    $count = 0;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    include "includes/head_links.php";
    ?>
    <link rel="stylesheet" href="css/bookings.css">
    <script src="https://kit.fontawesome.com/2081765cf8.js" crossorigin="anonymous"></script>
    <title>Bookings</title>
</head>

<body onload="clearForm()">
    <?php
    include "includes/header.php";
    ?>
    <div class="container">
        <h1>Registration</h1>
        <form method="post" id="booking-form" class="form" action="bookings_submit.php?property_id=<?= $property_id ?>">
            <label for="fname">First Name<i class="fa-solid fa-star-of-life"></i> :</label>
            <input type="text" name="name" placeholder="Enter First Name" required>
            <label for="lname">Last Name<i class="fa-solid fa-star-of-life"></i> :</label>
            <input type="text" name="lname" placeholder="Enter Last Name" required>
            <label for="Address">Address<i class="fa-solid fa-star-of-life"></i> :</label>
            <input type="text" placeholder="Address" name="address" required>
            <div class="labels">
                <label for="city">City<i class="fa-solid fa-star-of-life"></i> :</label>
                <label for="state">State<i class="fa-solid fa-star-of-life"></i> :</label>
                <label for="zip code">Zip code:</label>
            </div>
            <div class="inputs">
                <input type="text" placeholder="city" name="city" required>
                <input type="text" placeholder="state" name="state" required>
                <input type="number" placeholder="zip code" name="zip">
            </div>
            <label for="mobile">Mobile<i class="fa-solid fa-star-of-life"></i> :</label>
            <input type="number" name="number" placeholder="Enter Mobile Number" required minlength="10" maxlength="10">
            <label for="email">Email<i class="fa-solid fa-star-of-life"></i> :</label>
            <input type="text" placeholder="Email" name="email" required>
            <div class="labels">
                <label for="months">Tenure<i class="fa-solid fa-star-of-life"></i> :</label>
                <label for="gender">Gender<i class="fa-solid fa-star-of-life"></i> :</label>
            </div>
            <div class="inputs tg">
                <input type="number" name="months" placeholder="Enter number of months you want to stay" min="1" required>
                <select name="gender">
                    <option value="male" selected>Male</option>
                    <option value="female">Female
                    </option>
                </select>
                <input type="text" name="gender" value="<?php echo $property["gender"]; ?>" readonly>
                <div></div>
            </div>
            <div>
                <input type="checkbox" class="myCheckbox" required>
                <label for="checkbox">I Accept your terms and conditions</label>
                <br>
                <input type="checkbox" class="myCheckbox" required>
                <label for="checkbox">I shall pay the booking amount before 10 days of arrival</label>
            </div>
            <!-- <button type="submit" id="btn">Submit</button> -->
            <button type="submit">submit"</button>
        </form>
    </div>
    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
</body>
<script>
    var genderSelect = document.querySelector('select[name="gender"]');
    var genderInput = document.querySelector('input[name="gender"]');
    if (genderInput.value.toLowerCase() === 'unisex') {
        genderInput.style.display = "none";
    } else {
        genderSelect.style.display = "none";
    }
</script>
<script>
    // JavaScript function to clear all form fields
    function clearForm() {
        $('#booking-form')[0].reset();
    }
</script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    $("#booking-form").on("submit", function(e) {
        <?php if (!$user_id) { ?>
            e.preventDefault();
            alert("Please log in to book now.");
            $('#login-modal').modal('show');
        <?php } elseif ($count['count'] >= 1) { ?>
            e.preventDefault();
            alert("You have already booked this hostel.");
        <?php } else { ?>
            alert("Your booking is successful!!!");
        <?php } ?>
    });
</script>

</html>