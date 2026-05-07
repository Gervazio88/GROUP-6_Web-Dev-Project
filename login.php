<?php
// Force display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Check if session is already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php";

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "<script>alert('Please fill all fields'); window.location.href='index.html';</script>";
        exit();
    }

    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Verify password (hashed)
        if (password_verify($password, $user['password'])) {

            // Store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];


            if ($user['role'] == "member") {

        $check = $conn->query("
            SELECT status 
            FROM member_details 
            WHERE username = '{$user['username']}'
        ");

        if ($check->num_rows == 0) {
            echo "<script>alert('Member record not found'); window.location.href='index.html';</script>";
            exit();
        }

        $row = $check->fetch_assoc();

        if ($row['status'] != 'approved') {
            echo "<script>alert('Your account is pending approval by the chairperson'); window.location.href='index.html';</script>";
            exit();
        }
    }

            // Role-based redirection
            switch ($user['role']) {
                case "member":
                    header("Location: member_dashboard.php");
                    break;
                case "treasurer":
                    header("Location: treasurer_dashboard.php");
                    break;
                case "chairperson":
                    header("Location: chair_dashboard.php");
                    break;
                default:
                    echo "<script>alert('Invalid role assigned'); window.location.href='index.html';</script>";
                    break;
            }
            exit();

        } else {
            echo "<script>alert('Incorrect password'); window.location.href='index.html';</script>";
            exit();
        }

    } else {
        echo "<script>alert('User not found'); window.location.href='index.html';</script>";
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>