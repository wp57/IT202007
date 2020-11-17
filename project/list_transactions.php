<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
<?php
$results = [];
$res = [];
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}

    $db = getDB();
    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id = :q LIMIT 10");
    $r = $stmt->execute([":q" => $id]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
    $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id = :q LIMIT 10");
    $r2 = $stmt->execute([":q" => $id]);
    if ($r2) {
        $res = $stmt2->fetch(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }

?>
<h3>List Transactions</h3>
<div class="list-group-item">
                    <div>
                        <div>Account Number:</div>
                        <div><?php safer_echo($res["account_number"]); ?></div>
                    </div>
                    <div>
                        <div>Account Type:</div>
                        <div><?php safer_echo($res["account_type"]); ?></div>
                    </div>
                    <div>
                        <div>Balance:</div>
                        <div><?php safer_echo($res["balance"]); ?></div>
  </div>

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
