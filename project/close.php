<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$db = getDB();
$u = [];
$id = get_user_id();
$stmt = $db->prepare("SELECT * from Accounts WHERE active = 'Active' AND user_id = :id");
$r = $stmt->execute([":id" => $id]);
if ($r) {
    $u = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php
if (isset($_POST["save"])) {
    $source = $_POST["source"];
    $user = get_user_id();
    $stmt = $db->prepare("SELECT * from Accounts WHERE active = 'Active' AND id like :q LIMIT 1");
    $r = $stmt->execute([":q" => $source]);
    if ($r) {
      $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      flash("There was a problem fetching the results");
    }

    foreach($res as $now)
    {
      if($now["balance"] == 0)
      {
        $stmt = $db->prepare("UPDATE Accounts set active = 'Closed' WHERE active = 'Active' AND id = :id");
        $r = $stmt->execute([":id" => $source]);
        if ($r) {
            flash("This account is now closed.");
            die(header("Location: close.php"));
        }
        else {
            flash("Error updating profile");
        }
      }
      else {
        flash("You still have $" . $now['balance'] . " in this account. Please withdraw or transfer the funds in this account first.");
      }
    }

}
?>
<div class = "big">
<form method="POST" style = "height: 400px">
<div class = "heading">
    <h3>Close an Account</h3>
</div>
        <select name="source">
            <?php foreach($u as $user): ?>
              <option value="" disabled selected>Account</option>
              <option value="<?= $user['id']; ?>"><?= $user['account_number']; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" name="save" value="Close"/>
    </form>
</div>
<?php require(__DIR__ . "/partials/flash.php");
