<?php


include_once '../include/pdoinit.php';
require_once '../include/utilities.php';
include_once '../include/loginfunctions.php';

//sec_session_start(); //Wird nur gebraucht, wenn User eingelogt sein muss auf der Seite, bei index.php beispielsweise nicht

$con = new connect_pdo();
$pdo = $con->dbh();

$error_msg = "";//needed?

$titel = "";
$group = "";
$state = "";
$output = false;


//Allgemeine Validierung
if (//Schmeisst Error, wenn leeres Formular versendet wird; bei anderen Feldern ists egal//Nicht mehr @.@

    val_san_input($_POST['titel'], $titel, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    &&

    val_san_input($_POST['group'], $group, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
    &&
    val_san_input($_POST['state'], $state, FILTER_SANITIZE_STRING, FILTER_SANITIZE_FULL_SPECIAL_CHARS)

) {

    /*   var_dump($titel);
       return;*/
    $stmt = $pdo->prepare
    (
        "INSERT INTO Project (pro_name, pro_grp_id, pro_pst_id) VALUES (:TITEL, :GROUP, :STATE)"
    );
    $stmt->bindParam(':TITEL', $titel);
    $stmt->bindParam(':GROUP', $group);
    $stmt->bindParam(':STATE', $state);

    if ($stmt->execute()) {

        $stmt = $pdo->prepare
        (
            "SELECT pro_id FROM Project WHERE pro_name = :TITEL LIMIT 1"
        );
        $stmt->bindParam(':TITEL', $titel);

        $stmt->execute();

        $db_result = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_projekt = $db_result['pro_id'];

        if ($db_result != false) {

            $stmt = $pdo->prepare
            (
                "INSERT INTO Backlog (blog_pro_id) VALUES (:IDPROJEKT)"
            );
            $stmt->bindParam(':IDPROJEKT', $id_projekt);

            if ($stmt->execute()) {

                $stmt = $pdo->prepare
                (
                    "SELECT blog_id FROM Backlog WHERE blog_pro_id = :IDPROJEKT LIMIT 1"
                );
                $stmt->bindParam(':IDPROJEKT', $id_projekt);

                $stmt->execute();

                $db_result = $stmt->fetch(PDO::FETCH_ASSOC);
                $id_backlog = $db_result['blog_id'];

                if ($db_result != false) {

                    $stmt = $pdo->prepare
                    (
                        "INSERT INTO Sprint (spr_blog_id, spr_name) VALUES (:IDBACKLOG, 'Sprint 1')"
                    );
                    $stmt->bindParam(':IDBACKLOG', $id_backlog);

                    if ($stmt->execute()) {

                        $output = true;
                    }

                }

            } else {

            }
        }


    }


} else {
    //echo 'Bedingungen nicht eingehalten';
    $output = false;
    exit();
}


?>