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
</div>
<?php
function do_bank_action2($account1, $account2, $amountChange, $memo, $type){
    $db = getDB();
    $query = null;
    $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id like :q");
    $r2 = $stmt2->execute([":q" => "%$query%"]);
    if ($r2) {
        $res = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }

    $loan1 = false;
    $loan2 = false;
    foreach($res as $r){
        if($account1 == $r["id"]){
            $a1tot = $r["balance"];
            if($r['account_type'] == "Loan")
                $loan1 = true;
        }
        if($account2 == $r["id"]){
            $a2tot = $r["balance"];
            if($r['account_type'] == "Loan")
                $loan2 = true;
        }
    }

    if(!$loan1){
        if(-$amountChange) > $a2tot)
            flash("You are paying off more than you owe! Your loan balance is $" . $a2tot. ". Enter that amount or less");
    }
    else{
            if (isset($_POST["save"])) {
                $amount = (float)$_POST["amount"];
                $memo = $_POST["memo"];
                $user = get_user_id();


            if ($amount < $a1tot) {
                $query = "INSERT INTO `Transactions` (`act_src_id`, `act_dest_id`, `amount`, `action_type`, `expected_total`, `memo`)
        VALUES(:p1a1, :p1a2, :p1change, :type, :a1tot, :memo),
                        (:p2a1, :p2a2, :p2change, :type, :a2tot, :memo)";

                $stmt = $db->prepare($query);
                $stmt->bindValue(":p1a1", $account1);
                $stmt->bindValue(":p1a2", $account2);
                $stmt->bindValue(":p1change", $amountChange);
                $stmt->bindValue(":type", "Transfer");
                $stmt->bindValue(":a1tot", $a1tot+$amountChange);
                $stmt->bindValue(":memo", $memo);
                //flip data for other half of transaction
                $stmt->bindValue(":p2a1", $account2);
                $stmt->bindValue(":p2a2", $account1);
                
	        if($loan2){
                    $stmt->bindValue(":p2change", $amountChange);
                    $stmt->bindValue(":type", "Transfer");
                    $stmt->bindValue(":a2tot", $a2tot+$amountChange);
                }
                else{
                    $stmt->bindValue(":p2change", ($amountChange*-1));
                    $stmt->bindValue(":type", "Transfer");
                    $stmt->bindValue(":a2tot", $a2tot-$amountChange);
                }
                $stmt->bindValue(":memo", $memo);
                $result = $stmt->execute();
                if ($result) {
                    flash("Transfer successfully made!");
                }
                else {
                    $e = $stmt->errorInfo();
                    flash("Error creating: " . var_export($e, true));
                }
                $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = Accounts.id)");
                $r = $stmt->execute();
                return $result;
            }
            else{
                flash("Error: You do not have enough money to make this withdrawal.");
            }
        }
  }
    else{
        flash("Error: You cannot transfer money from your loan account.");
    }
}
}
?>
<?php require(__DIR__ . "/partials/flash.php");
