<?php

include_once 'pdoinit.php';
include_once 'loginfunctions.php';
include_once 'usr_grp_functions.php';
include_once 'permission_functions.php';

sec_session_start();
$con = new connect_pdo();
$pdo = $con->dbh();

$error_msg = "";


if($maxAdmin == false){

    if (isset($_POST['username'], $_POST['email'], $_POST['p'])) {
        $first_usrtabl_stat = false;
        if (!inital_usr_exists($pdo)) {
            $first_usrtabl_stat = true;
        }
        // Sanitize and validate the data passed in
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Not a valid email
            $error_msg .= '<p class="error">Die von Ihnen eingegebene E-Mail-Adresse ist ungültig</p>';
        }

        $password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
        if (strlen($password) != 128) {
            // The hashed pwd should be 128 characters long.
            // If it's not, something really odd has happened
            $error_msg .= '<p class="error">ungültige Passwortkonfiguration</p>';
        }
        if ($stmt = $pdo->prepare
        (
            "SELECT id, username, password, salt, deactivatedon 
			FROM members
            WHERE email = :EMAIL LIMIT 1"
        )
        ) {
            $stmt->bindParam(':EMAIL', $email);
            $stmt->execute();
            $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
            $prep_stmt = "SELECT id FROM members WHERE email = :EMAIL LIMIT 1";
            $stmt = $pdo->prepare($prep_stmt);
            if ($stmt) {
                $stmt->bindParam(':EMAIL', $email);
                $stmt->execute();
                $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($dbresult !== false) {
                    // A user with this email address already exists
                    $error_msg .= '<p class="error">Ein Benutzer mit dieser E-Mail-Adresse ist bereits vorhanden.</p>';
                }
            } else {
                $error_msg .= '<p class="error">Database error</p>';
            }
            if (empty($error_msg)) {
                // Create a random salt
                $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

                // Create salted password
                $password = hash('sha512', $password . $random_salt);

                // Insert the new user into the database
                if ($insert_stmt = $pdo->prepare("INSERT INTO members (username, isBatman, email, password, salt, first_login) VALUES (:USERNAME, :ISBATMAN ,:EMAIL, :PASSWORD, :SALT, :FIRSTLOGIN)")) {
                    $isBatman = 1;
                    $insert_stmt->bindParam(':USERNAME', $username);
                    $insert_stmt->bindParam(':ISBATMAN', $isBatman);
                    $insert_stmt->bindParam(':EMAIL', $email);
                    $insert_stmt->bindParam(':PASSWORD', $password);
                    $insert_stmt->bindParam(':SALT', $random_salt);
                    $insert_stmt->bindParam(':FIRSTLOGIN', $isBatman);
                    // Execute the prepared query.
                    if (!$insert_stmt->execute()) {
                        header('Location: error.php?err=Registration failure: INSERT');
                        exit();
                    } else {
                        header('Location: register_success.php');
                        if ($stmt = $pdo->prepare("SELECT id FROM members WHERE email = :EMAIL LIMIT 1")) {
                            $stmt->bindParam(':EMAIL', $email);
                            if ($stmt->execute()) //if(!$stmt->execute())
                            {
                                $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
                                if (inital_usr_exists($pdo) && $first_usrtabl_stat) //Erstellter User ist InitialUser
                                {
                                    $usriddb = $dbresult['id'];
                                    //header('Location: http://www.google.ch');
                                    //exit();

                                    $admin_groupid = create_usergroup($pdo, 'InitialAdministrator', 'Initiale Administratorengruppe');
                                    if ($admin_groupid != false) //Erstelle Initiale Admingruppe
                                    {
                                        if (add_usr_grp($pdo, $admin_groupid, $usriddb)) // Füge Initialuser der Gruppe hinzu
                                        {
                                            $permission_sites = get_permission_sites($pdo);
                                            if ($permission_sites != false) {
                                                $status = array();
                                                for ($i = 0; $i < count($permission_sites); $i++) {
                                                    if (add_usrgroup_to_site($pdo, $admin_groupid, $permission_sites[$i]['id']) != false) {
                                                        //Ebenfalls User zum Schalten erstellen
                                                        //Initialusers auf MyopenTaskTracker mappen mit Konfigurationsrechten<--I.o.
                                                    } else {
                                                        $status[] = false;
                                                    }
                                                }
                                                if (in_array(false, $status)) {
                                                    header('Location: error.php?Registration failure:Could not set Sitepermissions for Group');
                                                    exit();
                                                } else {
                                                    if (add_groupperm_to_room($pdo, $admin_groupid, 0, 2)) //wird zu oft angesteuert-->Immer noch?
                                                    {
                                                        //return true;
                                                        header('Location: register_success.php');
                                                        exit();
                                                    } else {
                                                        //return false;
                                                        header('Location: error.php?Registration failure:Could not set Roompermission for Group');
                                                        exit();
                                                    }
                                                    //header('Location: register_success.php');
                                                    //exit();
                                                }
                                            } else {
                                                header('Location: error.php?Registration failure:Could not read Sitepermissions');
                                                exit();
                                            }
                                        } else {
                                            header('Location: error.php?Registration failure:Add User to Admingroup');
                                            exit();
                                        }
                                    } else {
                                        header('Location: error.php?Registration failure:Create Admingroup');
                                        exit();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

    }
}else{
    header("Location: index.php");
}