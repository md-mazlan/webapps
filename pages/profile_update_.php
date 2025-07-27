<?php
include_once("../php/check_login_status.php");
include_once("../php/db_conx.php");
include_once("../php/init.php");

if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang_code = $_SESSION['lang'] ?? 'my';

include_once "../lang/lang_{$lang_code}.php";

$sql = "SELECT user_id, user_full_name, user_email FROM users WHERE user_id = '$log_user_id'";
$result = mysqli_query($db_conx, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $user_id = $row['user_id'];
    $user_full_name = $row['user_full_name'];
    $user_email = $row['user_email'];
} else {
    echo "No user found.";
    exit();
}


$is_complete_user_detail = false;
$full_name = "";
$gender = "";
$race = "";
$phone = "";
$birthday = "";
$address1 = "";
$address2 = "";
$area = "";
$postcode = "";
$city = "";
$state = "";

$sql_user_detail = "SELECT * FROM user_details WHERE user_id = '$log_user_id'";
$result_user_detail = mysqli_query($db_conx, $sql_user_detail);
if (mysqli_num_rows($result_user_detail) > 0) {
    $is_complete_user_detail = true;
    $row = mysqli_fetch_assoc($result_user_detail);
    $full_name = $row['full_name'];
    $gender = $row['gender'];
    $ethnic = $row['ethnic'];
    $phone = $row['phone'];
    $birthday = $row['birthday'];
    $address1 = $row['address1'];
    $address2 = $row['address2'];
    $area = $row['area'];
    $postal_code = $row['postal_code'];
    $city = $row['city'];
    $state = $row['state'];
}

$sql_ethnic = "SELECT * FROM sabah_ethnic_groups";
$query_ethnic = mysqli_query($db_conx, $sql_ethnic);
$ethnic_groups = [];
while ($row = mysqli_fetch_assoc($query_ethnic)) {
    $ethnic_groups[] = $row;
}

$sql_dun = "SELECT * FROM sabah_dun_seats";
$query_dun = mysqli_query($db_conx, $sql_dun);
$dun_seats = [];
while ($row = mysqli_fetch_assoc($query_dun)) {
    $dun_seats[] = $row;
}

$sql_regions = "SELECT * FROM malaysia_regions";
$query_regions = mysqli_query($db_conx, $sql_regions);
$regions = [];
while ($row = mysqli_fetch_assoc($query_regions)) {
    $regions[] = $row;
}

