<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (isset($_GET[$id])) {
    $id = $_GET["id"];
}
?>
<?php
$query = "";
$res = [];
if (isset($id)){
    $db = getDB();
    $UserId = get_user_id();
    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id = :q LIMIT 10");
    $r = $stmt->execute([":q" => "$id"]);
    if($r){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results " . var_export($stmt->errorInfo(), true));
    }
    $stmt2 = $db->prepare("SELECT id, account_number, account_type from Accounts WHERE id = :q");
    $r2 = $stmt2->execute([":q" => "$query"]);
    if ($r2) {
        $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<h3>List Transactions</h3>
<div class="results">
    <?php if (count($res) > 0): ?>
        <div class="list-group">
            <?php foreach ($res as $r): ?>
                <div class="list-group-item">
                    <?php foreach ($res2 as $r2): ?>
                    <div>
                        <div>Transaction Number:</div>
                        <div><?php safer_echo($r[id]); ?></div>
                    </div>
                    <div>
                        <div>Balance:</div>
                        <div><?php safer_echo($r[expected_total]); ?></div>
                    </div>
                    <div>
                        <div>Account Type:</div>
                        <div><?php safer_echo($r2[account_type]); ?></div>
                    </div>
                    <div>
                        <div>Account Number:</div>
                            <div><?php safer_echo($r2[account_number]); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php echo "<br>"; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
