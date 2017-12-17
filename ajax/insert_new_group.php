<?php


include_once '../include/pdoinit.php';
require_once '../include/utilities.php';
include_once '../include/loginfunctions.php';

//sec_session_start(); //Wird nur gebraucht, wenn User eingelogt sein muss auf der Seite, bei index.php beispielsweise nicht

$con = new connect_pdo();
$pdo = $con->dbh();

$error_msg = "";//needed?

$groupname = "";

$output = false;


//Allgemeine Validierung
if (//Schmeisst Error, wenn leeres Formular versendet wird; bei anderen Feldern ists egal//Nicht mehr @.@

    val_san_input($_POST['groupname'], $groupname, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)

) {
    $stmt = $pdo->prepare
    (
        "INSERT INTO `group` (grp_id, `grp_name`) VALUES (NULL, :GROUPNAME)"
    );
    $stmt->bindParam(':GROUPNAME', $groupname);

    if ($stmt->execute()) {

      $output = true;

    }else{
        $output = false;
    }

} else {
    //echo 'Bedingungen nicht eingehalten';
   $output = false;
    exit();
}


?>