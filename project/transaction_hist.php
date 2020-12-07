<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>
<?php
$query = "";
$res = [];
if (isset($id)){
    $db = getDB();
    $userId = get_user_id();
    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id like :q LIMIT 10");
    $r = $stmt->execute([":q" => "%$id%"]);
    if($r){
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  
    $stmt2 = $db->prepare("SELECT id, account_number, account_type from Accounts WHERE user_id like :q LIMIT 10");
    $r2 = $stmt2->execute([":q" => "%$query%"]);
    if ($r2) {
        $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<h3>Transaction History</h3>
<div class="results">
    <?php if (count($res) > 0): ?>
        <div class="list-group">
            <?php foreach ($res as $r): ?>
                <div class="list-group-item">
                    <?php foreach ($res2 as $r2): ?>
                    <?php if ($r["act_src_id"] == $r2["id"]): ?>
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
		<br>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
}