?>
<div data-script=" js/profile_update.js" data-style="css/profile.css">
    <div id="profile-content">
        <div class="container">
            <div class="profile-image-section">
                <div class="profile-image-box">
                    <!-- Placeholder SVG avatar -->
                    <img src="images/profile.jpg" alt="Profile Picture">
                    <div class="profile-camera" title="Change photo">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="13" r="3" />
                            <path d="M5 7h2l2-3h6l2 3h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                        </svg>
                    </div>
                </div>
                <div class="profile-actions">
                    <!-- <div class="profile-action">LOGO</div> -->
                    <div class="profile-action">UPLOAD DOCUMENTS</div>
                </div>
            </div>
            <form id="update_form" action="php/update_user_details.php" method="post" class="form">
                <div class="input-box">
                    <label><?php echo $lang["full_name"] ?></label>
                    <input type="text" name="full_name" placeholder="Enter full name" value="<?php echo $user_full_name; ?>" required />
                </div>
                <div class="input-box">
                    <label><?php echo $lang["ic_number"] ?></label>
                    <input type="text" placeholder="Enter IC Number" value="<?php echo $user_id; ?>" readonly />
                </div>

                <div class="gender-box">
                    <h3><?php echo $lang["gender"] ?></h3>
                    <div class="gender-option">
                        <div class="gender">
                            <input type="radio" name="gender" id="check-male"  value="m" checked />
                            <label for="check-male"><?php echo $lang["male"] ?></label>
                        </div>
                        <div class="gender">
                            <input type="radio" name="gender" id="check-female"  value="f" />
                            <label for="check-female"><?php echo $lang["female"] ?></label>
                        </div>
                    </div>
                </div>

                <div class="input-box">
                    <label><?php echo $lang["ethnic"] ?></label>
                    <select class="select-box" name="ethnic" required>
                        <option><?php echo $lang["select"] ?></option>
                        <?php
                        for ($i = 0; $i < count($ethnic_groups); $i++) {
                            $ethnic = $ethnic_groups[$i];
                            echo "<option value='{$ethnic['name']}'>{$ethnic['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="column">
                    <div class="input-box">
                        <label><?php echo $lang["phone_number"] ?></label>
                        <input type="number" name="phone" value="<?php echo $phone?>" placeholder="Enter phone number" required />
                    </div>
                    <div class="input-box">
                        <label><?php echo $lang["birthday"] ?></label>
                        <input type="date" name="birthday" value="<?php echo $birthday?>" placeholder="Enter birth date" required />
                    </div>
                </div>

                <div class="input-box">
                    <label><?php echo $lang["email"] ?></label>
                    <input type="text" name="email" placeholder="Enter email address" value="<?php echo $user_email; ?>" required />
                </div>

                <div class="input-box address">
                    <label><?php echo $lang["address"] ?></label>
                    <input type="text" name="address1" value="<?php echo $address1?>" placeholder="Enter street address" required />
                    <input type="text" name="address2" value="<?php echo $address2?>" placeholder="Enter street address line 2" />
                    <div class="column">
                        <input type="text" name="area" value="<?php echo $area?>" placeholder="Enter your region" required />
                        <input type="number" name="postal_code" value="<?php echo $postal_code?>" placeholder="Enter postal code" required />
                    </div>

                    <div class="column">
                        <input type="text" name="city" value="<?php echo $city?>" placeholder="Enter your city" required />
                        <div class="select-box">
                            <select name="state" required>
                                <option hidden><?php echo $lang["state"] ?></option>
                                <?php
                                foreach ($regions as $region) {
                                    $selected = "";
                                    if(strtolower($region['name']) == strtolower($state)){
                                        $selected = "selected";
                                    };
                                    echo "<option value='{$region['name']}' $selected>{$region['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <br>
                <h2><?php echo $lang["employment_details"] ?></h2>

                <div class="input-box">
                    <label><?php echo $lang["sector"] ?></label>
                    <div class="select-box">
                        <select name="sector" required>
                            <option hidden><?php echo $lang["select"] ?></option>
                            <option><?php echo $lang["government"] ?></option>
                            <option><?php echo $lang["private"] ?></option>
                            <option><?php echo $lang["self_employment"] ?></option>
                        </select>
                    </div>
                </div>

                <div class="input-box">
                    <label><?php echo $lang["employer_name"] ?></label>
                    <input type="text" placeholder="Enter employer name" value="" required />
                </div>

                <div class="input-box">
                    <label><?php echo $lang["employer_address"] ?></label>
                    <input type="text" placeholder="Employer Address" value="" required />
                </div>


                <br>
                <h2><?php echo $lang["member_option"] ?></h2>
                <div class="input-box">
                    <label>DUN</label>
                    <div class="select-box">
                        <select name="dun" required>
                            <option hidden>DUN</option>
                            <?php for ($i = 0; $i < count($dun_seats); $i++) {
                                $seat = $dun_seats[$i];
                                echo "<option value='{$seat['code']}'>{$seat['seat']}</option>";
                            } ?>
                        </select>
                    </div>
                </div>

                <div class="input-box">
                    <label>Shirt / vest size</label>
                    <div class="select-box">
                        <select name="shirt_size" required>
                            <option hidden><?php echo $lang["select"] ?> <?php echo $lang["size"] ?></option>
                            <option>S</option>
                            <option>M</option>
                            <option>L</option>
                            <option>XL</option>
                            <option>XXL</option>
                            <option>XXXL</option>
                        </select>
                    </div>
                </div>

                <button type="submit"><?php echo $lang["update"] ?></button>
            </form>
        </div>
    </div>

</div>