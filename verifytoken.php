<?php
include('./controllers/Userimp.php');
// getting the token from the url 
$user = new Userimp();
$verified = null;
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $user->setToken($token);
    $verified = $user->verifyEmail();
} else {
    header('location:./index.php');
}

if ($verified) {
    echo "Your email has been verified just close this tab and login";
}else{
    echo "Your email could not be verified";
}