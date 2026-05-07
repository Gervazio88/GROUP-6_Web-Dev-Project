<?php
session_start();
include "db.php";

if(!isset($_SESSION['username'])){
    header("Location: index.html");
    exit();
}

$username = $_SESSION['username'];

$user = $conn->query("
    SELECT * FROM member_details WHERE username='$username'
")->fetch_assoc();

$page = $_GET['page'] ?? 'info';
?>

<!DOCTYPE html>
<html>
<head>
<title>Member Dashboard</title>
<style>
body{font-family:Arial;}
.container{display:flex;}
.sidebar{width:220px;background:#2c3e50;padding:15px;}
.sidebar a{display:block;color:white;padding:10px;text-decoration:none;}
.sidebar a:hover{background:#34495e;}
.content{flex:1;padding:20px;}
.card{border:1px solid #ccc;padding:10px;margin:10px 0;}
input,button{padding:6px;margin:5px 0;}
</style>
</head>

<body>
<div class="container">
<!-- SIDEBAR -->
<div class="sidebar">
<h3 style="color:white;">My Account</h3>
<a href="?page=info">My Information</a>
<a href="?page=savings">My Savings</a>
<a href="?page=loan">My Loan</a>
<a href="?page=apply">Loan Application</a>
<a href="index.html">Logout</a>
</div>

<div class="content">

<?php
/* ================= INFO ================= */
if($page=="info"){
?>
<h2>My Information</h2>

<p><strong>Full Name:</strong> <?= $user['fullname'] ?></p>
<p><strong>Phone:</strong> <?= $user['phone'] ?></p>
<p><strong>DOB:</strong> <?= $user['dob'] ?></p>
<p><strong>Gender:</strong> <?= $user['gender'] ?></p>
<p><strong>Email:</strong> <?= $user['email'] ?></p>

<?php
}

/* ================= SAVINGS ================= */
elseif($page=="savings"){

$res=$conn->query("
SELECT SUM(amount) as total_savings 
FROM savings 
WHERE username='$username'
");

$total=$res->fetch_assoc()['total_savings'] ?? 0;

$history=$conn->query("
SELECT * FROM savings 
WHERE username='$username' 
ORDER BY id DESC
");
?>

<h2>My Savings</h2>

<h3>Total Savings: MK <?= number_format($total,2) ?></h3>

<table border="1">
<tr><th>Date</th><th>Amount</th></tr>

<?php while($r=$history->fetch_assoc()){ ?>
<tr>
<td><?= $r['date'] ?></td>
<td>MK <?= number_format($r['amount'],2) ?></td>
</tr>
<?php } ?>

</table>

<?php
}

/* ================= LOAN (FIXED SECTION) ================= */
elseif($page=="loan"){

/* ACTIVE LOAN */
$activeLoan = $conn->query("
SELECT * FROM loans 
WHERE username='$username' 
AND status IN ('approved','closed')
ORDER BY id DESC 
LIMIT 1
")->fetch_assoc();

/* REJECTED LOAN */
$rejectedLoan = $conn->query("
SELECT * FROM loans 
WHERE username='$username' 
AND status='rejected'
ORDER BY id DESC 
LIMIT 1
")->fetch_assoc();

echo "<h2>My Loan</h2>";

/* ================= ACTIVE LOAN ================= */
if($activeLoan){

$fine = $activeLoan['fine'] ?? 0;

$totalLoan = $activeLoan['amount'] + $activeLoan['interest'] + $fine;
$balance = $totalLoan - $activeLoan['paid'];

if($balance < 0) $balance = 0;

if(!empty($activeLoan['reminder'])){
    echo "<p style='color:orange;'><b>{$activeLoan['reminder']}</b></p>";
}

echo "<p><b>Loan Amount:</b> MK ".number_format($activeLoan['amount'],2)."</p>";
echo "<p><b>Interest:</b> MK ".number_format($activeLoan['interest'],2)."</p>";
echo "<p><b>Fine:</b> MK ".number_format($fine,2)."</p>";
echo "<p><b>Paid:</b> MK ".number_format($activeLoan['paid'],2)."</p>";
echo "<p><b>Remaining Balance:</b> MK ".number_format($balance,2)."</p>";
echo "<p><b>Due Date:</b> {$activeLoan['due_date']}</p>";
}

/* ================= REJECTED LOAN (DO NOT OVERWRITE ACTIVE) ================= */
if($rejectedLoan){

$rawReason = strtolower($rejectedLoan['message'] ?? '');
$reasonText = "Loan rejected due to system rules or eligibility checks";

/* clean mapping */
if(strpos($rawReason, 'insufficient') !== false){
    $reasonText = "Loan rejected due to insufficient funds in the bank";
}
elseif(strpos($rawReason, 'existing') !== false && strpos($rawReason, 'loan') !== false){
    $reasonText = "Loan rejected due to existing active loan balance";
}
elseif(strpos($rawReason, 'balance') !== false){
    $reasonText = "Loan rejected due to unpaid loan balance";
}
elseif($rawReason == "insufficient_funds"){
    $reasonText = "Loan rejected due to insufficient funds in the bank";
}
elseif($rawReason == "existing_loan"){
    $reasonText = "Loan rejected due to existing active loan balance";
}
elseif($rawReason == "existing_balance"){
    $reasonText = "Loan rejected due to unpaid loan balance";
}

echo "<hr>";
echo "<p style='color:red;'>
❌ <b>Latest Rejected Loan</b><br>
<b>Reason:</b> $reasonText
</p>";
}

/* ================= NO LOAN ================= */
if(!$activeLoan && !$rejectedLoan){
    echo "<p>No loan record found</p>";
}
}

/* ================= APPLY LOAN ================= */
elseif($page=="apply"){

if(isset($_POST['apply'])){
$amount=$_POST['amount'];
$interest = 0.2 * $amount;

$conn->query("
INSERT INTO loans(username,amount,interest,status,paid)
VALUES('$username','$amount','$interest','pending',0)
");

echo "<p style='color:green;'>Loan request submitted</p>";
}
?>

<h2>Loan Application</h2>

<form method="POST">
<label>Amount:</label><br>
<input type="number" name="amount" required min="5000"><br><br>
<button name="apply">Apply</button>
</form>

<?php } ?>

</div>
</div>

</body>
</html>
