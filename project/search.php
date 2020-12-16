<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<form method="POST" >
<div class="big">
<h3>Search for Users<h3>
</div>
<br>
<input type="text" placeholder="First or Last Name" name="name"/>
  <br>
 <input type="submit" name="search" value="Search"/>

</form>
<?php
$res=[];
$db = getDB();
$stmt = $db->prepare("SELECT * from Users");
$r = $stmt->execute();
if($r)
  $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

$userName = null;

if(isset($_POST['search'])){
  $userName = $_POST['name'];
}

?>
<div class="results">
    <?php if (count($res) > 0): ?>
        <div class="list-group">
              <?php foreach ($res as $r): ?>
                <?php if($r['first_name'] == $userName || $r['last_name'] == $userName): ?>
                <div class="list-group-item">
                    <div>
                        <div>User:</div>
                        <a type="button" href="profile.php?id=<?php safer_echo($r['id']); ?>"><?php safer_echo($r['first_name'] . " " . $r['last_name'] . " (" . $r['username'] . ")"); ?></a>
                    </div>
                    <br>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
</div>
<?php require(__DIR__ . "/partials/flash.php");


