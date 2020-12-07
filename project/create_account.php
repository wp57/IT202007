<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<form method="POST">
<div class="heading3"

    <h3>Create an Account</h3>
</div>

        <input type="text" placeholder="Account Number" minlength="12"  name="aNum"/>
  <br>
        <input type="text" placeholder="Account Type" name="aType"/>
 <br>
        <input type="float" placeholder="Balance" min="0.00" name="balance"/>
 <br>
        <input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
        //TODO add proper validation/checks
        $aNum = $_POST["aNum"];
  $aType = $_POST["aType"];
        $user = get_user_id();
  $balance = $_POST["balance"];
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance) VALUES(:aNum, :aType, :user, :balance)");
        $r = $stmt->execute([
                ":aNum"=>$aNum,
                ":aType"=>$aType,
                ":user"=>$user,
    ":balance"=>$balance
        ]);
        if($r){
                flash("Created successfully with id: " . $db->lastInsertId());
        }
        else{
                $e = $stmt->errorInfo();
                flash("Error creating: " . var_export($e, true));
        }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
