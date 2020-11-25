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
  $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id like :q");
  $r2 = $stmt2->execute([":q" => "%$query%"]);
  if ($r2) {
        $res = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

  $a1tot = null;
  $a2tot = null;
  foreach($res as $r)
  {
    if($account1 == $r["id"])
        $a1tot = $r["balance"];
    if($account2 == $r["id"])
      $a2tot = $r["balance"];
flash($a1tot);
  
}
 
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
    $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = Accounts.id)");
    $r = $stmt->execute([
       ":balance"=>($a1tot+$amountChange),
       ":id"=>$account1
  	]);
    $r = $stmt->execute([
       ":balance"=>($a2tot-$amountChange),
       ":id"=>$account2
  	]);

	return $result;
  }
flash($a1tot);
if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $dest = $_POST["dest"];
    $memo = $_POST["memo"];

    if ($amount >= 0.0){
	$isVal = true;
    }
    else {
	flash("Error: Please enter a positive value.");
    }
    if ($isVal) {
	$stmt = $db->prepare("SELECT balance FROM Accounts WHERE id = :id");
	$r = $stmt->execute([":id" => "$id"]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $balance = (float)$res["balance"];
    }
    if ($amount > $balance) {
	$isVal = false;
    	flash("Error: You do not have enough money in your account to make a withdrawal of this amount."); 
    }

    $user = get_user_id();
    $db = getDB();
    $sql = "SELECT DISTINCT id from Accounts where account_number = '000000000000'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetch();
    $world = $result["id"];
    do_bank_action($dest, $world, ($amount * -1), $memo);
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
