<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
<?php
$db = getDB();
$id = get_user_id();
$u = [];
$stmt = $db->prepare("SELECT * FROM Accounts WHERE (account_type != 'Loan') AND active = 'Active' AND frozen = 'false' AND  user_id = :id");
$r = $stmt->execute([":id" => "$id"]);
if ($r) {
        $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
    <form method="POST" style = "height: 400px">
<div class="heading"
    <h3>Make a Deposit</h3>
</div>
        <select name="source">
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
    $source = $_POST["source"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $db = getDB();
    $sql = "SELECT DISTINCT id from Accounts where account_number = '000000000000'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetch();
    $world = $result["id"];
if ($amount > 0) {
    do_bank_action($world, $source, ($amount * -1), $memo, "Deposit");    
}
else {
        flash("Error: Value must be positive! Try again.");
}
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
