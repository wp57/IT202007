<!DOCTYPE html>
<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class="dbig">

<form method="POST" style = "height: 400px; width: 360px;">
<div class = "heading2">
<h3>Create Savings Account</h3>
</div>

  <br>
	<input type="float" placeholder = "Balance" min="5.00" name="balance"/>
 <br>
	<input type="submit" name="save" value="Create"/>
</form>

<html>
<body>
<p id="apy"></p>
  <script>
    function myFunction() {
      var bal = document.getElementByName("balance")[0].value;
      if(bal >= 5)
        document.getElementById("apy").innerHTML = "Your APY is 0.03%.";
      else
        document.getElementById("apy").innerHTML = "Your balance needs to be more than $5.00 in order to calculate your APY.";
    }
  </script>
</body>
</html>

<?php
if(isset($_POST["save"])){
  $db = getDB();
	$aNum = rand(000000000001, 999999999999);
  for($x = strlen($aNum); $x < 12; $x++){
    $aNum = ("0" . $aNum);
  }
  $aType = "Savings";
  $user = get_user_id();
  $balance = $_POST["balance"];
  if($balance >= 5)
  {
    do {
      $stmt = $db->prepare("INSERT INTO Accounts (account_number, account_type, user_id, balance) VALUES(:aNum, :aType, :user, :balance)");
  	$r = $stmt->execute([
  		":aNum"=>$aNum,
  		":aType"=>$aType,
  		":user"=>$user,
                ":balance"=>$balance
      ]);
      $aNum = rand(000000000000, 999999999999);
      for($x = strlen($aNum); $x < 12; $x++){
        $aNum = ("0" . $aNum);
      }
      $e = $stmt->errorInfo();
      }
	while($e[0] == "23000");
    if($r){
      $lastId = $db->lastInsertId();
  		flash("Your savings account was successfully created with account number " . $aNum . "!");
  	}
  	else{
  		$e = $stmt->errorInfo();
  		flash("Sorry, there was an error creating: " . var_export($e, true));
  	}
  $stmt = $db->prepare("UPDATE Accounts SET balance = (SELECT SUM(amount) FROM Transactions WHERE Transactions.act_src_id = Accounts.id) where id = :id");
    $r = $stmt->execute([
       ":balance"=>($a1tot+$amountChange),
       ":id"=>$account1
      ]);
    $r = $stmt->execute([
       ":balance"=>($a2tot-$amountChange),
       ":id"=>$account2
      ]);
	die(header("Location: list_accounts.php"));
  }
  else
  {
    flash('Balance must be $5.00 or more! Please try again.');
  }
}
?>
</div>
<?php require(__DIR__ . "/partials/flash.php");
