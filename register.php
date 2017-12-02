<?php

include_once 'include/process_register.php';
include_once 'include/loginfunctions.php';

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
    <link rel="stylesheet" href="css/screen.min.css"/>
    <link rel="stylesheet" href="css/register.css"/>
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/JavaScript" src="js/sha512.js"></script>
    <script type="text/JavaScript" src="js/forms.js"></script>
    <script type="text/JavaScript" src="js/index.js"></script>
</head>
<body> <!--Seite muss gesichert werden, damit man nur darauf zugreifen kann, wenn kein User existiert-->
<!-- Registration form to be output if the POST variables are not
set or if the registration script caused an error. -->

<div class="body"></div>
<div class="grad"></div>
<div class="header">
    <div >openTask<span></span></div>
    <p><div class=""><span>Tracker</span></div></p>
</div>

<div class="register">

    

    <ul>
        <li>Der Nickname darf nur Zahlen, Gross/Kleinbuchstaben und Underlines enthalten.</li>
        <li>Das Passwort muss mindestens 6 Zeichen enthalten.</li>
        <li>Bedingungen für das Passwort
            <ul>
                <li>Mindestens ein grosser Buchstabe (A-Z)</li>
                <li>Mindestens ein kleiner Buchstabe (a-z)</li>
                <li>Mindesten eine Zahl (0-9)</li>
            </ul>
        </li>
        <br>



    </ul>
    </ul>
    <div class="inputsregister">


        <form method="post" name="registration_form" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>">
            Nickname: <input placeholder="Nickname" type='text' name='username' id='username' class="input"><br>
            Passwort: <input placeholder="Password" type="password" name="password" id="password" class="input"><br>
            Passwort Wiederholen: <input placeholder="Password" type="password" name="confirmpwd" id="confirmpwd"
                                         class="input"><br>

            <input type="button"
                   value="Registrieren"
                   onclick="return regformhash(this.form,
                                   this.form.username,
                                   this.form.password,
                                   this.form.confirmpwd);">
        </form>
    </div>

</div>
</body>
</html>
