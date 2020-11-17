<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$query = "";
$query2 = "";
$results = [];
$res = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
if (isset($_POST["search"]) && !empty($query)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id = :q LIMIT 10");
    $r = $stmt->execute([":q" => "$query"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
    $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id = :q LIMIT 10");
    $r2 = $stmt->execute([":q" => "$query2"]);
    if ($r2) {
        $res = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
}
?>
<h3>List Transactions</h3>
<form method="POST">
    <input name="query" placeholder="Enter your account ID." value="<?php safer_echo($query); ?>"/>
    <input type="submit" value="Search" name="search"/>
</form>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <div>
                        <div>Transaction Number:</div>
                        <div><?php safer_echo($r["id"]); ?></div>
                    </div>
                    <div>
                        <div>Amount Changed:</div>
                        <div><?php safer_echo($r["amount"]); ?></div>
                    </div>
                    <div>
                        <div>Expected Total:</div>
                        <div><?php safer_echo($r["expected_total"]); ?></div>
                    </div>
                    <div>
                        <div>Action Type:</div>
                        <div><?php safer_echo($r["action_type"]); ?></div>
                    </div>
                    <div>
                        <div>Owner Id:</div>
                        <div><?php safer_echo($r["id"]); ?></div>
                    </div>
                    <div>
                          <a type="button" href="edit_transactions.php?id=<?php safer_echo($r['id']); ?>">Edit</a>
                        <a type="button" href="view_transactions.php?id=<?php safer_echo($r['id']); ?>">View</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
