<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
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
    <form method="POST" style = "height: 450px">
<div class = "heading4">
<h3>Make a Transfer</h3>
</div>
        <select name="source">
            <?php foreach($u as $user): ?>
             <option value="" disabled selected>Account</option>
              <option value="<?= $user["id"]; ?>"><?= $user["account_number"]; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <select name="dest">
            <?php foreach($u as $user): ?>
	      <option value="" disabled selected>Destination Account</option>
              <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="float" placeholder="Amount" min="0.00" name="amount"/>
        <br>
        <input type="text" placeholder="Attach optional message" name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>
<?php
if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $dest = $_POST["dest"];
    

 $stmt2 = $db->prepare("SELECT balance FROM Accounts WHERE id = :id");
    $r2 = $stmt2->execute([
       ":id"=>$dest
      ]);
	$result = $stmt2->fetch(PDO::FETCH_ASSOC);
	$a1tot = $result["balance"];    

if($amount > 0 && $source != $dest) {
        if ($amount < $a1tot) {
	do_bank_action($source, $dest, ($amount * -1), $memo, "Transfer");
        }
	elseif($source == $dest){
        flash("Error: You cannot transfer money to the same account! Try again.");
        }
        elseif ($amount > $a1tot){
            flash("Error: You do not have enough money to make this withdrawal.");
        }
	}
else {
        flash("Error: Value must be positive!");
     }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
