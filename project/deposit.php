<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
$db = getDB();
$sql = "SELECT DISTINCT acc.id, acc.account_number, Users.id from Accounts as acc JOIN Users where acc.user_id = Users.id";
$stmt = $db->prepare($sql);
$stmt->execute();
$users=$stmt->fetchAll();
?>
    <h3>Deposit Transaction</h3>
    <form method="POST">
        <label>Account</label>
        <br>
        <select name="source">
            <?php foreach($users as $user): ?>
             <?php if ($user["id"] == get_user_id()): ?>
              <option value="<?= $user['id']; ?>"><?= $user['id']; ?></option>
              <?php endif; ?>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Amount</label>
        <br>
        <input type="float" min="0.00" name="amount"/>
        <br>
        <label>Memo</label>
        <br>
        <input type="text" placeholder-"Optional message for your deposit" name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
function do_bank_action($account1, $account2, $amountChange, $memo){
  $db = getDB();
  $query = null;
  $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id = :q");
  $r2 = $stmt2->execute([":q" => "$query"]);
  if ($r2) {
        $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

  $a1tot = null;
  $a2tot = null;
  foreach($results as $r)
  {
    if($account1 == $r["id"])
        $a1tot = $r["balance"];
    if($account2 == $r["id"])
      $a2tot = $r["balance"];
  }
	$query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `expected_total`, `memo`) 
	VALUES(:p1a1, :p1a2, :p1change, :type, :a1tot, :memo), 
			(:p2a1, :p2a2, :p2change, :type, :a2tot, :memo)";
	
	$stmt = $db->prepare($query);
	$stmt->bindValue(":p1a1", $account1);
	$stmt->bindValue(":p1a2", $account2);
	$stmt->bindValue(":p1change", $amountChange);
	$stmt->bindValue(":type", "Deposit");
	$stmt->bindValue(":a1tot", $a1tot+$amountChange);
  $stmt->bindValue(":memo", $memo);
	//flip data for other half of transaction
	$stmt->bindValue(":p2a1", $account2);
	$stmt->bindValue(":p2a2", $account1);
	$stmt->bindValue(":p2change", ($amountChange*-1));
	$stmt->bindValue(":type", "Deposit");
	$stmt->bindValue(":a2tot", $a2total-$amountChange);
  $stmt->bindValue(":memo", $memo);
	$result = $stmt->execute();
  if ($result) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating: " . var_export($e, true));
    }
    $stmt = $db->prepare("UPDATE Accounts set balance = :balance where id=:id");
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

if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    flash($source);
    do_bank_action("000000000000", $source, ($amount * -1), $memo);
}
?>
<?php require(__DIR__ . "/partials/flash.php");