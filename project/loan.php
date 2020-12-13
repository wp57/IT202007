<!DOCTYPE html>
<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
<?php
$db = getDB();
$u = [];
$id = get_user_id();
$stmt = $db->prepare("SELECT * from Accounts WHERE user_id = :id");
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<form method="POST" style = "height: 550px">
  <h3>Take Out a Loan<h3>
  <br>
  <h3>3% APY<h3>
  <br>
	<input id="amt" type="float" placeholder="Loan Amount" min="5.00" name="amt"/>
 <br>
        <select name="source">
            <?php foreach($u as $user): ?>
            <option value="" disabled selected>Account</option>  
	    <option value="<?= $user["id"]; ?>"><?= $user["account_number"]; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
	<input type="submit" name="save" value="Create"/>
</form>
<?php
if(isset($_POST["save"])){
  $db = getDB();
  $aNum = rand(000000000001, 999999999999);
  for ($x = strlen($aNum); $x < 12; $x++){
    $aNum = ("0" . $aNum);
  }
  $aType = "Loan";
  $user = get_user_id();
  $balance = $_POST["amt"];
  $apy = 0.03;
  if($balance >= 500){
    do {
      $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance, apy) VALUES(:aNum, :aType, :user, :balance, :apy)");
  	$r = $stmt->execute([
  		":aNum"=>$aNum,
  		":aType"=>$aType,
  		":user"=>$user,
      		":apy" => $apy,
      		":balance"=>$balance
      ]);
      $aNum = rand(000000000000, 999999999999);
      for ($x = strlen($aNum); $x< 12; $x++){
        $aNum = ("0" . $aNum);
      }
      $e = $stmt->errorInfo();
    } while ($e[0] == "23000");
    if ($r){
      $lastId = $db->lastInsertId();
  		flash("Your account was created successfully with account number: " . $aNum);
  	}
  	else{
  		$e = $stmt->errorInfo();
  		flash("Error creating: " . var_export($e, true));
  	}
   
   $source = $_POST["source"];
   $query = null;
   $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id like :q");
   $r2 = $stmt2->execute([":q" => "%$query%"]);
   if ($r2) {
          $res = $stmt2->fetchAll(PDO::FETCH_ASSOC);
   }
      
    foreach ($res as $r){
      if ($source == $r["id"])
          $a2tot = $r["balance"];
    }
  
   $query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `expected_total`) 
	VALUES(:p1a1, :p1a2, :p1change, :type, :a1tot), 
			(:p2a1, :p2a2, :p2change, :type, :a2tot)";
      
    $stmt = $db->prepare($query);
  	$stmt->bindValue(":p1a1", $lastId);
  	$stmt->bindValue(":p1a2", $source);
  	$stmt->bindValue(":p1change", $balance);
  	$stmt->bindValue(":type", "Deposit");
  	$stmt->bindValue(":a1tot", $balance);
  	//flip data for other half of transaction
  	$stmt->bindValue(":p2a1", $source);
  	$stmt->bindValue(":p2a2", $lastId);
  	$stmt->bindValue(":p2change", $balance);
  	$stmt->bindValue(":type", "Deposit");
  	$stmt->bindValue(":a2tot", $a2tot+$balance);
  	$result = $stmt->execute();
    if ($result) {
          flash("Transaction created successfully");
      }
    else {
         $e = $stmt->errorInfo();
         flash("Error creating: " . var_export($e, true));
    }
    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = Accounts.id)");
    $r = $stmt->execute();
	  die(header("Location: list_accounts.php"));
  }
  else {
    flash("Error: Loan amount must be $500.00 or more.");
  }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
