<?php
session_start();
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}
$user_id = $_SESSION['user_id'];

$sql_1 = "SELECT * FROM users WHERE id = $user_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) {
    echo "Something went wrong!";
    return;
}
$user = mysqli_fetch_assoc($result_1);
if (!$user) {
    echo "Something went wrong!";
    return;
}

$sql_2 = "SELECT * 
            FROM interested_users_properties iup
            INNER JOIN properties p ON iup.property_id = p.id
            WHERE iup.user_id = $user_id";
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) {
    echo "Something went wrong!";
    return;
}
$sql_3 = "SELECT * FROM bookings_users WHERE user_id = $user_id";
$result_3 = mysqli_query($conn,$sql_3);
if(!$result_3){
    echo "Something went wrong!";
    return;
}
$booking_users = mysqli_fetch_all($result_3,MYSQLI_ASSOC);
$interested_properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);
$sql_4 = "SELECT property_id FROM bookings_users WHERE user_id = $user_id";
$result_4 = mysqli_query($conn,$sql_4);
if(!$result_4){
    echo "Something went wrong!";
    return;
}
$properties_id = array();

while ($row = mysqli_fetch_assoc($result_4)) {
    $properties_id[] = $row['property_id'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>
    <?php
    include "includes/head_links.php";
    ?>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link href="css/dashboard.css" rel="stylesheet" />
</head>

<body>
    <?php
    include "includes/header.php";
    ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item">
                <a href="index.php">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Dashboard
            </li>
        </ol>
    </nav>
    <div class="my-profile page-container">
        <h1>My Profile</h1>
        <div class="row">
            <div class="col-md-3 profile-img-container">
                <i class="fas fa-user profile-img"></i>
            </div>
            <div class="col-md-9">
                <div class="row no-gutters justify-content-between align-items-end">
                    <div class="profile">
                        <div class="name"><?= $user['full_name'] ?></div>
                        <div class="email"><?= $user['email'] ?></div>
                        <div class="phone"><?= $user['phone'] ?></div>
                        <div class="college"><?= $user['college_name'] ?></div>
                    </div>
                    <div class="edit">
                        <a class="edit-profile" data-toggle="modal" data-target="#edit-modal">Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab">
    <button class="tablinks active" onclick="openTab(event, 'bookings')">My Bookings</button>
    <button class="tablinks" onclick="openTab(event, 'my-interested-properties')">My Interests</button>
</div>
<?php
    if (count($booking_users) > 0) {
    ?>
    <div id="bookings" class="tabcontent">
    <!-- <div> -->
    <div class="page-container">
            <h1>My Bookings</h1>
            <div class="scroll">
            <?php
                    foreach ($properties_id as $p_id) {
                        $property_images = glob("img/properties/" . $p_id . "/*");
                        $sql_5 = "SELECT * FROM properties WHERE id = $p_id";
                        $result_5 = mysqli_query($conn, $sql_5);
                        if (!$result_5) {
                            echo "Something went wrong!";
                            return;
                        }
                        $property = mysqli_fetch_assoc($result_5);
                        if (!$property) {
                            echo "Something went wrong!";
                            return;
                        }
                    ?>
                        <div class="property-card property-id-<?= $p_id ?> row">
                            <div class="image-container col-md-4">
                                <img src="<?= $property_images[0] ?>" />
                            </div>
                            <div class="content-container col-md-8">
                                <div class="row no-gutters justify-content-between">
                                    <?php
                                    $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                                    $total_rating = round($total_rating, 1);
                                    ?>
                                    <div class="star-container" title="<?= $total_rating ?>">
                                        <?php
                                        $rating = $total_rating;
                                        for ($i = 0; $i < 5; $i++) {
                                            if ($rating >= $i + 0.8) {
                                        ?>
                                                <i class="fas fa-star"></i>
                                            <?php
                                            } elseif ($rating >= $i + 0.3) {
                                            ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php
                                            } else {
                                            ?>
                                                <i class="far fa-star"></i>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="interested-container">
                                        <i class="is-interested-image fas fa-heart" property_id="<?= $property['id'] ?>"></i>
                                    </div>
                                </div>
                                <div class="detail-container">
                                    <div class="property-name"><?= $property['name'] ?></div>
                                    <div class="property-address"><?= $property['address'] ?></div>
                                    <div class="property-gender">
                                        <?php
                                        if ($property['gender'] == "male") {
                                        ?>
                                            <img src="img/male.png">
                                        <?php
                                        } elseif ($property['gender'] == "female") {
                                        ?>
                                            <img src="img/female.png">
                                        <?php
                                        } else {
                                        ?>
                                            <img src="img/unisex.png">
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="rent-container col-6">
                                        <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                                        <div class="rent-unit">per month</div>
                                    </div>
                                    <div class="button-container col-6">
                                        <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
<?php }else {
    ?>
    <div id="bookings" class="bookings tabcontent">
        <div class="page-container">
            <h1>No Bookings!</h1>
            <p>You currently have no properties in your bookings. Start exploring and book properties!</p>
        </div>
    </div>
    <?php
}
?>
<?php
    if (count($interested_properties) > 0) {
    ?>
    <div id="my-interested-properties" class="my-interested-properties tabcontent" >
            <div class="page-container">
                <h1>My Interested Properties</h1>
                <div class="scroll">
                    <?php
                    foreach ($interested_properties as $property) {
                        $property_images = glob("img/properties/" . $property['id'] . "/*");
                    ?>
                        <div class="property-card property-id-<?= $property['id'] ?> row">
                            <div class="image-container col-md-4">
                                <img src="<?= $property_images[0] ?>" />
                            </div>
                            <div class="content-container col-md-8">
                                <div class="row no-gutters justify-content-between">
                                    <?php
                                    $total_rating = ($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3;
                                    $total_rating = round($total_rating, 1);
                                    ?>
                                    <div class="star-container" title="<?= $total_rating ?>">
                                        <?php
                                        $rating = $total_rating;
                                        for ($i = 0; $i < 5; $i++) {
                                            if ($rating >= $i + 0.8) {
                                        ?>
                                                <i class="fas fa-star"></i>
                                            <?php
                                            } elseif ($rating >= $i + 0.3) {
                                            ?>
                                                <i class="fas fa-star-half-alt"></i>
                                            <?php
                                            } else {
                                            ?>
                                                <i class="far fa-star"></i>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="interested-container">
                                        <i class="is-interested-image fas fa-heart" property_id="<?= $property['id'] ?>"></i>
                                    </div>
                                </div>
                                <div class="detail-container">
                                    <div class="property-name"><?= $property['name'] ?></div>
                                    <div class="property-address"><?= $property['address'] ?></div>
                                    <div class="property-gender">
                                        <?php
                                        if ($property['gender'] == "male") {
                                        ?>
                                            <img src="img/male.png">
                                        <?php
                                        } elseif ($property['gender'] == "female") {
                                        ?>
                                            <img src="img/female.png">
                                        <?php
                                        } else {
                                        ?>
                                            <img src="img/unisex.png">
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="row no-gutters">
                                    <div class="rent-container col-6">
                                        <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                                        <div class="rent-unit">per month</div>
                                    </div>
                                    <div class="button-container col-6">
                                        <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
} else {
    ?>
    <div id="my-interested-properties" class="my-interested-properties tabcontent">
        <div class="page-container">
            <h1>No Interested Properties</h1>
            <p>You currently have no properties in your interests. Start exploring and add properties to your interests!</p>
        </div>
    </div>
    <?php
}
?>
    <div class="space"></div>
    <?php
    include "includes/edit_modal.php";
    include "includes/footer.php";
    ?>
</body>
<script>
     $(document).ready(function() {
            // Add an event listener to the "Edit Profile" link
            $('.edit-profile').on('click', function() {
                // Set values in the form fields in the modal
                $('#edit-modal input[name="full_name"]').val('<?php echo $user['full_name']?>');
                $('#edit-modal input[name="phone"]').val('<?php echo $user['phone']?>');
                $('#edit-modal input[name="email"]').val('<?php echo $user['email']?>');
                $('#edit-modal').modal('show');
            });
     });
</script>
<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace("active", "");
        }
        document.getElementById(tabName).style.display = "contents";
        evt.currentTarget.className += " active";
            }
</script>
</html>