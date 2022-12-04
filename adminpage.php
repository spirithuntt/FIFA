<?php
include('./controllers/Userimp.php');
include('./middlewares/isadmin.php');
// check if user is admin
$islogedin = new IslogedIn();
$isadmin = new IsAdmin();
echo 'welcome admin';