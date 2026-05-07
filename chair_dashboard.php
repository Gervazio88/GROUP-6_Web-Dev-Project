<?php
session_start();
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['role']) || $_SESSION['role'] != "chairperson"){
    header("Location: index.html");
    exit();
}

$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html>
<head>
<title>Chairperson Dashboard</title>

<style>
body{
    font-family:Arial;
    margin:0;
    height:100vh;
    overflow:hidden; 
}

.container{
    display:flex;
    height:100vh;
}


.sidebar{
    width:220px;
    background:#2c3e50;
    padding:15px;
    position:fixed;
    left:0;
    top:0;
    bottom:0;

    box-sizing:border-box;
    overflow-y:auto;
}


.content{
    margin-left:220px; 
    flex:1;
    padding:20px;
    height:100vh;
    overflow-y:auto;
}

.sidebar a{
    display:block;
    color:white;
    padding:10px;
    text-decoration:none;
}

.sidebar a:hover{
    background:#34495e;
}

.card{
    border:1px solid #ccc;
    padding:10px;
    margin:10px 0;
}
</style>

</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
<h3 style="color:white;">Chairperson</h3>

<a href="?page=dashboard">Dashboard</a>
<a href="?page=members">Members</a>
<a href="?page=approvals">Approvals</a>
<a href="?page=loans">Loan Oversight</a>
<a href="?page=reports">Reports</a>
<a href="index.html">Logout</a>

</div>

<div class="content">

<?php

if($page == "dashboard"){

$totalMembers = $conn->query("
SELECT COUNT(*) as total 
FROM member_details 
WHERE status='approved'
")->fetch_assoc()['total'] ?? 0;

$bank = $conn->query("
SELECT total FROM bank_funds WHERE id=1
")->fetch_assoc()['total'] ?? 0;

$totalLoans = $conn->query("
SELECT SUM(amount) as total 
FROM loans 
WHERE status='approved'
")->fetch_assoc()['total'] ?? 0;

$outstanding = $conn->query("
SELECT SUM(amount + interest + IFNULL(fine,0) - paid) as total 
FROM loans 
WHERE status='approved'
")->fetch_assoc()['total'] ?? 0;

echo "<h2>Dashboard Overview</h2>";

echo "<div class='card'>👥 Members: $totalMembers</div>";
echo "<div class='card'>🏦 Total Bank Funds: MK ".number_format($bank,2)."</div>";
echo "<div class='card'>💳 Loans: MK ".number_format($totalLoans ?? 0,2)."</div>";
echo "<div class='card'>⚠️ Outstanding: MK ".number_format($outstanding ?? 0,2)."</div>";
}
elseif($page == "members"){

if(isset($_GET['delete'])){
$id = $_GET['delete'];
$conn->query("DELETE FROM member_details WHERE id='$id'");
echo "<p class='success'>Member deleted</p>";
}

$res = $conn->query("SELECT * FROM member_details WHERE status='approved'");

echo "<h2>Members</h2>";

while($r = $res->fetch_assoc()){
echo "<div class='card'>";
echo "<b>{$r['fullname']}</b><br>";
echo "{$r['phone']}<br>";
echo "<a href='?page=members&delete={$r['id']}'>Delete</a>";
echo "</div>";
}
}
elseif($page == "approvals"){

if(isset($_GET['approve'])){
$id = $_GET['approve'];


$count = $conn->query("
SELECT COUNT(*) as total 
FROM member_details 
WHERE status='approved'
")->fetch_assoc()['total'] ?? 0;

if($count >= 40){
$conn->query("UPDATE member_details SET status='rejected' WHERE id='$id'");
echo "<p class='error'>Group is full (40 members reached)</p>";
}else{
$conn->query("UPDATE member_details SET status='approved' WHERE id='$id'");
echo "<p class='success'>Member approved</p>";
}
}

if(isset($_GET['reject'])){
$id = $_GET['reject'];
$conn->query("UPDATE member_details SET status='rejected' WHERE id='$id'");
echo "<p class='error'>Member rejected</p>";
}

$res = $conn->query("SELECT * FROM member_details WHERE status='pending'");

echo "<h2>Pending Approvals</h2>";

while($r = $res->fetch_assoc()){
echo "<div class='card'>";
echo "<b>{$r['fullname']}</b><br>";
echo "{$r['phone']}<br>";
echo "<a href='?page=approvals&approve={$r['id']}'>Approve</a> | ";
echo "<a href='?page=approvals&reject={$r['id']}'>Reject</a>";
echo "</div>";
}
}
elseif($page == "loans"){

$res = $conn->query("
SELECT l.*, m.fullname 
FROM loans l
JOIN member_details m ON l.username = m.username
");

echo "<h2>Loan Oversight</h2>";

while($r = $res->fetch_assoc()){

$fine = $r['fine'] ?? 0;
$total = $r['amount'] + $r['interest'] + $fine;
$remaining = $total - $r['paid'];

echo "<div class='card'>";
echo "<b>{$r['fullname']}</b><br>";
echo "Amount: MK ".number_format($r['amount'],2)."<br>";
echo "Remaining: MK ".number_format($remaining ?? 0,2)."<br>";
echo "Status: {$r['status']}<br>";
echo "Due: {$r['due_date']}";
echo "</div>";
}
}
elseif($page == "reports"){

$res = $conn->query("SELECT * FROM transactions ORDER BY id DESC");

echo "<h2>Reports</h2>";

echo "<table border='1'>
<tr><th>Date</th><th>Name</th><th>Type</th><th>Amount</th><th>Balance</th></tr>";

while($r = $res->fetch_assoc()){
echo "<tr>
<td>{$r['date']}</td>
<td>{$r['fullname']}</td>
<td>{$r['type']}</td>
<td>MK {$r['amount']}</td>
<td>MK {$r['balance']}</td>
</tr>";
}

echo "</table>";
}
?>

</div>
</div>

</body>
</html>