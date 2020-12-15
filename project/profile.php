<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
    <?php
    if (!is_logged_in()) {
        //this will redirect to login and kill the rest of this script (prevent it from executing)
        flash("You must be logged in to access this page");
        die(header("Location: login.php"));
    }

  $id = get_user_id();
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
$db = getDB();
    $stmt = $db->prepare("SELECT email, username, first_name, last_name, visible from Users WHERE id = :id LIMIT 1");
    $stmt->execute([":id" => $id]);
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    $vis = $results['visible'];

        if (isset($_POST["saved"])) {
            $isValid = true;
            //check if our email changed
            $newEmail = get_email();
            if (get_email() != $_POST["email"]) {
                //TODO we'll need to check if the email is available
                $email = $_POST["email"];
                $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where id = :id");
                $stmt->execute([":id" => $id]);
                $results = $stmt->fetch(PDO::FETCH_ASSOC);

                $inUse = 1;//default it to a failure scenario
                if ($results && isset($results["InUse"])) {
                    try {
                        $inUse = intval($results["InUse"]);
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
                else if(strlen($username) >= 5)
                {
                    $newUsername = $username;
                }
                else
                {
                    flash("New username must be at least 5 characters.");
                    $isValid = false;
                }

            }

            $newFirstName = get_firstName();
            if ((get_firstName() != $_POST["firstName"])) {
                $newFirstName = $_POST["firstName"];
            }

            $newLastName = get_lastName();
            if ((get_lastName() != $_POST["lastName"])) {
                $newLastName = $_POST["lastName"];
            }
            $_SESSION["user"]["visible"] = $_POST["visible"];
	    $vis = $_SESSION["user"]["visible"];

            if (($vis != $_POST["visible"])) {
                $vis = $_POST["visible"];
           }
            if ($isValid) {
                $stmt = $db->prepare("UPDATE Users set email = :email, username= :username, first_name= :firstName, last_name= :lastName, visible = :visible where id = :id");
                $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername,":firstName" => $newFirstName, ":lastName" => $newLastName, ":visible" => $vis, ":id" => get_user_id()]);
                if ($r) {
                    flash("Updated profile");
                }
                else {
                    flash("Error updating profile");
                }

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
                                if(strlen($_POST["password"]) >= 8)
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
                $stmt = $db->prepare("SELECT email, username, first_name, last_name, visible from Users WHERE id = :id LIMIT 1");
                $stmt->execute([":id" => get_user_id()]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    $email = $result["email"];
                    $username = $result["username"];
                    $firstName = $result["first_name"];
                    $lastName = $result["last_name"];
                    $vis = $result["visible"];
                    //let's update our session too
                    $_SESSION["user"]["email"] = $email;
                    $_SESSION["user"]["username"] = $username;
                    $_SESSION["user"]["first_name"] = $firstName;
                    $_SESSION["user"]["last_name"] = $lastName;
                    $_SESSION["user"]["visible"] = $vis;
                }
            }
            else {
                //else for $isValid, though don't need to put anything here since the specific failure will output the message
            }

    }

    ?>
<?php if($vis == 'Public' && $id != get_user_id()): ?>
     <div class="heading"
        <h3>Profile</h3>
</div>
<?php safer_echo("First Name: " . $result['first_name']); ?>
<?php safer_echo("Last Name: " . $result['last_name']); ?>

    <?php safer_echo("Username: " . get_username()); ?>
<?php endif; ?>
<?php if($id == get_user_id()): ?>
 
 <form method="POST" style = "height: 700px; width: 400px;">
  <div class="heading"
        <h3>Edit Your Profile</h3>
</div>
   <input type="text" placeholder="First Name" name="firstName" value="<?php safer_echo(get_firstName()); ?>"/>
        <br>
        <input type="text" name="lastName" placeholder="Last Name" value="<?php safer_echo(get_lastName()); ?>"/>
        <br>
	<input type="email" placeholder="Email" name="email" value="<?php safer_echo(get_email()); ?>"/>
        <br>
	<input type="text" placeholder="Username" maxlength="60" name="username" value="<?php safer_echo(get_username()); ?>"/>
	<!-- DO NOT PRELOAD PASSWORD-->
    <input type="password" placeholder="Current Password" name="current"/>
    <br>
    <input type="password" placeholder="New Password" name="password"/>
    <br>
    <input type="password" placeholder="Confirm Password" name="confirm"/>
    <br>
    <select name="visible">
        <option value="" disabled selected>Privacy</option>
        <option value="Public">Public</option>
        <option value="Private">Private</option>
    </select>
    <input type="submit" name="saved" value="Save Profile"/>
<?php endif; ?>
</form>
</div>
<?php require(__DIR__ . "/partials/flash.php");
