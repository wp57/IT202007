<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
    <?php
    $db = getDB();
    $id = get_user_id();
    $u = [];
    $stmt = $db->prepare("SELECT * from Accounts WHERE (account_type != 'Loan') AND frozen = 'false' AND user_id = :id");
    $r = $stmt->execute([":id" => "$id"]);
    if ($r) {
        $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    $u2 = [];
    $stmt = $db->prepare("SELECT * from Accounts WHERE user_id = :id");
    $r = $stmt->execute([":id" => "$id"]);
    if ($r) {
        $u2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <?php foreach($u2 as $user): ?>
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
</div>
<?php
if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $dest = $_POST["dest"];


    $stmt2 = $db->prepare("SELECT * FROM Accounts WHERE id = :id");
    $r2 = $stmt2->execute([
        ":id" => $source
    ]);
    $result = $stmt2->fetch(PDO::FETCH_ASSOC);
    $a1tot = $result["balance"];
    $aType = $result["account_type"];
    
    if ($amount > 0) {
        if ($amount <= $a1tot) {
            do_bank_action($source, $dest, ($amount * -1), $memo, "Transfer");
        } elseif ($source == $dest) {
            flash("Error: You cannot transfer money to the same account! Try again.");
        } elseif ($amount > $a1tot) {
            flash("Error: You do not have enough money to make this withdrawal.");
        } elseif ($aType == 'Loan') {
	    flash("Error: You cannot transfer money from a loan account.");
    } else {
        flash("Error: Value must be positive!");
    }
}
}
    ?>
    </div>
    <?php require(__DIR__ . "/partials/flash.php");
