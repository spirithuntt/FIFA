<?php
// middleware to check if user is admin
class IslogedIn {
    public function __construct()
    {
        if($_SERVER['PHP_SELF'] != '/FIFA/login.php' && $_SERVER['PHP_SELF'] != '/FIFA/signup.php'){
            if(!isset($_SESSION['logged'])){
                header('location: ./login.php');
            }
        }
        else{
            if(isset($_SESSION['logged'])){
                // if page url is login.php redirect to index.php
                if($_SERVER['PHP_SELF'] == '/FIFA/login.php' || $_SERVER['PHP_SELF'] == '/FIFA/signup.php'){
                    header('location: ./index.php');
                }
            }
        }
    }
}