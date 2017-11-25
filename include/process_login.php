<?php

include_once 'pdoinit.php';
include_once 'permission_login_check.php';
include_once 'loginfunctions.php';
include_once 'permission_functions.php';


sec_session_start(); // Our custom secure way of starting a PHP session.
$con = new connect_pdo();
$pdo = $con->dbh();


if (isset($_POST['email'], $_POST['p'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['p']; // The hashed password.
  
    if (login($email, $password, $pdo) == true) {
        $firstLogin = 1;
    	//$url=get_user_url($pdo);
        $_SESSION['email'] = $email;

        $firstLogincheck = firstLogin($pdo, $firstLogin);

            if($firstLogincheck){

                header("Location: ../openTaskTracker.php");  //../connect ist korrekt !!!!!!!!!!!!
                exit();

            }else{
                header("Location: $url");
                exit();
            }


    } else {
        // Login failed 
        header('Location: ../index.php?error=1');
        exit();
    }
} else {
    // The correct POST variables were not sent to this page. 
    header('Location: ../error.php?err=Could not process login');
    exit();
}