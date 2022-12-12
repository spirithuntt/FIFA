<?php
include("./controllers/Userimp.php");
include("./middlewares/isLoggedin.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>User Profile</title>
</head>

<body>
    <?php

    //fetch data from database
    if(isset($_SESSION['id'])){
        $user = new Userimp();
        $user->setId($_SESSION['id']);
        $user->showUser();
    }
    if(!isset($_SESSION['id'])){
        echo '<div class="alert alert-danger">
            your account has been deleted please sign up again.
            <a class="btn btn-dark" href="signup.php">sign up</a>
        </div>';
    }
    if (isset($_POST['update'])){
        $user = new Userimp();
        $user->setFirst_name($_POST['fname']);
        $user->setLast_name($_POST['lname']);
        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $user->setId($_SESSION['id']);
        $user->setPassword_com($_POST['password_comfirm']);
        $user->updateUser();
    }
    if(isset($_POST['delete'])){
        //show user session id
        $user = new Userimp();
        $user->setId($_SESSION['id']);
        $user->deleteUser();
    }
?>
</body>
</html>