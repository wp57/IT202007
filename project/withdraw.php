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
    <form method="POST">
<div class = "heading">
    <h3>Make a Withdrawal</h3>
</div>
        <select name="dest">
            <?php foreach($u as $user): ?>
	      <option value="" disabled selected>Account</option>
               <option value="<?= $user["id"]; ?>"><?= $user["account_number"]; ?></option>
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
    $dest = $_POST["dest"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $db = getDB();
    $sql = "SELECT DISTINCT id from Accounts where account_number = '000000000000'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetch();
    $world = $result["id"];
	
    // get users acc balance from acc table, check for negative withdrawal
 $stmt2 = $db->prepare("SELECT balance FROM Accounts WHERE id = :id");
    $r2 = $stmt2->execute([
       ":id"=>$dest
      ]);
	$result = $stmt2->fetch(PDO::FETCH_ASSOC);
	$a1tot = $result["balance"];    

if ($amount > 0) {
        if ($amount < $a1tot) {
            do_bank_action($dest, $world, ($amount * -1), $memo, "withdraw");
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
