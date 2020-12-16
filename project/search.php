<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "big">;
<?php
if (!has_role("Admin")) {
    flash("You don't have permission to access this page");
    die(header("Location: login.php"));
}
?>
    <form method="POST style = "height: 300px">
        <div class="heading">
            <h3>Search for Users by Name<h3>
        </div>
        <input type="text" placeholder="First or Last Name" name="name"/>
        <br>
        <input type="submit" name="search" value="Search"/>
        <br>
<br>
<div class="heading">        
<h3>Search for Users by Account Number<h3>
</div>        
        <input type="int" maxlength="12" placeholder="Account Number" name="aNum"/>
        <br>
        <input type="submit" name="searchNum" value="Search Num"/>
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

$aNum = null;

if(isset($_POST['searchNum'])){
    $aNum = $_POST['aNum'];
}

$res2=[];
$stmt2 = $db->prepare("SELECT * from Accounts where account_number = :q");
$r2 = $stmt2->execute([":q" => $aNum]);
if($r2)
    $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
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
        <br>
        <div class="results">
            <?php if (count($res2) > 0): ?>
                <div class="list-group">
                    <?php foreach ($res2 as $r2): ?>
                        <?php if($r2['account_number'] == $aNum): ?>
			<div class="list-group-item">
                            <div>
                                <div>Account Number: <?php safer_echo($aNum); ?> </div>
                                <a type="button" href="transaction_history.php?id=<?php safer_echo($r2['id']); ?>">Transaction History</a>
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
</div>
<?php require(__DIR__ . "/partials/flash.php");
