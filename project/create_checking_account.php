<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<h3>Create Checking Account</h3>
<form method="POST">
  <label>Balance</label>
  <br>
	<input type="float" min="5.00" name="balance"/>
 <br>
	<input type="submit" name="save" value="Create"/>
</form>

<?php
if(isset($_POST["save"])){
	$db = getDB();
	$aNum = rand(000000000000, 999999999999);
	for($x = strlen($aNum); $x < 12; $x++){
		$aNum = ("0" . $aNum);
	}
	$aType = "Checking";
	$user = get_user_id();
        $balance = $_POST["balance"];
    if($balance >= 5){
    do {
      $db = getDB();
      $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance) VALUES(:aNum, :aType, :user, :balance)");
  	$r = $stmt->execute([
  		":aNum"=>$aNum,
  		":aType"=>$aType,
  		":user"=>$user,
                ":balance"=>0
      ]);
      $aNum = rand(000000000000, 999999999999);
      for($x = strlen($aNum); $x < 12; $x++){
        $aNum = ("0" . $aNum);
      }
       
      $e = $stmt->errorInfo();
    }
    while($e[0] == "23000");
    if($r){
  		flash("Created successfully with id: " . $db->lastInsertId() . "!");
                die(header("Location: list_accounts.php"));	
    }
  	else{
  		$e = $stmt->errorInfo();
  		flash("Error creating: " . var_export($e, true));
  	}
  $query = null;
   $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id like :q");
    $r2 = $stmt2->execute([":q" => "%$query%"]);
    if ($or2) {
          $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
      
      }
   $a1tot = null;
  foreach($results as $r)
  {
    if($r["id"] == 0)
        $a1tot = $r["balance"];
  }
    
   $query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `expected_total`) 
	VALUES(:p1a1, :p1a2, :p1change, :type, :a1tot), 
			(:p2a1, :p2a2, :p2change, :type, :a2tot)";
      
    $stmt = $db->prepare($query);
  	$stmt->bindValue(":p1a1", 0);
  	$stmt->bindValue(":p1a2", $lastId);
  	$stmt->bindValue(":p1change", ($balance*-1));
  	$stmt->bindValue(":type", "Deposit");
  	$stmt->bindValue(":a1tot", $a1tot-$balance);
  	//flip data for other half of transaction
  	$stmt->bindValue(":p2a1", $lastId);
  	$stmt->bindValue(":p2a2", 0);
  	$stmt->bindValue(":p2change", $balance);
  	$stmt->bindValue(":type", "Deposit");
  	$stmt->bindValue(":a2tot", $balance);
  	$result = $stmt->execute();
    if ($result) {
          flash("Transaction created successfully with id: " . $db->lastInsertId());
      }
    else {
         $e = $stmt->errorInfo();
         flash("Error creating: " . var_export($e, true));
    }
    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = Accounts.id)");
    $r = $stmt->execute();
	die(header("Location: list_accounts.php"));
  }
  else
  {
    flash('Balance must be $5.00 or more');
  }
}
?>
</drift>
<?php require(__DIR__ . "/partials/flash.php");
