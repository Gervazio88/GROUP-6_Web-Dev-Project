<?php
include "db.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$page = $_GET['page'] ?? 'members';
?>

<!DOCTYPE html>
<html>
<head>
<title>Treasurer Dashboard</title>

<style>
body{font-family:Arial;}
.container{display:flex;}
.sidebar{width:220px;background:#2c3e50;padding:15px;}
.sidebar a{display:block;color:white;padding:10px;text-decoration:none;}
.sidebar a:hover{background:#34495e;}
.content{flex:1;padding:20px;}
.card{border:1px solid #ccc;padding:10px;margin:10px 0;}
.success{color:green;}
.error{color:red;}
.warning{color:orange;}
</style>

</head>

<body>

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
<h3 style="color:white;">Treasurer</h3>
<a href="?page=members">Members</a>
<a href="?page=savings">Add Savings</a>
<a href="?page=repay">Loan Repay</a>
<a href="?page=loans">Loans</a>
<a href="?page=reports">Reports</a>
<a href="index.html">Logout</a>
</div>

<div class="content">

<?php
/* ================= MEMBERS ================= */
if($page=="members"){

$res=$conn->query("
SELECT m.fullname,

IFNULL((SELECT SUM(s.amount) FROM savings s WHERE s.username=m.username),0) as savings,

IFNULL((
    SELECT SUM(l.amount + l.interest + IFNULL(l.fine,0) - l.paid)
    FROM loans l
    WHERE l.username=m.username AND l.status='approved'
),0) as loan

FROM member_details m
");

echo "<h2>Members Summary</h2>";

echo "<table border=1>
<tr><th>Name</th><th>Savings</th><th>Loan Balance</th></tr>";

while($r=$res->fetch_assoc()){
echo "<tr>
<td>{$r['fullname']}</td>
<td>MK ".number_format($r['savings'],2)."</td>
<td>MK ".number_format($r['loan'],2)."</td>
</tr>";
}

echo "</table>";
}


/* ================= ADD SAVINGS ================= */
elseif($page=="savings"){

if(isset($_POST['save'])){
$username=$_POST['username'];
$amount=$_POST['amount'];
$date=date("Y-m-d");

$user=$conn->query("SELECT fullname FROM member_details WHERE username='$username'")->fetch_assoc();

$conn->query("
INSERT INTO savings(username,date,amount,total)
VALUES('$username','$date','$amount','$amount')
");

$conn->query("
UPDATE bank_funds 
SET total = total + $amount 
WHERE id=1
");

$conn->query("
INSERT INTO transactions(username,fullname,type,amount,balance,date)
VALUES('$username','{$user['fullname']}','savings','$amount','$amount','$date')
");

echo "<p class='success'>Savings added successfully</p>";
}
?>

<h2>Add Savings</h2>
<form method="POST">
Username: <input name="username" required><br><br>
Amount: <input name="amount" type="number" required min=1000><br><br>
<button name="save">Add Savings</button>
</form>

<?php
}


/* ================= REPAYMENT ================= */
elseif($page=="repay"){

if(isset($_POST['pay'])){
$username=$_POST['username'];
$amount=$_POST['amount'];
$date=date("Y-m-d");

$loan=$conn->query("
SELECT * FROM loans 
WHERE username='$username' AND status='approved'
")->fetch_assoc();

if(!$loan){
echo "<p class='error'>No active loan found</p>";
}
else{

$fine = $loan['fine'] ?? 0;
$total = $loan['amount'] + $loan['interest'] + $fine;

$remaining = $total - $loan['paid'];

if($amount > $remaining){
echo "<p class='error'>Cannot pay more than remaining balance (MK ".number_format($remaining,2).")</p>";
}
else{

$new_paid = $loan['paid'] + $amount;

$conn->query("
UPDATE loans 
SET paid='$new_paid'
WHERE id='{$loan['id']}'
");

$conn->query("
UPDATE bank_funds 
SET total = total + $amount 
WHERE id=1
");

if($new_paid >= $total){
$conn->query("UPDATE loans SET status='closed' WHERE id='{$loan['id']}'");
}

$conn->query("
INSERT INTO transactions(username,fullname,type,amount,balance,date)
VALUES('$username','$username','loan repay','$amount','$new_paid','$date')
");

echo "<p class='success'>Payment recorded</p>";
}
}
}
?>

<h2>Loan Repayment</h2>
<form method="POST">
Username: <input name="username" required><br><br>
Amount: <input name="amount" type="number" required min=5000><br><br>
<button name="pay">Repay Loan</button>
</form>

<?php
}


/* ================= LOANS ================= */
elseif($page=="loans"){

/* ================= APPROVE / REJECT LOGIC FIXED ================= */
if(isset($_GET['approve'])){
$id=$_GET['approve'];

$loan=$conn->query("SELECT * FROM loans WHERE id='$id'")->fetch_assoc();

/* CHECK EXISTING ACTIVE LOAN */
$check=$conn->query("
SELECT * FROM loans 
WHERE username='{$loan['username']}'
AND status='approved'
AND (amount+interest+IFNULL(fine,0)-paid)>0
");

/* BANK TOTAL */
$bank=$conn->query("SELECT total FROM bank_funds WHERE id=1")->fetch_assoc();
$bank_total=$bank['total'] ?? 0;

/* ================= REJECT: EXISTING LOAN ================= */
if($check->num_rows > 0){

$conn->query("
UPDATE loans 
SET status='rejected',
message='Loan rejected due to existing loan balance'
WHERE id='$id'
");

echo "<p class='error'>Rejected: Existing loan balance</p>";
exit();
}

/* ================= REJECT: INSUFFICIENT FUNDS ================= */
elseif($bank_total < $loan['amount']){

$conn->query("
UPDATE loans 
SET status='rejected',
message='Loan rejected due to insufficient funds'
WHERE id='$id'
");

echo "<p class='error'>Rejected: Insufficient funds</p>";
exit();
}

/* ================= APPROVE LOAN ================= */
else{

$conn->query("
UPDATE loans 
SET status='approved',
due_date=DATE_ADD(CURDATE(), INTERVAL 14 DAY)
WHERE id='$id'
");

$conn->query("
UPDATE bank_funds 
SET total = total - {$loan['amount']}
WHERE id=1
");

echo "<p class='success'>Loan approved</p>";
exit();
}
}


/* ================= ADD FINE ================= */
if(isset($_POST['add_fine'])){
$id=$_POST['loan_id'];
$fine=$_POST['fine'];

$conn->query("
UPDATE loans 
SET fine = IFNULL(fine,0) + $fine
WHERE id='$id'
");

echo "<p class='warning'>Fine added successfully</p>";
exit();
}


/* ================= REMINDER ================= */
if(isset($_GET['remind'])){
$id=$_GET['remind'];

$conn->query("
UPDATE loans 
SET reminder='Reminder sent on ".date("Y-m-d H:i:s")."'
WHERE id='$id'
");

echo "<p class='warning'>Reminder sent</p>";
exit();
}


/* ================= LOANS LIST ================= */
$res=$conn->query("
SELECT l.*, m.fullname 
FROM loans l
JOIN member_details m ON l.username=m.username
WHERE l.status='approved'
");

echo "<h2>Loans</h2>";

while($r=$res->fetch_assoc()){

$fine = $r['fine'] ?? 0;
$total = $r['amount'] + $r['interest'] + $fine;
$remaining = $total - $r['paid'];

$overdue = ($r['due_date'] < date("Y-m-d") && $remaining > 0);

echo "<div class='card'>";

echo "<b>{$r['fullname']}</b><br>";
echo "Amount: MK ".number_format($r['amount'],2)."<br>";
echo "Interest: MK ".number_format($r['interest'],2)."<br>";
echo "Fine: MK ".number_format($fine,2)."<br>";
echo "Remaining: MK ".number_format($remaining,2)."<br>";
echo "Due: {$r['due_date']}<br>";

echo "
<form method='POST'>
<input type='hidden' name='loan_id' value='{$r['id']}'>
<input type='number' name='fine' placeholder='Add fine'>
<button name='add_fine'>Add Fine</button>
</form>
";

if($overdue){
echo "<span class='error'>OVERDUE</span><br>";
echo "<a href='?page=loans&remind={$r['id']}'>Send Reminder</a>";
}

echo "</div>";
}


/* ================= PENDING ================= */
$res2=$conn->query("SELECT * FROM loans WHERE status='pending'");

echo "<h2>Pending Loans</h2>";

while($r=$res2->fetch_assoc()){
echo "<div class='card'>";
echo "{$r['username']} - MK {$r['amount']}<br>";
echo "<a href='?page=loans&approve={$r['id']}'>Approve</a>";
echo "</div>";
}
}


/* ================= REPORTS ================= */
elseif($page=="reports"){

$res=$conn->query("SELECT * FROM transactions ORDER BY id DESC");

echo "<h2>Reports</h2>";

echo "<table border=1>
<tr><th>Date</th><th>Name</th><th>Type</th><th>Amount</th><th>Balance</th></tr>";

while($r=$res->fetch_assoc()){
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
