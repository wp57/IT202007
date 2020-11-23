<<<<<<< HEAD
<?php require_once(__DIR__ . "/partials/nav.php"); ?>
This is a placeholder index file for the project.
Typically this would be a public homepage and I leave it up to the project requirements to fill this in.
If we didn't have an index file navigating to the project folder would list our files.
=======
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<ul>
    <li><a href="home.php">Home</a></li>
    <?php if (!is_logged_in()): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    <?php endif; ?>
    <?php if (is_logged_in()): ?>
        <li><a href="profile.php">Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    <?php endif; ?>
</ul>
>>>>>>> 402f506503617c0d334edae78d3902c14a2014c9
