<?php
include ("./controllers/Userimp.php");
include("./middlewares/isLoggedin.php");
$islogedin = new IslogedIn();
var_dump($_SESSION);
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFA 2022</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
        <link rel="stylesheet" href="assets/css/style.css">
                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"></script>
</head>

<body>
<main class="row col-12 mx-0">
        <div class="col-12 col-md-12 col-lg-4 col-sm-12 fullheight background px-5 bg-danger">
            <div class="d-flex flex-column justify-content-center mt-5 sh">                        
                <?php 

                if(isset($_POST['submit'])){
                    $user = new Userimp;
                    $user->setFirst_name($_POST['fname']);
                    $user->setLast_name($_POST['lname']);
                    $user->setEmail($_POST['email']);
                    $user->setPassword($_POST['password']);
                    $user->setPassword_com($_POST['verifypassword']);
                    $user->create();
                }
                ?>
                <h1 class="text-start text-white">Sign up</h1>
                <p class="text-white mb-4">Login with your account to access</p>
            <form method="POST">
            <div class="mb-3">
                    <label  class="form-label text-white">first name</label>
                    <input type="name" class="input form-control border border-dark" id="fname" name="fname">
                </div>
                <div class="mb-3">
                    <label  class="form-label text-white">last name</label>
                    <input type="name" class="input form-control border border-dark" id="lname" name="lname">
                </div>
                <div class="mb-3">
                    <label  class="form-label text-white">Email address</label>
                    <input type="email" class="input form-control border border-dark" id="email" aria-describedby="emailHelp" name="email">
                </div>
                <div class="mb-3">
                    <label  class="form-label text-white">Password</label>
                    <input type="password" class="input form-control border border-dark" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label  class="form-label text-white">repeat password</label>
                    <input type="password" class="input form-control border border-dark" id="verifypassword" name="verifypassword">
                </div>
                <div class="d-grid">
                    <button class="btn btn-danger mt-3" type="submit" name="submit">Sign Up</button>
                </div>
                <div class="mt-3">
                <p class="mb-0  text-center text-light">You already have an account? <a
                        class="text-white fw-bold"> Log In</a></p>
            </div>
            </form>
            </div>
        </div>
        <div class="col-8 d-lg-block d-none d-md-none"  style="height: 100vh !important;background-size: cover;background-image: url(assets/img/img.jpg); background-repeat: no-repeat; background-position:center ">
            
        </div>
        </div>
    </div>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa"></script>
</body>

</html>