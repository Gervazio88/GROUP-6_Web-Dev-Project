<?php
$conn = mysqli_connect("localhost", "root", "", "village_bank");

if (!$conn) {
    die("Connection failed" . mysqli_connect_error());
}

?>