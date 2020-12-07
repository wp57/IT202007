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

    if($amount > 0 && $source != $dest)
      do_bank_action($source, $dest, ($amount * -1), $memo, "Transfer");
    else
    {
      if($amount <= 0)
	flash("Error: Value must be positive! Try again.");	
      if($source == $dest)
        flash("Error: You cannot transfer money to the same account! Try again.");
    }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");






