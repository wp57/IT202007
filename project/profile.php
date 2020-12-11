<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
<?php

//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You must be logged in to access this page");
    die(header("Location: login.php"));
}

$db = getDB();

$stmt = $db->prepare("SELECT first_name, last_name, visible FROM Users WHERE id = :id");
$stmt->execute([":id" => get_user_id()]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$first = $result["first_name"];
$last = $result["last_name"];
$visible = $result["visible"];


//save data if we submitted the form
if (isset($_POST["saved"])) {
    $isValid = true;
    //check if our email changed
    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        //TODO we'll need to check if the email is available
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Email already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
 $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;//default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            flash("Username already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        }
        else {
            $newUsername = $username;
        }
    }

    $newfirstName = get_firstName();
    if ((get_firstName() != $_POST["firstName"])) {
        $newFirstName = $_POST["firstName"];
    }

    $newLastName = get_lastName();
    if ((get_lastName() != $_POST["lastName"])) {
        $newLastName = $_POST["lastName"];
    }
    if ($isValid) {
        $stmt = $db->prepare("UPDATE Users set email = :email, username= :username, where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_user_id()]);
        if ($r) {
            flash("Updated profile");
        }
        else {
            flash("Error updating profile");
        }
        //password is optional, so check if it's even set
        //if so, then check if it's a valid reset request
    if (!empty($_POST["password"]) && !empty($_POST["confirm"]) && !empty($_POST["current"])) {
        $curr = $_POST["current"];
        $stmt = $db->prepare("SELECT password from Users WHERE id = :userid");
        $stmt->execute([":userid" => get_user_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result["password"])) {
                $password_hash_from_db = $result["password"];
                if(password_verify($curr, $password_hash_from_db))
                {
                        if ($_POST["password"] == $_POST["confirm"])
   {
                                if(strlen($_POST["password"]) >= 5)
                                {
                                        $password = $_POST["password"];
                                        $hash = password_hash($password, PASSWORD_BCRYPT);
                                        $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                                        $r = $stmt->execute([":id" => get_user_id(), ":password" => $hash]);
                                        if ($r) {
                                                flash("Reset Password");
                                        }
                                        else {
                                                flash("Error resetting password");
                                        }
                                }
                                else if(strlen($_POST["password"]) < 5)
                                {
                                        flash("New password must be at least 5 characters.");
                                }
                        }
                        else
                        {
                                flash("New passwords do not match.");
                        }
                }
                else
                {
                        flash("Current password is incorrect.");
                }
          }

}


//fetch/select fresh data in case anything changed
$stmt = $db->prepare("SELECT email, username from Users WHERE id = :id LIMIT 1");
$stmt->execute([":id" => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
            //let's update our session too
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
	 }
    }


if($isValid){
        $first2 = $_POST["firstName"];
        $last2 = $_POST["lastName"];
        $visible2 = $visible;
        if(isset($_POST["Public"])){
            $visible = $_POST["Public"];
        } else {
            $visible = "Private";
        }


        if($first == $first2){
            $stmt = $db->prepare("UPDATE Users set first_name = :first WHERE id = :id");
            $stmt->execute([":first" => $first2, ":id" => get_user_id()]);
        }

        if($last == $last2){
            $stmt = $db->prepare("UPDATE Users set last_name = :last WHERE id = :id");
            $stmt->execute([":last" => $last2, ":id" => get_user_id()]);
        }

        if($visible == $visible2){
            $stmt = $db->prepare("UPDATE Users set visible = :visibility WHERE id = :id");
            $stmt->execute([":visibility" => $visible2, ":id" => get_user_id()]);
        }
    }
    die(header("Location: profile.php"));
}

?>
    <form method="POST">
<div class="heading"

    <h3>Edit Your Profile</h3>
</div>

        <input type="text" placeholder="First Name" name="firstName" value="<?php safer_echo(get_firstName()); ?>"/>
        <br>
        <input type="text" name="lastName" placeholder="Last Name" value="<?php safer_echo(get_lastName()); ?>"/>
        <br>
	<input type="email" placeholder="Email" name="email" value="<?php safer_echo(get_email()); ?>"/>
        <br>
	<select name="visible">
            <option value="Public">Public</option>
            <option value="Private">Private</option>
        </select>
	<input type="text" placeholder="Username" maxlength="60" name="username" value="<?php safer_echo(get_username()); ?>"/>
        <!-- DO NOT PRELOAD PASSWORD-->
        <br>
	<input type="password" placeholder="Current Password" name="current"/>
        <br>
	<input type="password" placeholder="New Password" name="password"/>
        <br>
	<input type="password" placeholder="Confirm Password" name="confirm"/>
        <br>
	<input type="submit" name="saved" value="Save Profile"/>
    </form>
</div>
<?php require(__DIR__ . "/partials/flash.php");
