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
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * from Users where first_name like :query OR last_name like :query");
    $r = $stmt->execute([":query" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
}
?>

<form method="POST">
<div class = heading4">
<h3>Search for User by Name<h3>
</div>
    <input name="query" placeholder="First or Last Name" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>First name:</div>
                        <div><?php safer_echo($r["first_name"]); ?></div>
                    </div>
                    <div>
                        <div>Last name:</div>
                        <div><?php safer_echo($r["last_name"]); ?></div>
                    </div>


                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div> 
</div>
<?php require(__DIR__ . "/partials/flash.php");
