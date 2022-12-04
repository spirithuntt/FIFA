<?php
// getting the verified value from the url
if (isset($_GET['verified'])) {
    $verified = $_GET['verified'];
    if ($verified == 1) {
        echo "Your email has been verified";
    } else {
        echo "Your email could not be verified";
    }
}