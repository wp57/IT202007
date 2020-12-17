<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<div class="big">
<?php
$db = getDB();
$u = [];
$stmt = $db->prepare("SELECT * from Accounts WHERE active = 'Active' AND frozen = 'false'");
$r = $stmt->execute();
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$u2 = [];
$stmt = $db->prepare("SELECT * from Accounts WHERE active = 'Active' AND frozen = 'true'");
$r = $stmt->execute();
if ($r) {
    $u2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST["save"])) {
  $freeze = $_POST['freeze'];
  $stmt = $db->prepare("UPDATE Accounts set frozen = 'true' where active = 'Active' and id = :id");
  $r = $stmt->execute([":id" => $freeze]);
  if ($r) {
    flash("This account is now frozen.");
    die(header("Location: freeze.php"));
  }
  else {
    flash("Error updating account.");
  }
}

if (isset($_POST["save2"])) {
  $unfreeze = $_POST['unfreeze'];
  $stmt = $db->prepare("UPDATE Accounts set frozen = 'false' where active = 'Active' and id = :id");
  $r = $stmt->execute([":id" => $unfreeze]);
  if ($r) {
    flash("This account is no longer frozen.");
    die(header("Location: freeze.php"));
  }
  else {
    flash("Error updating account.");
  }
}
?>
 <form method="POST" style = "height: 400px">
<div class = "heading">
    <h3>Freeze an Account</h3>
</div>
        <select name="freeze">
            <?php foreach($u as $user): ?>
	      <option value="" disabled selected>Account</option>
               <option value="<?= $user["id"]; ?>"><?= $user["account_number"]; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="save" value="freeze"/>
<div class = "heading">
    <h3>Unfreeze an Account<h3>
        </div>
        <select name="unfreeze">
            <?php foreach($u2 as $user2): ?>
		<option value="" disabled selected>Account</option>
              <option value="<?= $user2['id']; ?>"><?= $user2['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" name="save2" value="unfreeze"/>
</div>
<?php require(__DIR__ . "/partials/flash.php");
