<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="shiftRight">
<?php
$db = getDB();
$id = get_user_id();
$u = [];
$stmt = $db->prepare("SELECT * FROM Accounts WHERE user_id = :id");
$r = $stmt->execute([":id" => "$id"]);
if ($r) {
	$u = $stmt->fetchAll(PDO::FETCH_ASSOC); 
}
?>
    <h3>Make a Withdrawal</h3>
    <form method="POST">
        <label>Account</label>
        <br>
        <select name="dest">
            <?php foreach($u as $user): ?>
               <option value="<?= $user[id]; ?>"><?= $user["account_number"]; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Amount</label>
        <br>
        <input type="float" min="0.00" name="amount"/>
        <br>
        <label>Memo</label>
        <br>
        <input type="text" placeholder="Optional message for your withdrawal" name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>
<?php
function do_bank_action($account1, $account2, $amountChange, $memo){
  $db = getDB();
  $query = null;
  $stmt2 = $db->prepare("SELECT SUM(amount) as balance FROM Transactions WHERE Transactions.act_src_id = :id");
    $r2 = $stmt2->execute([
       ":id"=>$account1
      ]);
	$result = $stmt2->fetch(PDO::FETCH_ASSOC);
	$a1tot = (int)$result["balance"];    
$r2 = $stmt2->execute([
       ":id"=>$account2
      ]);
	$result = $stmt2->fetch(PDO::FETCH_ASSOC);
	$a2tot = (int)$result["balance"];
 
  
  if($a1tot+$amountChange >= 0)
  {
  	$query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `expected_total`, `memo`) 
  	VALUES(:p1a1, :p1a2, :p1change, :type, :a1tot, :memo), 
  			(:p2a1, :p2a2, :p2change, :type, :a2tot, :memo)";
  	
  	$stmt = $db->prepare($query);
  	$stmt->bindValue(":p1a1", $account1);
  	$stmt->bindValue(":p1a2", $account2);
  	$stmt->bindValue(":p1change", $amountChange);
  	$stmt->bindValue(":type", "Withdraw");
  	$stmt->bindValue(":a1tot", $a1tot+$amountChange);
    $stmt->bindValue(":memo", $memo);
  	//flip data for other half of transaction
  	$stmt->bindValue(":p2a1", $account2);
  	$stmt->bindValue(":p2a2", $account1);
  	$stmt->bindValue(":p2change", ($amountChange*-1));
  	$stmt->bindValue(":type", "Withdraw");
  	$stmt->bindValue(":a2tot", $a2tot-$amountChange);
    $stmt->bindValue(":memo", $memo);
  	$result = $stmt->execute();
    if ($result) {
          flash("Created successfully with id: " . $db->lastInsertId());
      }
      else {
          $e = $stmt->errorInfo();
          flash("Error creating: " . var_export($e, true));
      }
   
    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = :id) where id = :id");
    $r = $stmt->execute([
       ":id"=>$account1
  	]);
    $r = $stmt->execute([
       ":id"=>$account2
  	]);

	return $result;
  }
  else{
    flash("Error: You cannot withdraw more than you have.");
	echo ($a1tot); 
 }
}
if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $dest = $_POST["dest"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $db = getDB();
    $sql = "SELECT DISTINCT id from Accounts where account_number = '000000000000'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetch();
    $world = $result["id"];
    if ($amount > 0) {	
    do_bank_action($dest, $world, ($amount * -1), $memo);
    }
    else { 
    flash("Error: Value must be positive!");
    }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
