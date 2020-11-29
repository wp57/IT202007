<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="shiftRight">
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>

<?php
$db = getDB();
$sql = "SELECT id, account_number from Accounts";
$stmt = $db->prepare($sql);
$stmt->execute();
$users=$stmt->fetchAll();
?>
    <h3>Create Transaction</h3>
    <form method="POST">
        <label>Action Type</label>
        <br>
        <select name="actType" id ="mySelect" onchange="myFunction()">
            <option value="Deposit">Deposit</option>
            <option value="Withdraw">Withdraw</option>
            <option value="Transfer">Transfer</option>
        </select>	
        <br>
        <label>Account</label>
        <br>

        <select name="source">
            <?php foreach($users as $user): ?>
              <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>

        <div id="ifTran">
        <br>
        <label>Transaction Destination</label>
        <br>
        <select name="dest">
            <?php foreach($users as $user): ?>
              <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        </div>

        <script>
        document.getElementById("ifTran").style.display = "none";
        function myFunction() {
          var x = document.getElementById("mySelect").value;
          if(x=="Transfer")
            document.getElementById("ifTran").style.display = "inline";
          else
            document.getElementById("ifTran").style.display = "none";
        }
        </script>

        <br>
        <label>Amount</label>
        <br>
        <input type="float" min="0.00" name="amount"/>
        <br>
        <label>Memo</label>
        <br>
        <input type="text" placeholder="Optional message for your transaction"  name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>
<?php
if (isset($_POST["save"])) {
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $dest = $_POST["dest"];
    $actType = $_POST["actType"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    $db = getDB();
    $sql = "SELECT DISTINCT id from Accounts where account_number = '000000000000'";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result=$stmt->fetch();
    $world = $result["id"];
    switch($actType)
    {
        case "Deposit":
            do_bank_action($world, $source, ($amount * -1), $actType, $memo);
            break;
        case "Withdraw":
            do_bank_action($world, $source, ($amount * -1), $actType, $memo);
            break;
        case "Transfer":
            do_bank_action($source, $dest, ($amount * -1), $actType, $memo);
            break;    
    }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
