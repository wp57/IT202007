<link rel="stylesheet" href="static/css/styles.css">
<?php
// branch
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav>
<ul class="nav">
    <li><a href="home.php">Home</a></li>
    <?php if (!is_logged_in()): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>
    <?php if(has_role("Admin")): ?>
        <li><a href="create_account.php">Create Account</a></li>
	<li><a href="create_transactions.php">Create a Transaction</a></li>
    <?php endif; ?>

    <?php if (is_logged_in()): ?>
	<li><a href="create_checking_account.php">Create Checking Account</a></li>
        <li><a href="list_accounts.php">List Accounts</a></li>
        <li><a href="deposit.php">Deposit</a></li>
        <li><a href="withdraw.php">Withdraw</a></li>
        <li><a href="#">Transfer</a></li>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
</ul>
</nav>
