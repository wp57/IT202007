<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="list">
    <?php
    $query = get_user_id();
    $res = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance, apy from Accounts WHERE active = 'Active' AND user_id = :q LIMIT 5");
    $r = $stmt->execute([":q" => "$query"]);
    if ($r) {
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        flash("There was a problem fetching the results");
    }
    ?>
    <h3>List Accounts</h3>
    <div class="results">
        <?php if (count($res) > 0): ?>
            <div class="list-group">
                <?php foreach ($res as $r): ?>
                    <div class="list-group-item">
                        <div>
                            <div>Account Number:</div>
                            <div><?php safer_echo($r["account_number"]); ?></div>
                        </div>
                        <div>
                            <div>Account Type:</div>
                            <div><?php safer_echo($r["account_type"]); ?></div>
                        </div>
                        <div>
                            <div>Balance:</div>
                            <div><?php safer_echo($r["balance"]); ?></div>
                        </div>
                        <div>
                            <div>Owner Id:</div>
                            <div><?php safer_echo($r["user_id"]); ?></div>
                        </div>
                        <div>
                            <?php if($r["apy"] != 0): ?>
                                <div>
                                    <div>APY:</div>
                                    <div><?php safer_echo(($r["apy"]*100) . "%"); ?></div>
                                </div>
                            <?php endif; ?>
                            <a type="button" href="transaction_hist.php?id=<?php safer_echo($r["id"]); ?>">Transaction History</a>
                        </div>
                    </div>
                    <br>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
</div>
<?php require(__DIR__ . "/partials/flash.php");



