<?php
include('./controllers/Userimp.php');
// getting the token from the url 
$user = new Userimp();
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user->setToken($token);
    $user->verifyEmail();
} else {
    header('location:./index.php');
}