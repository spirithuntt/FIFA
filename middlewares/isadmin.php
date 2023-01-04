<?php
// to check if its admin 
class IsAdmin {
    public function __construct()
    {
        if($_SESSION['isadmin'] == 0){
            header('location: ./index.php');
        }
    }
}