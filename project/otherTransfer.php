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
<div class = "heading2">
    <h3>Make a Transfer to Another User</h3>
</div>   	
        <form method="POST">
        <select name="source">
            <?php foreach($u as $user): ?>
		<option value="" disabled selected>Account</option>
	       <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Please enter the last 4 digits of the user's account number.</label>
        <br>
        <input type="int" placeholder="Last 4 Digits" maxlength = "4" name="dest"/>
        <br>
        <input type="text" placeholder="User's Last Name" name="lastName"/>
        <br>
        <input type="float" placeholder="Amount" min="0.00" name="amount"/>
        <br>
        <input type="text" placeholder="Attach optional message" name="memo"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>

if (isset($_POST["save"])) {
    $query = "";
    $amount = (float)$_POST["amount"];
    $source = $_POST["source"];
    $dest = $_POST["dest"];
    $lastName = $_POST["lastName"];
    $memo = $_POST["memo"];
    $user = get_user_id();
    
    $isVal = false;
    $stmt = $db->prepare("SELECT * from Users WHERE id = :q");
    $r = $stmt->execute([":q" => "$query"]);
    if ($r) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    foreach($res as $this)
    {
      if($this["last_name"] == $lastName)
      {
        $thisId = $this["id"];
        $stmt2 = $db->prepare("SELECT * from Accounts WHERE user_id = :q");
        $r2 = $stmt2->execute([":q" => "$currId"]);
        if ($r) {
            $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        foreach($res2 as $last4)
        {
          if(substr($last4["account_number"], 8, 12) == $dest)
          {
            $isVal = true;
            $dest = $last4["id"];
            break;
          }
        }
        if(strlen($dest > 4))
          break;
      }
    }
    
    if($isVal)
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
