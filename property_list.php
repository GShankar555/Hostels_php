<?php
session_start();
require "includes/database_connect.php";
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$city_name = $_GET["city"];
$user_is_logged_in = isset($_SESSION['user_id']);
$sql_1 = "SELECT * FROM cities WHERE name = '$city_name'";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) {
    echo "Something went wrong!";
    return;
}
$city = mysqli_fetch_assoc($result_1);
if (!$city) {
    echo "Sorry! We do not have any PG listed in this city.";
    return;
}
$city_id = $city['id'];
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
if ($sort == 'highest_rent') {
    $sql_2 = "SELECT * FROM properties WHERE city_id = $city_id ORDER BY rent DESC";
} elseif ($sort == 'lowest_rent') {
    $sql_2 = "SELECT * FROM properties WHERE city_id = $city_id ORDER BY rent ASC";
} else {
    $sql_2 = "SELECT * FROM properties WHERE city_id = $city_id";
}
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) {
    echo "Something went wrong!";
    return;
}
$properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);
$sql_3 = "SELECT user_id,property_id,city_id
            FROM interested_users_properties iup
            INNER JOIN properties p ON iup.property_id = p.id
            WHERE p.city_id = $city_id";
$result_3 = mysqli_query($conn, $sql_3);
if (!$result_3) {
    echo "Something went wrong!";
    return;
}
$interested_users_properties = mysqli_fetch_all($result_3, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in <?php echo $city_name ?> | PG Life</title>
    <?php
    include "includes/head_links.php";
    ?>
    <link href="css/property_list.css" rel="stylesheet" />
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
                <?php echo $city_name; ?>
            </li>
        </ol>
    </nav>
    <div class="page-container">
        <div class="filter-bar row justify-content-around">
            <div class="col-auto" data-toggle="modal"
            data-target="#filter-modal">
                <img src="img/filter.png" alt="filter" />
                <span>Filter</span>
            </div>
            <div class="col-auto">
                <a href="?city=<?= urlencode($city_name) ?>&sort=highest_rent">
                    <img id="high-rent" src="img/desc.png" alt="sort-desc" />
                    Highest rent first
                </a>
            </div>
            <div class="col-auto">
                <a href="?city=<?= urlencode($city_name) ?>&sort=lowest_rent">
                    <img src="img/asc.png" alt="sort-asc" />
                    Lowest rent first
                </a>
            </div>
        </div>
        <?php
        foreach ($properties as $property) {
            $property_images = glob("img/properties/" . $property['id'] . "/*");
        ?>
            <div class="property-card row" data-gender="<?= $property['gender'] ?>">
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
                            <?php
                            $interested_users_count = 0;
                            $is_interested = false;
                            foreach ($interested_users_properties as $interested_user_property) {
                                if ($interested_user_property['property_id'] == $property['id']) {
                                    $interested_users_count++;
                                    if ($interested_user_property['user_id'] == $user_id) {
                                        $is_interested = true;
                                    }
                                }
                            }
                            if ($is_interested) {
                            ?>
                                <i class="fas fa-heart" id="<?=$property['id']?>"></i>
                            <?php
                            } else {
                            ?>
                                <i class="far fa-heart" id="<?=$property['id']?>"></i>
                            <?php
                            }
                            ?>
                            <div class="interested-text"><?= $interested_users_count ?> interested</div>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= $property['name'] ?></div>
                        <div class="property-address"><?= $property['address'] ?></div>
                        <div class="property-gender">
                            <?php
                            if ($property['gender'] == "male") {
                            ?>
                                <img src="img/male.png" />
                            <?php
                            } elseif ($property['gender'] == "female") {
                            ?>
                                <img src="img/female.png" />
                            <?php
                            } else {
                            ?>
                                <img src="img/unisex.png" />
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
        
        if (count($properties) == 0) {
        ?>
            <div class="no-property-container">
                <p>No PG to list</p>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="filter-heading">Filters</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Gender</h5>
                    <hr />
                    <div>
                    <button id="btn-no-filter" class="btn btn-outline-dark btn-active">
                    No Filter
                    </button>
                    <button id="btn-unisex" class="btn btn-outline-dark">
                    <i class="fas fa-venus-mars"></i>Unisex
                    </button>
                    <button id="btn-male" class="btn btn-outline-dark">
                    <i class="fas fa-mars"></i>Male
                    </button>
                    <button id="btn-female" class="btn btn-outline-dark">
                    <i class="fas fa-venus"></i>Female
                    </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-success">Okay</button>
                </div>
            </div>
        </div>
    </div>
    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
</body>
<script>
   function filterProperties(gender) {
      var propertyCards = document.querySelectorAll('.property-card');
      var filterButtons = document.querySelectorAll('.btn');
      filterButtons.forEach(function(btn) {
         btn.classList.remove('btn-active');
      });
      document.getElementById('btn-' + gender).classList.add('btn-active');
      propertyCards.forEach(function(card) {
         var cardGender = card.getAttribute('data-gender');
         var cardDisplay = 'flex';
         if (gender !== 'no-filter' && gender !== cardGender) {
            cardDisplay = 'none';
         }
         card.style.display = cardDisplay;
      });
}
   document.getElementById('btn-no-filter').addEventListener('click', function() {
      filterProperties('no-filter');
   });
   document.getElementById('btn-unisex').addEventListener('click', function() {
      filterProperties('unisex');
   });
   document.getElementById('btn-male').addEventListener('click', function() {
      filterProperties('male');
   });
   document.getElementById('btn-female').addEventListener('click', function() {
      filterProperties('female');
   });
</script>
<script>
$(document).ready(function() {
    $(".fa-heart").on("click", function() {
        <?php if ($user_is_logged_in) { ?>
            var iconId = $(this).attr('id');
            $.ajax({
                type: 'POST',
                url: 'update_interest.php',
                data: { property_id: iconId, user_id: <?= $user_id ?> },
                success: function(response) {
                    location.reload();
                },
                error: function(jqXHR, textStatus, errorThrown) {
        console.error('AJAX error:', textStatus, errorThrown);
        alert('Error updating the interested count.');
    }});
        <?php } else { ?>
            alert("Please log in to show your interest.");
            $('#login-modal').modal('show');
        <?php } ?>
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</html>