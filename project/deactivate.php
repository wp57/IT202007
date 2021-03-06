<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
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
$stmt = $db->prepare("SELECT * from Users WHERE deactivated = 'false'");
$r = $stmt->execute();
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$u2 = [];
$stmt = $db->prepare("SELECT * from Users WHERE deactivated = 'true'");
$r = $stmt->execute();
if ($r) {
    $u2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST["save"])) {
  $deactivate = $_POST['deactivate'];
  $stmt = $db->prepare("UPDATE Users set deactivated = 'true' where id = :id");
  $r = $stmt->execute([":id" => $deactivate]);
  if ($r) {
    flash("This user has been deactivated.");
    die(header("Location: deactivate.php"));
  }
  else {
    flash("Error updating account.");
  }
}

if (isset($_POST["save2"])) {
  $activate = $_POST['activate'];
  $stmt = $db->prepare("UPDATE Users set deactivated = 'false' where id = :id");
  $r = $stmt->execute([":id" => $activate]);
  if ($r) {
    flash("This user has been activated");
    die(header("Location: deactivate.php"));
  }
  else {
    flash("Error updating account.");
  }
}
?>

<form method="POST" style = "height: 500px">
<div class = "heading">
<h3>Deactivate a User<h3>
</div>
        <select name="deactivate">
            <?php foreach($u as $user): ?>
		<option value="" disabled selected>Username</option>
              <option value="<?= $user['id']; ?>"><?= $user['username']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" name="save" value="Deactivate"/>
        <br>
	<br>
	<br>
	
<div class = "heading">
<h3>Reactivate a User<h3>
</div>
        <select name="activate">
            <?php foreach($u2 as $user2): ?>
	      <option value="" disabled selected>Username</option>
              <option value="<?= $user2['id']; ?>"><?= $user2['username']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" name="save2" value="Reactivate"/>
</div>
<?php require(__DIR__ . "/partials/flash.php");
