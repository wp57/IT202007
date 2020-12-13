<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "big">

<form method="POST" style = "height: 400px; width: 360px;">
<div class = "heading2">
<h3>Create Checking Account</h3>
</div>

  <br>
	<input type="float" placeholder = "Balance" min="5.00" name="balance"/>
 <br>
	<input type="submit" name="save" value="Create"/>
</form>

<html>
<body>
<p id="apy"></p>
  <script>
    function myFunction() {
      var bal = document.getElementByName("balance")[0].value;
      if(bal >= 5)
        document.getElementById("apy").innerHTML = "Your APY is 0.03%";
      else
        document.getElementById("apy").innerHTML = "Your balance must be more than $5.00 in order to calculate your APY.";
    }
  </script>
</body>
</html>

<?php
if(isset($_POST["save"])){
	$db = getDB();
	$aNum = rand(000000000000, 999999999999);
	for($x = strlen($aNum); $x < 12; $x++){
		$aNum = ("0" . $aNum);
	}
	$aType = "Savings";
	$user = get_user_id();
        $balance = $_POST["balance"];
	$apy = 0.03; 
    if($balance >= 5){
    do {
      $db = getDB();
      $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance, apy) VALUES(:aNum, :aType, :user, :balance, :apy)");
  	$r = $stmt->execute([
  		":aNum"=>$aNum,
  		":aType"=>$aType,
  		":user"=>$user,
                ":balance"=>$balance,
	        ":apy"=>$apy
      ]);
      $aNum = rand(000000000000, 999999999999);
      for($x = strlen($aNum); $x < 12; $x++){
        $aNum = ("0" . $aNum);
      }
       
      $e = $stmt->errorInfo();
    }
    while($e[0] == "23000");
    if($r){
  		flash("Your checking account was successfully created with id: " . $db->lastInsertId() . "!");
                die(header("Location: list_accounts.php"));	
    }
  	else{
  		$e = $stmt->errorInfo();
  		flash("Sorry, there was an error creating: " . var_export($e, true));
  	}
  $query = null;
   $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id like :q");
    $r2 = $stmt2->execute([":q" => "%$query%"]);
    if ($r2) {
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
          flash("Your transaction was created successfully with id: " . $db->lastInsertId());
      }
    else {
         $e = $stmt->errorInfo();
         flash("Sorry, there was an error creating: " . var_export($e, true));
    }
    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = Accounts.id) where id = :id");
    $r = $stmt->execute([
       ":balance"=>($a1tot+$amountChange),
       ":id"=>$account1
      ]);
    $r = $stmt->execute([
       ":balance"=>($a2tot-$amountChange),
       ":id"=>$account2
      ]);
	die(header("Location: list_accounts.php"));
  }
  else
  {
    flash('Balance must be $5.00 or more! Please try again.');
  }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");

