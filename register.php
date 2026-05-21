









<?php
session_start();
include "db.php";

if(isset($_POST['save'])){

    // CHECK PASSWORD MATCH
    if($_POST['PassWord'] !== $_POST['PassWordConfirm']){
        die("Passwords do not match");
    }

    // GET FORM DATA
    $fullname = $_POST['Fname'];
    $dob = $_POST['dateofbirth'];
    $idnumber = $_POST['IDnumber'];
    $gender = $_POST['Gender'];
    $phone = $_POST['PhoneNumber'];
    $email = $_POST['Email'];

    $username = $_POST['UserName'];
    $password = password_hash($_POST['PassWord'], PASSWORD_DEFAULT);
    $keyword = $_POST['keyword'];

    // CHECK IF USERNAME EXISTS (VERY IMPORTANT)
    $check = $conn->prepare("SELECT username FROM users WHERE username=?");
    $check->bind_param("s",$username);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        die("Username already exists");
    }

    // INSERT INTO USERS
    $stmt1 = $conn->prepare("
        INSERT INTO users (username, password, keyword)
        VALUES (?, ?, ?)
    ");

    if(!$stmt1){
        die("Prepare failed (users): " . $conn->error);
    }

    $stmt1->bind_param("sss", $username, $password, $keyword);

    if(!$stmt1->execute()){
        die("User insert failed: " . $stmt1->error);
    }

    // INSERT INTO MEMBER DETAILS
    $stmt2 = $conn->prepare("
        INSERT INTO member_details 
        (username, fullname, gender, idnumber, phone, email, dob)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if(!$stmt2){
        die("Prepare failed (member_details): " . $conn->error);
    }

    $stmt2->bind_param(
        "sssssss",
        $username,
        $fullname,
        $gender,
        $idnumber,
        $phone,
        $email,
        $dob
    );

    if(!$stmt2->execute()){
        die("Member insert failed: " . $stmt2->error);
    }

    // ✅ DO NOT SET SESSION HEREs

    // ✅ REDIRECT TO LOGIN WITH SUCCESS MESSAGE
    header("Location: index.html?success=registered");
    exit();
}
?>