<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "big">
    <?php
    if (!has_role("Admin")) {
        flash("You don't have permission to access this page");
        die(header("Location: login.php"));
    }
    ?>
    
    <?php
    $name = null;
    $results=[];
    if(isset($_POST["search"])){
        $name = $_POST['name'];

    }
     $aNum = null;
    if(isset($_POST["numSearch"])){
        $aNum = $_POST['aNum'];
    }
    
    $db = getDB();
    var_dump($name);
    $stmt = $db->prepare("SELECT * from Users where first_name like :q OR last_name like :q");
    $r = $stmt->execute([":q" => "%$name%"]);
    if($r)
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $results2=[];
    $stmt2 = $db->prepare("SELECT * from Accounts where account_number like :q");
    $r2 = $stmt2->execute([":q" => "%$aNum%"]);
    if($r2)
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    ?>
    <form method="POST style = "height: 300px">
    <div class="heading">
        <h3>Search for Users<h3>
    </div>
    <input type="text" placeholder="First or Last Name" name="name"/>
    <br>
    <input type="submit" name="search" value="Search"/>
    <br>
    <br>
    <div class="heading">
        <h3>Search for Account<h3>
    </div>
    <input type="int" maxlength="12" placeholder="Account Number" name="aNum"/>
    <br>
    <input type="submit" name="numSearch" value="Search Num"/>
    </form>
    <label>User Search</label>
    <div class="results">
        <?php if (count($results) > 0): ?>
            <div class="list-group">
                <?php foreach ($results as $r): ?>
                    <div class="list-group-item">
                        <div>
                            <div>User:</div>
                            <a type="button" href="profile.php?id=<?php safer_echo($r['id']); ?>"><?php safer_echo($r['first_name'] . " " . $r['last_name'] . " (" . $r['username'] . ")"); ?></a>
                        </div>
                        <br>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
    <br>
    <div class="results">
        <?php if (count($results2) > 0): ?>
            <div class="list-group">
                <?php foreach ($results2 as $r2): ?>
                    <div class="list-group-item">
                        <div>
                            <div>Account Number: <?php safer_echo($aNum); ?> </div>
                            <a type="button" href="transaction_history.php?id=<?php safer_echo($r2['id']); ?>">Transaction History</a>
                        </div>
                        <br>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>No results</p>
        <?php endif; ?>
    </div>
</div>
<?php require(__DIR__ . "/partials/flash.php");






