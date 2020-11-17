<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<form method="POST">
  <label>Create Checking Account</label>
  <label>Balance</label>
	<input type="float" min="5.00" name="balance"/>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	$aNum = rand(000000000000, 999999999999);
	$user = get_user_id();        
        $aType = "Checking";
        $balance = $_POST["balance"];
	$db = getDB();
    if($balance >= 5){
    do {
      $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance) VALUES(:aNum, :aType, :user, :balance)");
  	$r = $stmt->execute([
  		":aNum"=>$aNum,
  		":aType"=>$aType,
  		":user"=>$user,
                ":balance"=>$balance
      ]);
      $aNum = rand(000000000000, 999999999999);
      $e = $stmt->errorInfo();
    }
    while($e[0] == "23000");
    if($r){
  		flash("Created successfully with id: " . $db->lastInsertId());	
    }
  	else{
  		$e = $stmt->errorInfo();
  		flash("Error creating: " . var_export($e, true));
  	}
  }
  else
  {
    flash("Minimum deposit of $5.00 required!");
  }
}
?>
<?php require(__DIR__ . "/partials/flash.php");
