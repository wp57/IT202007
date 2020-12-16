<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="big">
    <form method="POST" style = "height: 400px; width: 360px;">
        <div class = "heading2">
            <h3>Create Savings Account</h3>
        </div>

        <br>
        <input type="float" placeholder = "Balance" min="5.00" name="balance"/>
        <br>
        <input type="submit" name="save" value="Create"/>
    </form>
    <?php
    if(isset($_POST["save"])) {
        //TODO add proper validation/checks
        $db = getDB();
        $aNum = rand(000000000001, 999999999999);
        for ($x = strlen($aNum); $x < 12; $x++) {
            $aNum = ("0" . $aNum);
        }
        $aType = "Savings";
        $user = get_user_id();
        $apy = 0.03;
        $balance = $_POST["balance"];
        if ($balance >= 5) {
            do {
                $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance, apy) VALUES(:aNum, :aType, :user, :balance, :apy)");
                $r = $stmt->execute([
                    ":aNum" => $aNum,
                    ":aType" => $aType,
                    ":user" => $user,
                    ":apy" => $apy,
                    ":balance" => $balance
                ]);
                $aNum = rand(000000000000, 999999999999);
                for ($x = strlen($aNum); $x < 12; $x++) {
                    $aNum = ("0" . $aNum);
                }
                $e = $stmt->errorInfo();
            } while ($e[0] == "23000");
            $numOfMonths = 1;
            $lastId = $db->lastInsertId();
            $stmt = $db->prepare("UPDATE Accounts set nextAPY = TIMESTAMPADD(MONTH,:months,opened_date) WHERE id = :id");
            $r = $stmt->execute([":id" => $lastId, ":months" => $numOfMonths]);
            if ($r) {
                flash("Your savings account was successfully created with id: " . $lastId . "!");
            } else {
                $e = $stmt->errorInfo();
                flash("Sorry, there was an error creating: " . var_export($e, true));
            }

            $query = null;
            $stmt2 = $db->prepare("SELECT id, account_number, user_id, account_type, opened_date, last_updated, balance from Accounts WHERE id like :q");
            $r2 = $stmt2->execute([":q" => "%$query%"]);
            if ($r2) {
                $result = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            }
            $a1tot = null;
            foreach ($result as $r) {
                if ($r["id"] == 0)
                    $a1tot = $r["balance"];
            }
            $sql = "SELECT DISTINCT id from Accounts where account_number = '000000000000'";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            $world = $result["id"];
            $memo = "Savings";
            do_bank_action($world, $lastId, ($balance*-1), $memo, "Deposit");
            }
        else
            {
                flash('Balance must be $5.00 or more! Please try again.');
            }
    }
    ?>
    </div>
<?php require(__DIR__ . "/partials/flash.php");


