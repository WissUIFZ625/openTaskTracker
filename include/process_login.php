<?php

include_once 'pdoinit.php';
include_once 'permission_login_check.php';
include_once 'loginfunctions.php';
include_once 'permission_functions.php';


sec_session_start(); // Our custom secure way of starting a PHP session.
$con = new connect_pdo();
$pdo = $con->dbh();


if (isset($_POST['nickname'], $_POST['p'])) {
    $username = filter_input(INPUT_POST, 'nickname', FILTER_SANITIZE_STRING);
    $password = $_POST['p']; // The hashed password.
  
    if (login($username, $password, $pdo) == true) {
        $firstLogin = 1;
    	//$url=get_user_url($pdo);
        $_SESSION['username'] = $username;

        //$firstLogincheck = firstLogin($pdo, $firstLogin);


                header("Location: ../openTaskTracker.php");
                exit();


    } else {
        // Login failed 
        header('Location: ../login.php?error=1');
        exit();
    }
} else {
    // The correct POST variables were not sent to this page. 
    header('Location: ../error.php?err=Could not process login');
    exit();
}