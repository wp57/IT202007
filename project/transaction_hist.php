<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
$query = "";
$results = [];
if (isset($_POST["query"])) {
    $query = $_POST["query"];
}
    $db = getDB();
    $UserId = get_user_id();
    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id = $UserId like  :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$query%"]);
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
    $stmt2 = $db->prepare("SELECT id, account_number, account_type from Accounts WHERE id like :q");
  $r2 = $stmt2->execute([":q" => "%$query%"]);
  if ($r2) {
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<h3>List Transactions</h3>
<div class="results">
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <?php foreach ($results2 as $r2): ?>
                    <?php if ($r2["id"] == $r["act_src_id"]): ?>
                    <div>
                        <div>Transaction Number:</div>
                        <div><?php safer_echo($r["id"]); ?></div>
                    </div>
                    <div>
                        <div>Balance:</div>
                        <div><?php safer_echo($r["expected_total"]); ?></div>
                    </div>
                    <div>
                        <div>Account Type:</div>
                        <div><?php safer_echo($r2["account_type"]); ?></div>
                    </div>
                    <div>
                        <div>Account Number:</div>
                            <div><?php safer_echo($r2["account_number"]); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
                <?php echo "<br>"; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
