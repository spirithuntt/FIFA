<?php
include('./controllers/Userimp.php');
include('./middlewares/isLoggedin.php');
$islogedin = new IslogedIn();
echo'welcome';
var_dump($_SESSION);
