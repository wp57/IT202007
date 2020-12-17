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
    $stmt = $db->prepare("SELECT * from Accounts WHERE account_number like :query");
    $r = $stmt->execute([":query" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
}
?>

<form method="POST" style = "height: 600px">
<div class = "heading">
<h3>Search by Account Number<h3>
</div>
    <input name="query" placeholder="Account Number" value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <a href="transaction_hist.php?id=<?php safer_echo($r['id']); ?>"</a>
                        <div>
                            <div>Account Number:</div>
                            <div><?php safer_echo($r["account_number"]); ?></div>
                        </div>
                    </a>a>

                    <div>
                        <div>Account Type:</div>
                        <div><?php safer_echo($r["account_type"]); ?></div>
                    </div>

                    <div>
                        <div>Opened Date:</div>
                        <div><?php safer_echo($r["opened_date"]); ?></div>
                    </div>

                    <div>
                        <div>Balance:</div>
                        <div><?php safer_echo($r["balance"]); ?></div>
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
