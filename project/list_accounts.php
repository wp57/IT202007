<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
$query = "";
$res = [];
    $db = getDB();
    $stmt = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE user_id like :q LIMIT 5");
    $r = $stmt->execute([":q" => "%$query%"]);
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
		<?php if ($r["user_id"] == get_user_id()): ?>
                    <div class="list-group-item">
                    <div>
                        <div>Account Number</div>
                        <div><?php safer_echo($r[account_number]); ?></div>
                    </div>
                    <div>
                        <div>Account Type</div>
                        <div><?php safer_echo($r[account_type]); ?></div>
                    </div>
                    <div>
                        <div>Balance</div>
                        <div><?php safer_echo($r[balance]); ?></div>
                    </div>
                    <div>
                        <div>Owner Id</div>
                        <div><?php safer_echo($r[user_id]); ?></div>
                    </div>
                    <div>
		        <a type="button" href="transaction_hist.php?id=<?php safer_echo($r[id]); ?>">Transaction History</a>
                    </div>
                </div>   
		<?php endif; ?>
           <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
	<?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");

