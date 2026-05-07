<?php
include "db.php";

if(isset($_POST['reset'])){

    $username = $_POST['Username'];
    $fav = $_POST['keyword'];
    $newpass = password_hash($_POST['NewPassword'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND keyword=?");
    $stmt->bind_param("ss",$username,$fav);
    $stmt->execute();

    $result = $stmt->get_result();

    if($result->num_rows > 0){

        $update = $conn->prepare("UPDATE users SET password=? WHERE username=?");
        $update->bind_param("ss",$newpass,$username);
        $update->execute();

        header("Location: index.html");
    } else {
        echo "Wrong details";
    }
}
?>