<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
<?php
$db = getDB();
$u = [];
$id = get_user_id();
$stmt = $db->prepare("SELECT * from Accounts WHERE user_id = :id");
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<form method="POST" style = "height: 550px">
<div class = "heading2">
    <h3>Make a Transfer to Another User</h3>
</div>

        <select name="source">
            <?php foreach($u as $user): ?>
		<option value="" disabled selected>Account</option>
	       <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="int" placeholder="Last 4 Digits of Destination Account" maxlength = "4" name="dest"/>
        <br>
        <input type="text" placeholder="User's Last Name" name="lastName"/>
        <br>
        <input type="float" placeholder="Amount" min="0.00" name="amount"/>
        <br>
        <input type="text" placeholder="Attach optional message" name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>

<?php
if (isset($_POST["save"])) {
    $query = "";
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $dest = $_POST["dest"];
    $lastName = $_POST["lastName"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    
    $isValid = false;
    $stmt = $db->prepare("SELECT * from Users WHERE id = :q");
    $r = $stmt->execute([":q" => "$query"]);
    if ($r) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    foreach($res as $thisName)
    {
      if($thisName["last_name"] == $lastName)
      {
        $thisId = $thisName["id"];
        $stmt2 = $db->prepare("SELECT * from Accounts WHERE user_id = :q");
        $r2 = $stmt2->execute([":q" => "$thisId"]);
        if ($r) {
            $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        foreach($res2 as $last4)
        {
          if(substr($last4["account_number"], 8, 12) == $dest)
          {
            $isValid = true;
            $dest = $last4["id"];
            break;
          }
        }
        if(strlen($dest > 4))
        flash("Error: Only enter the last 4 digits of the destination account number.");  
	break;
      }
    }
    
    if($isValid)
    {
      if($amount > 0 && $source != $dest)
        do_bank_action($source, $dest, ($amount * -1), $memo, "Transfer");
      else
      {
        if($amount <= 0)
          flash("Error: Value must be positive!");
        if($source == $dest)
          flash("Error: You cannot transfer to the same account!");
      }
    }
    else
      flash("Error: No such account is found!");
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
