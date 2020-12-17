<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "big">
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$db = getDB();
$u = [];
$stmt = $db->prepare("SELECT * from Accounts WHERE (account_number != '000000000000') AND  active = 'Active' AND frozen = 'false'");
$r = $stmt->execute();
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
$u2 = [];
$stmt = $db->prepare("SELECT * from Accounts WHERE active = 'closed' AND frozen = 'false'");
$r = $stmt->execute();
if ($r) {
    $u2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if (isset($_POST["save"])) {
  $close = $_POST['close'];
  $stmt = $db->prepare("UPDATE Accounts set active = 'closed' where active = 'Active' AND id = :id");
  $r = $stmt->execute([":id" => $close]);
  if ($r) {
    flash("This account has been closed.");
    die(header("Location: closeAdmin.php"));
  }
  else {
    flash("Error updating account.");
  }
}
if (isset($_POST["save2"])) {
  $open = $_POST['open'];
  $stmt = $db->prepare("UPDATE Accounts set active = 'Active' where active = 'closed' AND id = :id");
  $r = $stmt->execute([":id" => $open]);
  if ($r) {
    flash("This account has been reopened.");
    die(header("Location: closeAdmin.php"));
  }
  else {
    flash("Error updating account.");
  }
}
?>
<form method="POST">
<div class = "heading4">
<h3>Close a User's Account<h3>
</div>
        <select name="close">
            <?php foreach($u as $user): ?>
	      <option value="" disabled selected>Account</option>
              <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" name="save" value="Close"/>
        <br>
	<br>
	<br>
<div class = "heading4">
<h3>Reopen a User's Account<h3>
</div>
        <select name="reopen">
            <?php foreach($u2 as $user2): ?>
		<option value="" disabled selected>Account</option>
              <option value="<?= $user2['id']; ?>"><?= $user2['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" name="save2" value="Reopen"/>
</div>
<?php require(__DIR__ . "/partials/flash.php"); 
