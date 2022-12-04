<?php
// is admin middleware
class IsAdmin {
    public function __construct()
    {
        if($_SESSION['isadmin'] == 0){
            header('location: ./index.php');
        }
    }
}