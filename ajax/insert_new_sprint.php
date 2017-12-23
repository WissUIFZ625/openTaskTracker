<?php


include_once '../include/pdoinit.php';
require_once '../include/utilities.php';
include_once '../include/loginfunctions.php';

//sec_session_start(); //Wird nur gebraucht, wenn User eingelogt sein muss auf der Seite, bei index.php beispielsweise nicht

$con = new connect_pdo();
$pdo = $con->dbh();

$error_msg = "";//needed?

$sprintname = "";
$projekt = "";

$output = false;


//Allgemeine Validierung
if (//Schmeisst Error, wenn leeres Formular versendet wird; bei anderen Feldern ists egal//Nicht mehr @.@

    val_san_input($_POST['sprintname'], $sprintname, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    &&

    val_san_input($_POST['projekt'], $projekt, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)


) {

    $stmt = $pdo->prepare
    (
        "SELECT blog_id FROM Backlog WHERE blog_pro_id = :IDPROJEKT LIMIT 1"
    );
    $stmt->bindParam(':IDPROJEKT', $projekt);

    $stmt->execute();

    $db_result = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_backlog = $db_result['blog_id'];

    if ($db_result != false) {

        $stmt = $pdo->prepare
        (
            "INSERT INTO Sprint (spr_blog_id, spr_name) VALUES (:IDBACKLOG, :SPRINTNAME)"
        );
        $stmt->bindParam(':IDBACKLOG', $id_backlog);
        $stmt->bindParam(':SPRINTNAME', $sprintname);

        if ($stmt->execute()) {

            $output = true;
        }


    } else {
        //echo 'Bedingungen nicht eingehalten';
        $output = false;
        exit();
    }
}

?>