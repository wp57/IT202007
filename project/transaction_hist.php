<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "list">
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
$typeOfTran = array("Withdraw","Deposit","Transfer");
?>
<div class = "heading">
<h3>Transactions History</h3>
</div>

    <form method="POST" style = "height: 500px;">
	<div class = "heading2">
	<h3>Filter Transactions</h3>
	</div>
        <select name="transaction">
            <?php foreach($typeOfTran as $transaction): ?>
              <option value="" disabled selected>Transaction Type</option>
              <option value="<?= $transaction; ?>"><?= $transaction; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="text" placeholder="Date 1: YYYY-MM-DD" name="firstDate"/>
        <br>
        <input type="text" placeholder="Date 2: YYYY-MM-DD" name="secDate"/>
        <br>
        <input type="submit" name="filter" value="Filter"/>
    </form>
<?php
$query = "";
$result = [];
if (isset($id)) {
    $db = getDB();
    $userId = get_user_id();
    $page = 1;
    $per_page = 10;
    if(isset($_GET["page"])){
        try {
            $page = (int)$_GET["page"];
        }
        catch(Exception $e){
        }
    }
    
    $stmt = $db->prepare("SELECT count(*) as total from Transactions WHERE act_src_id like :q ORDER BY id DESC LIMIT 10");
    $r = $stmt->execute([":q" => "%$id%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = 0;
    if($result){
        $total = (int)$result["total"];
    }
    $total_pages = ceil($total / $per_page);
    $offset = ($page-1) * $per_page;
    
    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id like :q ORDER BY id DESC LIMIT :offset, :count");
    //need to use bindValue to tell PDO to create these as ints
    //otherwise it fails when being converted to strings (the default behavior)
    $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
    $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
    $stmt->bindValue(":q", $id);
    $stmt->execute();
        $e = $stmt->errorInfo();
        if($e[0] != "00000"){
            flash(var_export($e, true), "alert");
        }
    
    if(isset($_POST["filter"]) || isset($_SESSION['isFiltered'])){
      if(isset($_POST["transaction"])){
        $_SESSION["tranAction"] = $_POST["transaction"];
        $actType = $_SESSION["tranAction"];
      }
      else{
	if(isset($_SESSION["tranAction"])){
        	$actType = $_SESSION["tranAction"];
      	}
      }
	$firstDate = "0000-01-01";
        $secDate = "9999-12-31";

      if(isset($_POST["firstDate"]) || isset($_POST["secDate"])){//not set def
        if($_POST["firstDate"] != "" && $_POST["secDate"] != ""){
          $_SESSION["first"] = $_POST["firstDate"];
          $_SESSION["sec"] = $_POST["secDate"];
          $firstDate = $_SESSION["first"];
          $secDate = $_SESSION["sec"];
        }//incorrectly set
        elseif(($_POST["firstDate"] != "" && $_POST["secDate"] == "") || ($_POST["secDate"] != "" && $_POST["firstDate"] == "")){
          $_SESSION["first"] = "0000-01-01";
          $_SESSION["sec"] = "9999-12-31";
          $firstDate = $_SESSION["first"];
          $sec = $_SESSION["sec"];
        }
      }//set
      elseif(isset($_SESSION["isFiltered"])){ 
        if($_SESSION["isFiltered"]){
          $firstDate = $_SESSION["first"];
          $secDate = $_SESSION["sec"];
        }
      }
      else{        
	$_SESSION["first"] = "0000-01-01";
        $_SESSION["sec"] = "9999-12-31";
        $firstDate = $_SESSION["first"];
        $secDate = $_SESSION["sec"];
      }
      
      $_SESSION["isFiltered"] = true;
      
      if($actType != ""){
        $stmt = $db->prepare("SELECT count(*) as total from Transactions WHERE (action_type like :a) AND (act_src_id like :q) AND (created BETWEEN :f AND :s) ORDER BY id DESC LIMIT 10");
        $r = $stmt->execute([":q" => "%$id%", ":a" => $actType, ":f" => $firstDate, ":s" => $secDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = 0;
        if($result){
          $total = (int)$result["total"];
        }
        $total_pages = ceil($total / $per_page);
        $offset = ($page-1) * $per_page;
        
        $stmt = $db->prepare("SELECT * from Transactions WHERE (action_type like :a) AND (act_src_id like :q) AND (created BETWEEN :f AND :s) ORDER BY id DESC LIMIT :offset, :count");
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
        $stmt->bindValue(":a", $actType);
        $stmt->bindValue(":f", $firstDate);
        $stmt->bindValue(":s", $secDate);
        $stmt->bindValue(":q", $id);
      }
      else
      {
        $stmt = $db->prepare("SELECT count(*) as total from Transactions WHERE (act_src_id like :q) AND (created BETWEEN :f AND :s) ORDER BY id DESC LIMIT 10");
        $r = $stmt->execute([":q" => "%$id%", ":f" => $firstDate, ":s" => $secDate]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = 0;
        if($result){
          $total = (int)$result["total"];
        }
        $total_pages = ceil($total / $per_page);
        $offset = ($page-1) * $per_page;
        
        $stmt = $db->prepare("SELECT * from Transactions WHERE (act_src_id like :q) AND (created BETWEEN :f AND :s) ORDER BY id DESC LIMIT :offset, :count");
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->bindValue(":count", $per_page, PDO::PARAM_INT);
        $stmt->bindValue(":f", $firstDate);
        $stmt->bindValue(":s", $secDate);
        $stmt->bindValue(":q", $id);
      }
    }
    
    $r = $stmt->execute();
    if ($r) {
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    $stmt2 = $db->prepare("SELECT id, account_number, account_type from Accounts WHERE user_id = :userId");
    $r2 = $stmt2->execute([":userId" => $userId]);
    if ($r2) {
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<div class="results">
  <label>Filtered By:
  <?php 
    if(isset($_SESSION['isFiltered'])):
      if($_SESSION['isFiltered']):
        if($actType != ""):
          echo $actType;
        endif;
      endif;
    endif;
    ?> Dates:
    <?php 
    if(isset($_SESSION['isFiltered'])):
      if($_SESSION['isFiltered']):
        echo $firstDate . " to  " . $secDate;
      endif;
    else:
      echo "All Time";
    endif; ?>
    </label>
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
			<br>
                    <?php endforeach; ?>
                </div>
		<br>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No results</p>
    <?php endif; ?>
</div>
  <nav aria-label="Trans History">
    <ul class="pagination justify-content-center">
      <li class="page-item <?php echo ($page-1) < 1?"disabled":"";?>">
        <a class="page-link" href="?id=<?php echo $id;?>&page=<?php echo $page-1;?>" tabindex="-1">Previous</a>
      </li>
      <?php for($i = 0; $i < $total_pages; $i++):?>
      <li class="page-item <?php echo ($page-1) == $i?"active":"";?>"><a class="page-link" href="?id=<?php echo $id;?>&page=<?php echo ($i+1);?>"><?php echo ($i+1);?></a></li>
      <?php endfor; ?>
      <li class="page-item <?php echo ($page+1) > $total_pages?"disabled":"";?>">
        <a class="page-link" href="?id=<?php echo $id;?>&page=<?php echo $page+1;?>">Next</a>
      </li>
    </ul>
  </nav>
</div>
