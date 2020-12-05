<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
$db = getDB();
$users = [];
$id = get_user_id();
$stmt = $db->prepare("SELECT * from Accounts WHERE user_id = :id");
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
    <h3>Make a Transfer</h3>
    <form method="POST">
        <br>
        <select name="source">
            <?php foreach($u as $user): ?>
		<option value="" disabled selected>Account</option>
              <option value="<?= $user["id"]; ?>"><?= $user["account_number"]; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <select name="dest">
            <?php foreach($u as $user): ?>
		<option value="" disabled selected>Transfer Destination</option>
           <option value="<?= $user["id"]; ?>"><?= $user["account_number"]; ?></option>
            <?php endforeach; ?>
        </select>

        <br>
        <input type="float" placeholder = "Amount" min="0.00" name="amount"/>
        <br>
        <input type="text" placeholder= "Attach optional message"  name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $dest = $_POST["dest"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    if($amount > 0)
      do_bank_action($source, $dest, ($amount * -1), $memo, "Transfer");
    else
	flash("Error: Value must be positive!");
}
?>
<?php require(__DIR__ . "/partials/flash.php"); 
