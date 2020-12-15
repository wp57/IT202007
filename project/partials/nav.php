<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="styles" href="static/css/styles.css">

  </head>
  <body>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>
<link rel="stylesheet" href="static/css/styles.css">
<?php

//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>

<nav class="navbar      navbar-light " align="center" >
 <!-- <a class="navbar-brand" align="center"  href="#">Peremanent Nav elem</a> -->
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
   <span class="navbar-toggler-icon"></span>

  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav">


    <li><a href="home.php">Home</a></li>
    <?php if (!is_logged_in()): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>
	<?php if (has_role("Admin")): ?>
	 <li><a href="create_account.php">Create Account</a></li>
           <li><a href="create_transactions.php">Create a Transaction</a></li>
    <?php endif; ?>
	 <?php if(is_logged_in()): ?>
        <li><a href="create_checking_account.php">Create Checking Account</a></li>
        <li><a href="list_accounts.php">List Accounts</a></li>
        <li><a href="deposit.php">Deposit</a></li>
        <li><a href="withdraw.php">Withdraw</a></li>
        <li><a href="transfer.php">Transfer</a></li>
	<li><a href="Transfer2.php">Transfer to Other Users</a></li>
        <li><a href="profile.php">Profile</a></li>
	<li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
</ul>
</div>
    </nav>

