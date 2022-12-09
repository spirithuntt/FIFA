<?php
include('./controllers/Userimp.php');
// creating a an instance of the class userimp
$user = new Userimp();
// calling the logout method
$user->logout();

header('location: ./login.php');