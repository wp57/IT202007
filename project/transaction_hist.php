<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<div class = "big">
<?php
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
$typeOfTran = array("Withdraw", "Deposit", "Transfer");
?>

<div class = "heading">
<h3>Filter Your Transactions</h3>
</div>

<form method="POST">
        <select name="transaction">
            <?php foreach($typeOfTran as $transaction): ?>
              <option value="" disabled selected>Filter by Account</option>
              <option value="<?= $transaction; ?>"><?= $transaction; ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label>Filter between dates:</label>
        <br>
        <label>First Date</label>
        <br>
        <input type="text" placeholder="YYYY-MM-DD" name="firstDate"/>
        <br>
        <label>Second Date</label>
        <br>
        <input type="text" placeholder="YYYY-MM-DD" name="secDate"/>
        <br>
        <input type="submit" name="filter" value="Filter"/>
    </form>

<?php
$query = "";
$result = [];
if (isset($id)){
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

    $stmt = $db->prepare("SELECT * from Transactions WHERE act_src_id like ORDER BY id DESC LIMIT 10");
    $r = $stmt->execute([":q" => "%$id%"]);
    if($r){
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  
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
      else
        $actType = $_SESSION["tranAction"];
     	$firstDate = "0000-01-01";
        $secDate = "9999-12-31";
      //not set
      if(isset($_POST["firstDate"]) || isset($_POST["secDate"])){
        if($_POST["firstDate"] != "" && $_POST["secDate"] != ""){
          $_SESSION["first"] = $_POST["firstDate"];
          $_SESSION["sec"] = $_POST["secDate"];
          $firstDate = $_SESSION["first"];
          $secDate = $_SESSION["sec"];
        } //incorrectly set
        elseif(($_POST["firstDate"] != "" && $_POST["secDate"] == "") || ($_POST["secDate"] != "" && $_POST["firstDate"] == ""))
        {
          flash("Error: Filter dates are not properly set. Please enter both a start and end date to filter by.");
          $_SESSION["first"] = "0000-01-01";
          $_SESSION["first"] = "9999-12-31";
          $firstDate = $_SESSION["first"];
          $secDate = $_SESSION["sec"];
        }
      }//set
      elseif(isset($_SESSION["isFiltered"])){ 
          $firstDate = $_SESSION["first"];
          $secDate = $_SESSION["sec"];
        }
      }//all time def
      else{
        $_SESSION["first"] = "0000-01-01";
        $_SESSION["sec"] = "9999-12-31";
        $firstDate = $_SESSION["start"];
        $secDate = $_SESSION["end"];
      }
      
      $_SESSION['isFiltered'] = true;
      
      if($actType != "")
      {
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
        $stmt->bindValue(":f", $startDate);
        $stmt->bindValue(":s", $endDate);
        $stmt->bindValue(":q", $id);
      }
      else
      {
        $stmt = $db->prepare("SELECT count(*) as total from Transactions WHERE (act_src_id like :q) AND (created BETWEEN :f AND :s) ORDER BY id DESC LIMIT 10");
        $r = $stmt->execute([":q" => "%$id%", ":s" => $firstDate, ":e" => $secDate]);
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

    $stmt2 = $db->prepare("SELECT id, account_number, account_type from Accounts WHERE user_id like :q LIMIT 10");
    $r2 = $stmt2->execute([":q" => "%$query%"]);
    if ($r2) {
        $res2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<div class = "heading">
<h3>Transaction History</h3>
</div>
<div class="results">
  <label>Filtered by 
  <?php 
    if(isset($_SESSION['isFiltered'])):
      if($_SESSION['isFiltered']):
        if($actType != ""):
          echo $actType;
        endif;
      endif;
    endif; ?>
     between Dates
    <?php 
    if(isset($_SESSION['isFiltered'])):
      if($_SESSION['isFiltered']):
        echo $firstDate . " and " . $secDate;
      endif;
    else:
      echo "0000-01-01 and 9999-12-31";
    endif; ?>
    </label>
    <?php if (count($results) > 0): ?>
        <div class="list-group">
            <?php foreach ($results as $r): ?>
                <div class="list-group-item">
                    <?php foreach ($results2 as $r2): ?>
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
                    <?php endforeach; ?>
                </div>
                <?php echo "<br>"; ?>
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
