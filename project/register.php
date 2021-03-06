<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "big">
<?php
if (isset($_POST["register"])) {
    $email = null;
    $password = null;
    $confirm = null;
    $username = null;
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    if (isset($_POST["confirm"])) {
        $confirm = $_POST["confirm"];
    }
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
    }
    $isValid = true;
    if (strlen($username) <4){
	$isValid = false; 
	flash("Username too short! Must be at least 4 characters.");
   }
    if (strlen($username) >=25){
	$isValid = false;
	flash("Username too long! Can be up to 15 characters.");
} 
   
    if (strlen($password) < 5){
	$isValid = false;
	flash("Password too short! Must be at least 5 characters.");
    } 
    //check if passwords match on the server side
    if ($password == $confirm) {
        //not necessary to show
        //echo "Passwords match <br>";
    }
    else {
        flash("Passwords don't match");
        $isValid = false;
    }
    if (!isset($email) || !isset($password) || !isset($confirm)) {
        $isValid = false;
    }
    //TODO other validation as desired, remember this is the last line of defense
    if ($isValid) {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $db = getDB();
        if (isset($db)) {
            //here we'll use placeholders to let PDO map and sanitize our data
            $stmt = $db->prepare("INSERT INTO Users(email, username, password) VALUES(:email,:username, :password)");
            //here's the data map for the parameter to data
            $params = array(":email" => $email, ":username" => $username, ":password" => $hash);
            $r = $stmt->execute($params);
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") {
                flash("Successfully registered! Please login.");
            }
            else {
                if ($e[0] == "23000") {//code for duplicate entry
                    flash("Username or email already exists.");
                }
                else {
                    flash("An error occurred, please try again");
                }
            }
        }
    }
    else {
        flash( "There was a validation issue");
    }
}
//safety measure to prevent php warnings
if (!isset($email)) {
    $email = "";
}
if (!isset($username)) {
    $username = "";
}
?>
    <form method="POST" style = "height: 460px">
<div class = "heading2">
<h3>Register<h3>
</div>
        <input type="email" id="email" placeholder = "Email" name="email" required value="<?php safer_echo($email); ?>"/>
        <input type="text" id="user" name="username" placeholder = "Usename" required maxlength="60" value="<?php safer_echo($username); ?>"/>
        <input type="password" id="p1" name="password" placeholder = "Password" required maxlength="60"/>
        <input type="password" id="p2" name="confirm" placeholder ="Confirm Password" required maxlength="60"/>
        <input type="submit" name="register" value="Register"/>
    </form>
</div>
<?php require(__DIR__ . "/partials/flash.php");
