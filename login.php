<?php

include_once 'include/pdoinit.php';
include_once 'include/loginfunctions.php';
include_once 'include/usr_grp_functions.php';

sec_session_start();

//DB-Initialisierung
$con = new connect_pdo();
$pdo = $con->dbh();


?>
<!DOCTYPE html>

<html>
<head>
    <title>Scrum Tool</title>
    <link rel="stylesheet" href="css/screen.min.css"/>
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/JavaScript" src="js/sha512.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script type="text/JavaScript" src="js/index.js"></script>
</head>

<body>


<?php
if (isset($_GET['error'])) {
    echo '<p class="error">Error Logging In!</p>';
}
?>
<div class="body"></div>
<div class="grad"></div>
<div class="header">

    <div >openTask<span></span></div>
    <p><div class=""><span>Tracker</span></div></p>
</div>


<div class="login">

    <form action="include/process_login.php" method="post" name="login_form">
        <input type="text" placeholder="Nickname" name="nickname"/><br>
        <input type="password"
               name="password"
               placeholder="Password"
               id="password"/><br>

        <input type="button"
               value="Login"
               onclick="formhash(this.form, this.form.password);"/>
    </form>


    <p class="link_register">Sie haben noch kein Login?<a href="register.php"> Registrieren</a> Sie sich</p>

</div>
</body>
</html>