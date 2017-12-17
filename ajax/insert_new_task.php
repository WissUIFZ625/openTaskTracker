<?php


include_once '../include/pdoinit.php';
require_once '../include/utilities.php';
include_once '../include/loginfunctions.php';

//sec_session_start(); //Wird nur gebraucht, wenn User eingelogt sein muss auf der Seite, bei index.php beispielsweise nicht

$con = new connect_pdo();
$pdo = $con->dbh();

$error_msg = "";//needed?

$titel = "";
$description = "";
$prio = "";
$output = false;


//Allgemeine Validierung
if (//Schmeisst Error, wenn leeres Formular versendet wird; bei anderen Feldern ists egal//Nicht mehr @.@

    val_san_input($_POST['titel'], $titel, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    &&

    val_san_input($_POST['description'], $description, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    &&
    val_san_input($_POST['prio'], $prio, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)

) {
    $stmt = $pdo->prepare
    (
        "INSERT INTO task (task_name, task_description, task_pri_id, task_tst_id) VALUES (:TITEL, :DESCRIPTION, :PRIO, 1)"
    );
    $stmt->bindParam(':TITEL', $titel);
    $stmt->bindParam(':DESCRIPTION', $description);
    $stmt->bindParam(':PRIO', $prio);

    if ($stmt->execute()) {

      $output = true;

    }


} else {
    //echo 'Bedingungen nicht eingehalten';
   $output = false;
    exit();
}


?>