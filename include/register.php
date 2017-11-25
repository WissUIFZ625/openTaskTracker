<?php

include_once 'Practiframe/include/process_register.php';
include_once 'Practiframe/include/loginfunctions.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
    <link rel="stylesheet" href="Practiframe/styles/main.css"/>
    <link rel="stylesheet" href="Practiframe/styles/styleregister.css"/>
    <script src="Practiframe/bower_components/jquery/dist/jquery.min.js"></script>
    <script type="text/JavaScript" src="Practiframe/js/sha512.js"></script>
    <script type="text/JavaScript" src="Practiframe/js/forms.js"></script>
    <script type="text/JavaScript" src="Practiframe/js/index.js"></script>
</head>
<body> <!--Seite muss gesichert werden, damit man nur darauf zugreifen kann, wenn kein User existiert-->
<!-- Registration form to be output if the POST variables are not
set or if the registration script caused an error. -->

<div class="body"></div>
<div class="grad"></div>
<div class="header">
    <img class="col-sm-2 dropdown topnavitem" style="height: 63px; width: auto; top: -19px;"
         src="Practiframe/img/xaxada.png" )">
    <br>
    <div style="margin-left:90px;">lo<span>kal</span></div>
</div>

<div class="register">


    <h1>Nun erstellen wir Ihr lokales Konto</h1>
    <h2 class="error">Notieren Sie sich Ihre Angaben für den späteren Gebrauch.</h2>
    <h1></h1>

    <ul>
        <li>Der Nickname darf nur Zahlen, Gross/Kleinbuchstaben und Underlines enthalten.</li>
        <li>Die Email-Adresse muss gültig sein. <br>(E-Mail wird später an diese Adresse versendet und ist Ihr
            zukünftiges lokales Login)
        </li>
        <li>Das Passwort muss mindestens 6 Zeichen enthalten.</li>
        <li>Bedingungen für das Passwort
            <ul>
                <li>Mindestens ein grosser Buchstabe (A-Z)</li>
                <li>Mindestens ein kleiner Buchstabe (a-z)</li>
                <li>Mindesten eine Zahl (0-9)</li>
            </ul>
        </li>
        <br>

        <?php echo $error_msg ?>

    </ul>
    </ul>
    <div class="test">


        <form method="post" name="registration_form" action="<?php echo esc_url($_SERVER['PHP_SELF']); ?>">
            Nickname: <input placeholder="Nickname" type='text' name='username' id='username' class="input"><br>
            E-mail: <input placeholder="E-Mail" type="text" name="email" id="email" class="input"><br>
            Passwort: <input placeholder="Password" type="password" name="password" id="password" class="input"><br>
            Passwort Wiederholen: <input placeholder="Password" type="password" name="confirmpwd" id="confirmpwd"
                                         class="input"><br>

            <input type="button"
                   value="Registrieren"
                   onclick="return regformhash(this.form,
                                   this.form.username,
                                   this.form.email,
                                   this.form.password,
                                   this.form.confirmpwd);">
        </form>
    </div>

</div>
</body>
</html>
