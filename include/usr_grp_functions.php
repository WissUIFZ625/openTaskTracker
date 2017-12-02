<?php

class Defaultvalues {

    const DEFAULT_ADMIN = 2 ;// 1 = Admin // 2 = für Live Schaltung (DB Flag first_login)
}


function toDateTime($unixTimestamp) //I.O.
{
    return date("Y-m-d H:i:s", $unixTimestamp);
}
function is_arr_num($arr) //I.O.
{
	if((isset($arr)) AND (is_array($arr)) AND (array_key_exists(0,$arr)))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function grp_is_usrgrp($mysqlcon, $grp_id) //I.O. // Ist nur nötig, wenn Gruppentypen vorhanden
{
		$stmt = $mysqlcon->prepare("SELECT type FROM location WHERE location_id =:GRP_ID LIMIT 1");
		$stmt->bindParam(':GRP_ID', $grp_id);
		$stmt->execute();   // Execute the prepared query.
		$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if(($dbresult['type'] == 2) || ($dbresult['type'] == 4))
		{
			return true;
		}
		else
		{
			return false;
		}
}

function show_usrs($mysqlcon, $fetchmode = NULL) //NULL wird zu "0"-->PDO::FETCH_BOTH (Ist default), "1"--> Nummeric Array, "2"-->Associative Array //I.O.
{
	$stmt = $mysqlcon->prepare("SELECT usr_id, usr_name FROM user");
	$stmt->execute();   // Execute the prepared query.
    $dbresult = NULL;
    switch ($fetchmode)
    {
        case 0:
                $dbresult = $stmt->fetchAll(PDO::FETCH_BOTH);
                break;
        case 1:
                $dbresult = $stmt->fetchAll(PDO::FETCH_NUM);
                break;
        case 2:
                $dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        default:
            	$dbresult = $stmt->fetchAll(PDO::FETCH_NUM);
                break;
    }
    return $dbresult;
}
function show_grps($mysqlcon, $type = NULL, $fetchmode = NULL) //NULL wird zu "0"-->PDO::FETCH_BOTH (Ist default), "1"--> Nummeric Array, "2"-->Associative Array //I.O.
{
	$stmt = NULL;
	if(!isset($type))
	{
		$type = 2;
	}
	
	if($type == '!2')
	{
		$stmt = $mysqlcon->prepare("SELECT location_id, name, type, parent_id, deactivatedon FROM location WHERE type !=:TYPE AND type !=:SINGLGRP ");
		$type = 2;
		$stmt->bindParam(':TYPE', $type);
		$exclude_singlegrp = 4;
		$stmt->bindParam(':SINGLGRP', $exclude_singlegrp);
	}
	else
	{
		$stmt = $mysqlcon->prepare("SELECT location_id, name, type, parent_id, deactivatedon FROM location WHERE type =:TYPE AND type !=:SINGLGRP ");
		$stmt->bindParam(':TYPE', $type);
		$exclude_singlegrp = 4;
		$stmt->bindParam(':SINGLGRP', $exclude_singlegrp);
	}
	$stmt->execute();   // Execute the prepared query.
    $dbresult = NULL;
    switch ($fetchmode)
    {
        case 0:
                $dbresult = $stmt->fetchAll(PDO::FETCH_BOTH);
                break;
        case 1:
                $dbresult = $stmt->fetchAll(PDO::FETCH_NUM);
                break;
        case 2:
                $dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        default:
            	$dbresult = $stmt->fetchAll(PDO::FETCH_NUM);
                break;
    }
    return $dbresult;
}
function inital_usr_exists ($mysqlcon) //I.O.
{
    if(count(show_usrs($mysqlcon)) > 0)
    {
        return true;
    }
    else
    {
        return false;
    }
}
function delete_usr($mysqlcon, $usrid)// I.O.
{
	if ($stmt = $mysqlcon->prepare("DELETE FROM members WHERE id = :ID LIMIT 1"))
	{
		$stmt->bindParam(':ID', $usrid);
		$stmt->execute();
		if ($slcstmt = $mysqlcon->prepare("Select * FROM members WHERE id = :ID LIMIT 1"))
		{
			$slcstmt->bindParam(':ID', $usrid);
			$slcstmt->execute();
			$slcdbresult = $slcstmt->fetchAll();
			$singl_usr_grp_id = NULL;
			if(count($slcdbresult) == 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		//return false;
		header("Location: ../error.php?err=Database error: cannot prepare statement");
		exit();
	}
}

function create_usr_m($mysqlcon, $username, $email, $password, $pw_isnt_hashed = NULL) //I.O.
{
    // Sanitize and validate the data passed in
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $error_msg = NULL;
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    if((isset($pw_isnt_hashed)) AND $pw_isnt_hashed === true)
    {
        $password = $password = hash('sha512', $password);
    }
    if (strlen($password) != 128)
    {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
    if ($stmt = $mysqlcon->prepare(
                              "SELECT id, username, password, salt, deactivatedon 
                               FROM members
                               WHERE email = :EMAIL LIMIT 1"
                               )
        )
    {
        $stmt->bindParam(':EMAIL', $email);
        $stmt->execute();
        $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
        $prep_stmt = "SELECT id FROM members WHERE email = :EMAIL LIMIT 1";
        $stmt = $mysqlcon->prepare($prep_stmt);
        if($stmt)
        {
            $stmt->bindParam(':EMAIL', $email);
            $stmt->execute();
            $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
            if($dbresult !== false)
            {
                // A user with this email address already exists
                $error_msg .= '<p class="error">A user with this email address already exists.</p>';
            }
        }
        else
        {
            $error_msg .= '<p class="error">Database error</p>'; 
        }
        if (empty($error_msg))
        {
            // Create a random salt
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
    
            // Create salted password 
            $password = hash('sha512', $password . $random_salt);
    
            // Insert the new user into the database 
            if ($insert_stmt = $mysqlcon->prepare("INSERT INTO members (username, email, password, salt) VALUES (:USERNAME, :EMAIL, :PASSWORD, :SALT)"))
            {
                $insert_stmt->bindParam(':USERNAME', $username);
                $insert_stmt->bindParam(':EMAIL', $email);
                $insert_stmt->bindParam(':PASSWORD', $password);
                $insert_stmt->bindParam(':SALT', $random_salt);
                // Execute the prepared query.
                if (! $insert_stmt->execute())
                {
                    return false;
                }
				else
				{
					return true;
				}
            }
			else
			{
				return false;
			}
        }
        else
        {
            return $error_msg;
        }
    }
}

function create_usr($mysqlcon, $username, $email, $password, $pw_isnt_hashed = NULL)
{
    // Sanitize and validate the data passed in
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    $error_msg = NULL;
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    if((isset($pw_isnt_hashed)) AND $pw_isnt_hashed === true)
    {
        $password = $password = hash('sha512', $password);
    }
    if (strlen($password) != 128)
    {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $error_msg .= '<p class="error">Invalid password configuration.</p>';
    }
    if ($stmt = $mysqlcon->prepare(
                              "SELECT id, username, password, salt, deactivatedon 
                               FROM members
                               WHERE email = :EMAIL LIMIT 1"
                               )
        )
    {

        $stmt->bindParam(':EMAIL', $email);
        $stmt->execute();
        $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
        $prep_stmt = "SELECT id FROM members WHERE email = :EMAIL LIMIT 1";
        $stmt = $mysqlcon->prepare($prep_stmt);

        if($stmt)
        {
            $stmt->bindParam(':EMAIL', $email);
            $stmt->execute();
            $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
            if($dbresult !== false)
            {
                // A user with this email address already exists
                $error_msg .= '<p class="error">A user with this email address already exists.</p>';
            }
        }
        else
        {
            $error_msg .= '<p class="error">Database error</p>'; 
        }
	

        if (empty($error_msg))
        {
            // Create a random salt
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

            // Create salted password 
            $password = hash('sha512', $password . $random_salt);

            // Insert the new user into the database 
            if ($insert_stmt = $mysqlcon->prepare(
                                             "INSERT INTO members (username, email, password, salt)
                                              VALUES
                                              (:USERNAME, :EMAIL, :PASSWORD, :SALT)"
                                            )
                )
            {
                $insert_stmt->bindParam(':USERNAME', $username);
                $insert_stmt->bindParam(':EMAIL', $email);
                $insert_stmt->bindParam(':PASSWORD', $password);
                $insert_stmt->bindParam(':SALT', $random_salt);
                // Execute the prepared query.
                if (! $insert_stmt->execute())
                {
                    return false;
                }
				//else
				/*{
					if(create_grp($mysqlcon, $username , 4))
					{
						return true;
					}
					else
					{
						return false;
					}
				}*/
            }
			else
			{
				return false;
			}
        //return true;
        }
        else
        {
            return $error_msg;
        }
    }
}

function edit_usr($mysqlcon, $id, $username, $email, $password, $disabled = 0)
{
    // Sanitize and validate the data passed in

    $error_msg = NULL;
	$savepswd = true;
	
	
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    {
        // Not a valid email
        $error_msg .= '<p class="error">The email address you entered is not valid</p>';
    }
	if(!empty($password)) $password = hash('sha512', $password);
	
    if (strlen($password) != 128)
    {
        // The hashed pwd should be 128 characters long.
        // If it's not, something really odd has happened
        $savepswd = false;

    }
  
        if (empty($error_msg))
        {
            // Create a random salt
			echo "Create a random salt";
            $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
   
            // Create salted password 
            $password = hash('sha512', $password . $random_salt);
			$insert_stmt=FALSE;
            // Update user into the database 
			if($savepswd){
				$insert_stmt = $mysqlcon->prepare(
                                             "UPDATE members SET username = :USERNAME, email = :EMAIL, password = :PASSWORD, deactivatedon = :DEACTIVATEDON,   salt = :SALT 
                                              WHERE id=:ID"
                                            );
                
				
			}else{
				$insert_stmt = $mysqlcon->prepare(
                                             "UPDATE members SET username = :USERNAME, email = :EMAIL, deactivatedon = :DEACTIVATEDON  
                                              WHERE id = :ID"
                                            );
										
                
			}
            if ($insert_stmt)
            {
				if($savepswd){
					$insert_stmt->bindParam(':PASSWORD', $password);
					$insert_stmt->bindParam(':SALT', $random_salt);
				}
                $insert_stmt->bindParam(':USERNAME', $username);
                $insert_stmt->bindParam(':EMAIL', $email);
               
               
				$insert_stmt->bindParam(':DEACTIVATEDON', $disabled);
				$insert_stmt->bindParam(':ID', $id);
				
                // Execute the prepared query.
                if (! $insert_stmt->execute())
                {
                    return false;
                }
            }
			else
			{
				return false;
			}
        //return true;
        }
        else
        {
            return $error_msg;
        }
    
}

function edit_usr_m($mysqlcon, $usrid, $usrname, $mail, $password, $pw_isnt_hashed = NULL) //Ist zu testen //Funktion wurde von Boris bereits geschrieben, ist zu ignorieren für Mittwoch
{
	$usrname = filter_var($usrname, FILTER_SANITIZE_STRING);
	$mail = filter_var($mail, FILTER_SANITIZE_EMAIL);
	$mail = filter_var($mail, FILTER_VALIDATE_EMAIL);
	$password = filter_var($password, FILTER_SANITIZE_STRING);
	if((isset($pw_isnt_hashed)) AND $pw_isnt_hashed === true)
	{
		$password = $password = hash('sha512', $password);
	}
	if (strlen($password) != 128)
	{
	   // The hashed pwd should be 128 characters long.
	   // If it's not, something really odd has happened
	   $error_msg .= '<p class="error">Invalid password configuration.</p>';
	}
	if ($stmt = $mysqlcon->prepare("SELECT email FROM members WHERE email = :EMAIL LIMIT 1"))
	{
	   $stmt->bindParam(':EMAIL', $mail);
	   $stmt->execute();
	   $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
	   if($dbresult !== false)
	   {
		   // A user with this email address already exists
		   $error_msg .= '<p class="error">A user with this email address already exists.</p>';
	   }
	}
	else
	{
		$error_msg .= '<p class="error">Database error</p>'; 
	}
	if(empty($error_msg))
	{
	   // Create a random salt
	   $random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));

	   // Create salted password 
	   $password = hash('sha512', $password . $random_salt);
	   if ($stmt = $mysqlcon->prepare("UPDATE members SET username = :USRNAME, email = :EMAIL, password =:PASSWORD, salt = :SALT WHERE location_id =:ID LIMIT 1"))
	   {
		   $stmt->bindParam(':ID', $usrid);
		   $stmt->bindParam(':USRNAME', $usrname);
		   $stmt->bindParam(':EMAIL', $mail);
		   $stmt->bindParam(':PASSWORD', $password);
		   $stmt->bindParam(':SALT', $random_salt);
		   if (! $insert_stmt->execute())
		   {
			   return false;
		   }
		   else
		   {
			  // return true;
			  $chk_arr = array("username" => $usrname,"email" => $mail,"password" => $password,"salt" => $random_salt,);
			  if ($stmt = $mysqlcon->prepare("SELECT username, email, password, salt FROM members WHERE location_id =:ID LIMIT 1"))
			  {
				$stmt->bindParam(':ID', $grpid);
				if (!$insert_stmt->execute())
				{
					return false;
				}
				else
				{
					$dbresult = $stmt->fetch(PDO::FETCH_NUM);
					for ($i = 0; $i <= count($dbresult); $i++)
					{
						if ($dbresult[$i] == $chk_arr[$i])
						{
							$chk_arr[$i] = true;
						}
						else
						{
							$chk_arr[$i] = false;
						}
						//Als Erweiterung: Ausgabe, welcher Wert nicht korrekt aktualisiert wurde--> Logging
					}
					if(in_array(false,$chk_arr))
					{
						return false;
					}
					else
					{
						return true;
					}
				}
			  }
		   }
	   }
	   else
	   {
		   return false;
	   }
	}
   else
   {
	   return false;
   }
}

function deactivate_usr($mysqlcon, $usrid, $datetimetodeactivate = NULL, $activate_usr = NULL) //Unixtimestamp wird als text in DB geschrieben //Wurde von Boris mit der User editieren Routine vereint, ist zu ignorieren für Mittwoch
{
    if(!isset($datetimetodeactivate))
    {
        $datetimetodeactivate = time(); 
    }
    if ($stmt = $mysqlcon->prepare("UPDATE members SET deactivatedon=:DEACTIVATEWHEN WHERE id =:ID LIMIT 1"))
    {
        if(isset($activate_usr) AND ($activate_usr === true))
        {
            $datetimetodeactivate = NULL; //Muss als String "NULL" gesetzt werden? Ist zu testen!
        }
        $stmt->bindParam(':DEACTIVATEWHEN', $datetimetodeactivate);
        $stmt->bindParam(':ID', $usrid);
        $stmt->execute();
        if ($slcstmt = $mysqlcon->prepare("SELECT deactivatedon FROM members WHERE id = :ID LIMIT 1"))
        {
            $slcstmt->bindParam(':ID', $usrid);
            $slcstmt->execute();
            $slcdbresult = $slcstmt->fetch(PDO::FETCH_ASSOC);
            
            if($slcdbresult['deactivatedon'] === $datetimetodeactivate)
            {
				if($activate_usr)
				{
					return true; 
				}
				else
				{
					return $datetimetodeactivate; 
				}
            }
            else
            {
				if($activate_usr)
				{
					return true;
				}
				else
				{
					 return $datetimetodeactivate;
				}
            }
        }
    }
    else
    {
        return $datetimetodeactivate;
    }
}
function delete_grp($mysqlcon, $grpid) // i.o.
{
    if ($stmt = $mysqlcon->prepare("DELETE FROM location WHERE location_id = :ID LIMIT 1"))
    {
        $stmt->bindParam(':ID', $grpid);
        $stmt->execute();
        if ($slcstmt = $mysqlcon->prepare("Select location_id FROM location WHERE location_id = :ID LIMIT 1"))
        {
            $slcstmt->bindParam(':ID', $grpid);
            $slcstmt->execute();
            $slcdbresult = $slcstmt->fetchAll();
            if(count($slcdbresult) == 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    else
    {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}
function create_grp($mysqlcon, $grpname) //I.O. //Gruppentypunterscheidung ist für Mittwoch zu ignorieren
{
    $error_msg="";
    $grpname = filter_var($grpname, FILTER_SANITIZE_STRING);
	$chk_grpname_stmt = "SELECT location_id FROM location WHERE name = :GRPNAME AND type = :TYPE LIMIT 1";
	$stmt = $mysqlcon->prepare($chk_grpname_stmt);
	if($stmt)
	{
		$stmt->bindParam(':GRPNAME', $grpname);
		$stmt->bindParam(':TYPE', $grp_typ);
		$stmt->execute();
		$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
		if($dbresult !== false)
		{
			// A user with this email address already exists
			$error_msg .= '<p class="error">A (User)Group with this Name already exists.</p>';
		}
	}
	else
	{
		$error_msg .= '<p class="error">Database error</p>'; 
	}
	if (empty($error_msg))
	{    
		// Insert the new group into the database 
		if ($insert_stmt = $mysqlcon->prepare("INSERT INTO location (name) VALUES (:GRPNAME)")) //if ($insert_stmt = $mysqlcon->prepare("INSERT INTO location (name", type") VALUES (:GRPNAME", :TYPE")"))
		{
			$insert_stmt->bindParam(':GRPNAME', $grpname);
			if ($insert_stmt->execute())  //(! $insert_stmt->execute())
			{
				return true;
			}
			else
			{
				 return false;
			}
		}
	}
	else
	{
		return $error_msg;
	}
}

function edit_grp($mysqlcon,$grpid, $grpname)//I.O.
{
	if ($stmt = $mysqlcon->prepare("UPDATE location SET name = :GRPNAME WHERE location_id =:ID LIMIT 1"))
	{
		 $grpname = filter_var($grpname, FILTER_SANITIZE_STRING);
		 $stmt->bindParam(':GRPNAME', $grpname);
		 $stmt->bindParam(':ID', $grpid);
		 if ($stmt->execute())
		 {
			if ($stmt = $mysqlcon->prepare("SELECT name FROM location WHERE location_id =:ID LIMIT 1"))
			{
				$stmt->bindParam(':ID', $grpid);
				if ($stmt->execute())
				{
					$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
					if($dbresult !== false)
					{
						if($dbresult['name'] == $grpname)
						{
							return true;
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		 }
		 else
		 {
			return false;
		 }
		 
	}
}

function deactivate_grp($mysqlcon, $grpid, $datetimetodeactivate = NULL, $activate_grp = NULL) //I.O. //Unixtimestamp wird als text in DB geschrieben
{
    if(!isset($datetimetodeactivate))
    {
        $datetimetodeactivate = time(); 
    }
    if ($stmt = $mysqlcon->prepare("UPDATE location SET deactivatedon=:DEACTIVATEWHEN WHERE location_id =:ID LIMIT 1"))
    {
        if(isset($activate_grp) AND ($activate_grp === true))
        {
            $datetimetodeactivate = NULL; //Muss als String "NULL" gesetzt werden? Ist zu testen!
        }
        $stmt->bindParam(':DEACTIVATEWHEN', $datetimetodeactivate);
        $stmt->bindParam(':ID', $grpid);
        $stmt->execute();
        if ($slcstmt = $mysqlcon->prepare("SELECT deactivatedon FROM location WHERE location_id = :ID LIMIT 1"))
        {
            $slcstmt->bindParam(':ID', $grpid);
            $slcstmt->execute();
            $slcdbresult = $slcstmt->fetch(PDO::FETCH_ASSOC);
            
            if($slcdbresult['deactivatedon'] === $datetimetodeactivate)
            {
				if($activate_grp)
				{
					return true;
				}
				else
				{
					return $datetimetodeactivate; 	
				}
            }
            else
            {
				if($activate_grp)
				{
					return true;
				}
				else
				{
					return $datetimetodeactivate;
				}
            }
        }
    }
    else
    {
        return $datetimetodeactivate;
    }
}

function add_usr_grp($mysqlcon,$grpid, $usrid)//i.O.
{
	if(isset($grpid,$usrid))
	{
		$slcstmt = $mysqlcon->prepare("SELECT group_id FROM member_location WHERE group_id =:GRP_ID_P AND user_id =:USR_ID_C");
		$slcstmt->bindParam(':GRP_ID_P', $grpid);
		$slcstmt->bindParam(':USR_ID_C', $usrid);
		$slcstmt->execute();   // Execute the prepared query.
		$slcdbresult = $slcstmt->fetchAll();
		if(count($slcdbresult) == 0)
		{
			$stmt = $mysqlcon->prepare("INSERT INTO member_location(group_id, user_id) VALUES (:GRP_ID_P, :GRP_ID_C)");
			$stmt->bindParam(':GRP_ID_P', $grpid);
			$stmt->bindParam(':GRP_ID_C', $usrid);
			if($stmt->execute())
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;//Mapping existiert bereits
			//return "Mapping allready exists";
		}
	}
	else
	{
		return false;
	}
}
function del_usr_grp($mysqlcon,$grpid, $usrid) // i.o.
{
    if ($stmt = $mysqlcon->prepare("DELETE FROM member_location WHERE group_id = :GRP_ID_P AND user_id =:USR_ID_C LIMIT 1"))
    {
        $stmt->bindParam(':GRP_ID_P', $grpid);
		$stmt->bindParam(':USR_ID_C', $usrid);
        $stmt->execute();
        if ($slcstmt = $mysqlcon->prepare("Select group_id FROM member_location WHERE group_id = :GRP_ID_P AND user_id =:USR_ID_C LIMIT 1"))
        {
			$slcstmt->bindParam(':GRP_ID_P', $grpid);
			$slcstmt->bindParam(':USR_ID_C', $usrid);
            $slcstmt->execute();
            $slcdbresult = $slcstmt->fetchAll();
            if(count($slcdbresult) == 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    else
    {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function add_grp_grp($mysqlcon,$grpid_p, $grpid_c) //Ist für Mittwoch zu ignorieren
{//Prüft den Gruppentypen nicht //Funktion wird für Webseite nicht freigegeben
	return false; //Restliche Funktion wird nicht abgehandelt
	if((isset($grpid_p,$grpid_c)) && ($grpid_c != $grpid_p))
	{
		if((grp_is_usrgrp($mysqlcon,$grpid_p)) AND (grp_is_usrgrp($mysqlcon,$grpid_c)))
		{
			$slcstmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_c_id =:GRP_ID_C"); //$slcstmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id =:GRP_ID_P AND group_c_id =:GRP_ID_C");
			//$slcstmt->bindParam(':GRP_ID_P', $grpid_p);
			$slcstmt->bindParam(':GRP_ID_C', $grpid_c);
			$slcstmt->execute();   // Execute the prepared query.
			$slcdbresult = $slcstmt->fetchAll();
			if(count($slcdbresult) == 0)
			{
				$slcstmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id =:GRP_ID_P AND group_c_id =:GRP_ID_C"); //$slcstmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id =:GRP_ID_P AND group_c_id =:GRP_ID_C");
				$slcstmt->bindParam(':GRP_ID_P', $grpid_p);
				$slcstmt->bindParam(':GRP_ID_C', $grpid_c);
				$slcstmt->execute();   // Execute the prepared query.
				$slcdbresult = $slcstmt->fetchAll();
				if(count($slcdbresult) == 0)
				{
					$stmt = $mysqlcon->prepare("INSERT INTO mapping_location(group_p_id, group_c_id) VALUES (:GRP_ID_P, :GRP_ID_C)");
					$stmt->bindParam(':GRP_ID_P', $grpid_p);
					$stmt->bindParam(':GRP_ID_C', $grpid_c);
					if($stmt->execute())
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					//return false;//Mapping existiert bereits
					return "Mapping allready exists";
				}
			}
			else
			{
				return 'Parent/Child-Relation allready exists';
			}
		}
		else
		{
			$slcstmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id =:GRP_ID_P AND group_c_id =:GRP_ID_C"); //$slcstmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id =:GRP_ID_P AND group_c_id =:GRP_ID_C");
			$slcstmt->bindParam(':GRP_ID_P', $grpid_p);
			$slcstmt->bindParam(':GRP_ID_C', $grpid_c);
			$slcstmt->execute();   // Execute the prepared query.
			$slcdbresult = $slcstmt->fetchAll();
			if(count($slcdbresult) == 0)
			{
				$stmt = $mysqlcon->prepare("INSERT INTO mapping_location(group_p_id, group_c_id) VALUES (:GRP_ID_P, :GRP_ID_C)");
				$stmt->bindParam(':GRP_ID_P', $grpid_p);
				$stmt->bindParam(':GRP_ID_C', $grpid_c);
				if($stmt->execute())
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				//return false;//Mapping existiert bereits
				return "Mapping allready exists";
			}
		}
	}
	else
	{
		return false;
	}
}

function del_grp_grp($mysqlcon, $grpid_p, $grpid_c) //Ist für Mittwoch zu ignorieren
{//Prüft den Gruppentypen nicht  //Funktion wird für Webseite nicht freigegeben
	return false; //Restliche Funktion wird nicht abgehandelt
    if ($stmt = $mysqlcon->prepare("DELETE FROM mapping_location WHERE group_p_id = :GRP_ID_P AND group_c_id =:GRP_ID_C LIMIT 1"))
    {
        $stmt->bindParam(':GRP_ID_P', $grpid_p);
		$stmt->bindParam(':GRP_ID_C', $grpid_c);
        $stmt->execute();
        if ($slcstmt = $mysqlcon->prepare("Select group_p_id FROM mapping_location WHERE group_p_id = :GRP_ID_P AND group_c_id =:GRP_ID_C LIMIT 1"))
        {
			$slcstmt->bindParam(':GRP_ID_P', $grpid_p);
			$slcstmt->bindParam(':GRP_ID_C', $grpid_c);
            $slcstmt->execute();
            $slcdbresult = $slcstmt->fetchAll();
            if(count($slcdbresult) == 0)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }
    else
    {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}

function check_type_location($mysqlcon, $location_id, $type_tocheck) //Ungestestet // Ist für Mittwoch zu ignorieren, da Typen bis dahin noch nicht implementiert werden.
{
	if((filter_var($location_id, FILTER_SANITIZE_NUMBER_INT) && filter_var($location_id, FILTER_VALIDATE_INT)) && (filter_var($type_tocheck, FILTER_SANITIZE_NUMBER_INT) && filter_var($type_tocheck, FILTER_VALIDATE_INT)))
	{
		if($stmt = $mysqlcon->prepare("SELECT type FROM location WHERE location_id = :LOCATIONID LIMIT 1"))
		{
			$stmt->bindParam(':LOCATIONID', $location_id);
			if($stmt->execute())
			{
				$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
				if($dbresult['type'] == $type_tocheck)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function add_grp_room($mysqlcon, $roomid, $grpid)//I.O. //Typen müssen geprüft werden //Typen werden für Mittwoch ignoriert
{
	//Sanatize and Validate
	if ($slc_stmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id =:GRP_ID LIMIT 1")) //	if ($slc_stmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id =:GRP_ID AND perm_type =:PERMISIONTYPE LIMIT 1"))
	{ 								  //SELECT group_p_id FROM mapping_location WHERE group_p_id = 17 AND group_c_id =0 LIMIT 1 
		$slc_stmt->bindParam(':ROOM_ID', $roomid);
		$slc_stmt->bindParam(':GRP_ID', $grpid);
		if($slc_stmt->execute())
		{
			$slc_dbresult = $slc_stmt->fetchAll();
			if(count($slc_dbresult) == 0)
			{
				if($stmt = $mysqlcon->prepare("INSERT INTO mapping_location (group_p_id, group_c_id) VALUES (:ROOM_ID, :GRP_ID)")) //if($stmt = $mysqlcon->prepare("INSERT INTO mapping_location (group_p_id, group_c_id, perm_type) VALUES (:ROOM_ID, GRP_ID, :PERMISIONTYPE)"))
				{
					$stmt->bindParam(':ROOM_ID', $roomid);
					$stmt->bindParam(':GRP_ID', $grpid);
					if($stmt->execute())
					{
						if($stmt = $mysqlcon->prepare("SELECT group_p_id, group_c_id FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id = :GRP_ID LIMIT 1")) //if($stmt = $mysqlcon->prepare("SELECT group_p_id, group_c_id, perm_type FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id = :GRP_ID AND perm_type = :PERMISIONTYPE LIMIT 1"))
						{
							$stmt->bindParam(':ROOM_ID', $roomid);
							$stmt->bindParam(':GRP_ID', $grpid);
							if($stmt->execute())
							{
								$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
								if(count($dbresult) == 1)
								{
									return true;
								}
								else
								{
									return false;
								}
							}
							else
							{
								return false;
							}
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				//Mapping allready exists
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function del_grp_room($mysqlcon, $roomid, $grpid/*, $perm_type*/) //I.O. //Typen müssen geprüft werden //Ist zu testen //Typen werden für Mittwoch ignoriert
{
	//Sanatize and Validate
	if($stmt = $mysqlcon->prepare("DELETE FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id =:GRP_ID LIMIT 1")) //if($stmt = $mysqlcon->prepare("DELETE FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id =:GRP_ID AND perm_type =:PERMISIONTYPE LIMIT 1"))
	{
		$stmt->bindParam(':ROOM_ID', $roomid);
		$stmt->bindParam(':GRP_ID', $grpid);
		if($stmt->execute())
		{
			if($stmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id =:GRP_ID LIMIT 1")) //if($stmt = $mysqlcon->prepare("SELECT group_p_id FROM mapping_location WHERE group_p_id = :ROOM_ID AND group_c_id =:GRP_ID AND perm_type =:PERMISIONTYPE LIMIT 1"))
			{
				$stmt->bindParam(':ROOM_ID', $roomid);
				$stmt->bindParam(':GRP_ID', $grpid);
				if($stmt->execute())
				{
					$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
					if(count($dbresult) == 0)
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
	return false;
	}
}
function get_singl_usrgrp($mysqlcon, $usrid) //Wird für Mittwoch ignoriert //Ungetestet
{
	if(filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT))
	{
		if($stmt = $mysqlcon->prepare("SELECT group_id, name FROM location WHERE name = :USRID AND type = 4"))
		{
			$stmt->bindParam(':USRID', $usrid);
			if($stmt->execute())
			{
				$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $dbresult;
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		return false;	
	}
}

function get_singl_usrroom($mysqlcon, $usrid) //Wird für Mittwoch ignoriert //Ungetestet
{
	if(filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT))
	{
		if($stmt = $mysqlcon->prepare("SELECT group_id, name FROM location WHERE name = :USRID AND type = 5"))
		{
			$stmt->bindParam(':USRID', $usrid);
			if($stmt->execute())
			{
				$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $dbresult;
			}
			else
			{
				return false;
			}
		}
	}
	else
	{
		return false;	
	}
}

function get_usrgrps($mysqlcon, $usrid, $get_deactivated_grp = NULL) //I.O.
{
	if(!isset($get_deactivated_grp))
	{
		$get_deactivated_grp = false;
	}
		$usr_locs = get_usrlocs($mysqlcon, $usrid);//Beinhaltet alle Gruppen-IDs aus member_location(Mappingtable)
		if($usr_locs == false)
		{
			return false;
		}
		$usr_grps = NULL;
		$prepstmt = NULL;
		$endresult = NULL;
		if($get_deactivated_grp) //$prepstmt = "SELECT location_id, name, type, parent_id, deactivatedon FROM location WHERE (deactivatedon = '') AND (type = 2 OR type = 4)";
		{
			//$prepstmt = "SELECT location_id, name, type, parent_id, deactivatedon FROM location WHERE type = 2";
			$prepstmt = "SELECT location_id, name, parent_id, deactivatedon FROM location"; //Query ist auf eigentständige Tabelle anzupassen
		}
		else
		{
			//$prepstmt = "SELECT location_id, name, type, parent_id, deactivatedon FROM location WHERE deactivatedon = '' AND type = 2";
			$prepstmt = "SELECT location_id, name, parent_id, deactivatedon FROM location WHERE deactivatedon = ''"; //Query ist auf eigentständige Tabelle anzupassen
		}
		if($stmt = $mysqlcon->prepare($prepstmt))
		{
			if($stmt->execute())
			{
				$temp_usr_locs = array();
				for ($i = 0; $i < count($usr_locs); $i++) //for ($i = 0; $i <= count($usr_locs); $i++)
				{
						$temp_usr_locs[] = $usr_locs[$i]['group_id'];
				}
				$temp_usr_locs = array_unique($temp_usr_locs); //Duplikate entfernen //Ist dies nötig? Wenn nicht, korrigieren
				$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
				for ($i = 0; $i < count($dbresult); $i++)
				{
					if(in_array($dbresult[$i]['location_id'],$temp_usr_locs))
					{
						$endresult[] =$dbresult [$i];
					}
					else
					{
						continue;
					}
				}
				return $endresult;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
}


function get_usrlocs($mysqlcon, $usrid) //I.O.
{
	//Sanatize and Validate
	if((filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT)))
	{
		if($stmt = $mysqlcon->prepare("SELECT group_id FROM member_location WHERE user_id = :USERID"))
		{
			$stmt->bindParam(':USERID', $usrid);
			if($stmt->execute())
			{
				$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
				return $dbresult;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function get_usrs_of_grp($mysqlcon, $grpid, $get_deactivated_usr = NULL)  //I.O. //return Array[0]->Array[0]{[id][username][email][deactivatedon]}<--Selbe Rückgabe bei Gruppen für User?
{
	if(!isset($get_deactivated_usr))
	{
		$get_deactivated_usr = false;
	}
	if($stmt = $mysqlcon->prepare("SELECT user_id FROM member_location WHERE group_id = :GROUPID"))
	{
		$stmt->bindParam(':GROUPID', $grpid);
		if($stmt->execute())
		{
			$mapdbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$temp_mapdbresult = array();
			for ($i = 0; $i < count($mapdbresult); $i++)
			{
				for ($j = 0; $j < count($mapdbresult[$i]); $j++)
				{
					$temp_mapdbresult[] = $mapdbresult[$i]['user_id'];
				}
			}
			$temp_mapdbresult = array_unique($temp_mapdbresult); //Doppelte IDs entfernen
			
			$usrstmt = NULL;
			if($get_deactivated_usr)
			{
				$usrstmt = "SELECT id, username, email, deactivatedon FROM members";
			}
			else
			{
				$usrstmt = "SELECT id, username, email, deactivatedon FROM members WHERE deactivatedon = ''";
			}
			if($stmt = $mysqlcon->prepare($usrstmt))
			{
				if($stmt->execute())
				{
					$usrdbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$endresult = array();
					for ($i = 0; $i < count($usrdbresult); $i++)
					{
						if(in_array($usrdbresult[$i]['id'],$temp_mapdbresult))
						{
							$endresult[] =$usrdbresult[$i];
						}
						else
						{
							continue;
						}
					}
					return $endresult;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function get_perm_from_usrid_nocontext($mysqlcon, $usrid)
{
	if($stmt = $mysqlcon->prepare("SELECT group_id FROM member_location WHERE user_id = :USRID"))
	{
		$stmt->bindParam(':USRID', $usrid);
		if($stmt->execute())
		{
			$dbresult_mem_loc = $stmt->fetchAll(PDO::FETCH_ASSOC);
			
						//$usergrps = get_usrgrps($mysqlcon,$usrid, $get_deactivated_grp);
			if($perms_wo_cntx = $mysqlcon->prepare("SELECT group_id, site_id FROM group_permission_without_room"))
			{
				if($perms_wo_cntx->execute())
				{
					$perms_wo_cntx_dbresult = $perms_wo_cntx->fetchAll(PDO::FETCH_ASSOC);
					$endresult = array();
					for ($i = 0; $i < count($perms_wo_cntx_dbresult); $i++)
					{
						for ($j = 0; $j < count($dbresult_mem_loc); $j++)
						{
							if($perms_wo_cntx_dbresult[$i]['group_id'] == $dbresult_mem_loc[$j]['group_id'])
							{
								$endresult[] = $perms_wo_cntx_dbresult[$i];
							}
							else
							{
								continue;
							}
						}
					}
					return $endresult;
				}
				else
				{
					return false;
				}
				
			}
			else
			{
				return false;
			}
			//return $usergrps;
	
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function get_perm_from_usrid($mysqlcon, $usrid)
{
	if($get_grpid_stmt = $mysqlcon->prepare("SELECT group_id FROM member_location WHERE user_id = :USERID"))
	{
		$get_grpid_stmt->bindParam(':USERID', $usrid);
		if($get_grpid_stmt->execute())
		{
			$get_grpid_dbresult = $get_grpid_stmt->fetchAll(PDO::FETCH_ASSOC);
			
			if($stmt = $mysqlcon->prepare("SELECT group_id, location_id, permission_level FROM rights_level_permission"))
			{
				if($stmt->execute())
				{
					$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);
					$temp_mapdbresult = array();
					$filtered_dbresult = array();
					for ($i = 0; $i < count($dbresult); $i++)
					{
						for ($j = 0; $j < count($get_grpid_dbresult); $j++)
						{
							if($dbresult[$i]['group_id'] == $get_grpid_dbresult[$j]['group_id'])
							{
									$temp_mapdbresult[] = $dbresult[$i];
							}
							else
							{
								continue;
							}
						}
					}
					return $temp_mapdbresult;
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function get_perm_from_usrmail($mysqlcon, $usrmail)
{
	if($get_usrid_stmt = $mysqlcon->prepare("SELECT id FROM members WHERE email = :EMAIL LIMIT 1"))
	{
		$get_usrid_stmt->bindParam(':EMAIL', $usrmail);
		if($get_usrid_stmt->execute())
		{
			$get_usrid_dbresult = $get_usrid_stmt->fetch(PDO::FETCH_ASSOC);
			return get_perm_from_usrid($mysqlcon, $get_usrid_dbresult['id']); //Je nach obiger Entwicklung richtige Werte zurückgeben.
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function add_perm_room_usr($mysqlcon, $usrid, $roomid, $perm_type)//Ungetestet, ist für Mittwoch zu ignorieren
{
	if
	(
	   (filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($roomid, FILTER_SANITIZE_NUMBER_INT) && filter_var($roomid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($perm_type, FILTER_SANITIZE_NUMBER_INT) && filter_var($perm_type, FILTER_VALIDATE_INT))
	)
	{
		$singl_usr_grp = get_singl_usrgrp($mysqlcon,$usrid);
		if(add_grp_room($mysqlcon, $roomid, $singl_usr_grp['location_id'], $perm_type))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function del_perm_room_usr($mysqlcon, $usrid, $roomid, $perm_type)//Ungetestet, ist für Mittwoch zu ignorieren
{
	if
	(
	   (filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($roomid, FILTER_SANITIZE_NUMBER_INT) && filter_var($roomid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($perm_type, FILTER_SANITIZE_NUMBER_INT) && filter_var($perm_type, FILTER_VALIDATE_INT))
	)
	{
		$singl_usr_grp = get_singl_usrgrp($mysqlcon,$usrid);
		if(del_grp_room($mysqlcon, $roomid, $singl_usr_grp['location_id'],$perm_type))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function add_vswitch_usr($mysqlcon, $usrid, $roomid, $tasterid, $perm_type)//Ungetestet, ist für Mittwoch zu ignorieren
{
	if
	(
	   (filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($roomid, FILTER_SANITIZE_NUMBER_INT) && filter_var($roomid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($perm_type, FILTER_SANITIZE_NUMBER_INT) && filter_var($perm_type, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($tasterid, FILTER_SANITIZE_NUMBER_INT) && filter_var($tasterid, FILTER_VALIDATE_INT))
	)
	{
		if(add_perm_room_usr($mysqlcon, $usrid, $roomid, $perm_type))
		{
			if($slcstmt = $mysqlcon->prepare("Select taster_id FROM vswitch_location WHERE taster_id = :TASTERID AND location_id = :LOCATIONID LIMIT 1"))
			{
				$slcstmt->bindParam(':TASTERID', $tasterid);
				$slcstmt->bindParam(':LOCATIONID', $roomid);
				if($slcstmt->execute())
				{
					$slcdbresult = $slcstmt->fetchAll();
					if(count($slcdbresult) == 0)
					{
						if($stmt = $mysqlcon->prepare("INSERT INTO vswitch_location (taster_id, location_id ) VALUES(:TASTERID, LOCATIONID)"))
						{
							$stmt->bindParam(':TASTERID', $tasterid);
							$stmt->bindParam(':LOCATIONID', $roomid);
							if($stmt->execute())
							{
								if($slcstmt = $mysqlcon->prepare("Select taster_id, location_id FROM vswitch_location WHERE taster_id = :TASTERID AND location_id = :LOCATIONID LIMIT 1"))
								{
									$slcstmt->bindParam(':TASTERID', $tasterid);
									$slcstmt->bindParam(':LOCATIONID', $roomid);
									if($slcstmt->execute())
									{
										$slcdbresult = $slcstmt->fetch(PDO::FETCH_ASSOC);
										if(count($slcdbresult) < 0)
										{
											return true;
										}
										else
										{
											return false;
										}
									}
									else
									{
										return false;
									}
								}
								else
								{
									return false;
								}
							}
							else
							{
								return false;
							}
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function del_vswitch_usr($mysqlcon, $usrid, $roomid, $taster_id, $perm_type)//Ungetestet, ist für Mittwoch zu ignorieren
{
	if
	(
	   (filter_var($usrid, FILTER_SANITIZE_NUMBER_INT) && filter_var($usrid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($roomid, FILTER_SANITIZE_NUMBER_INT) && filter_var($roomid, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($perm_type, FILTER_SANITIZE_NUMBER_INT) && filter_var($perm_type, FILTER_VALIDATE_INT))
	   &&
	   (filter_var($tasterid, FILTER_SANITIZE_NUMBER_INT) && filter_var($tasterid, FILTER_VALIDATE_INT))
	)
	{
		if($slcstmt = $mysqlcon->prepare("DELETE FROM vswitch_location WHERE taster_id = :TASTERID AND location_id = :LOCATIONID LIMIT 1"))
		{
			$slcstmt->bindParam(':TASTERID', $tasterid);
			$slcstmt->bindParam(':LOCATIONID', $roomid);
			if($slcstmt->execute())
			{		
				if($slcstmt = $mysqlcon->prepare("SELECT taster_id FROM vswitch_location WHERE taster_id = :TASTERID AND location_id = :LOCATIONID LIMIT 1"))
				{
					$slcstmt->bindParam(':TASTERID', $tasterid);
					$slcstmt->bindParam(':LOCATIONID', $roomid);
					if($slcstmt->execute())
					{
						$slcdbresult = $slcstmt->fetchAll();
						if(count($slcdbresult) == 0)
						{
							return true;
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function create_usergroup($mysqlcon, $grpname, $grpdescription)  //ungetestet
{
    $error_msg="";
    $grpname = filter_var($grpname, FILTER_SANITIZE_STRING);
	$grpdescription = filter_var($grpdescription, FILTER_SANITIZE_STRING);
	$chk_grpname_stmt = "SELECT group_id FROM user_group WHERE name = :GRPNAME";
	$stmt = $mysqlcon->prepare($chk_grpname_stmt);
	if($stmt)
	{
		$stmt->bindParam(':GRPNAME', $grpname);
		$stmt->execute();
		$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
		if($dbresult !== false)
		{
			$error_msg .= '<p class="error">A Usergroup with this Name already exists.</p>';
		}
	}
	else
	{
		$error_msg .= '<p class="error">Database error</p>'; 
	}
	if (empty($error_msg))
	{    
		// Insert the new group into the database 
		if ($insert_stmt = $mysqlcon->prepare("INSERT INTO user_group (name, description) VALUES (:GRPNAME, :DESCRIPTION)")) //if ($insert_stmt = $mysqlcon->prepare("INSERT INTO location (name", type") VALUES (:GRPNAME", :TYPE")"))
		{
			$insert_stmt->bindParam(':GRPNAME', $grpname);
			$insert_stmt->bindParam(':DESCRIPTION', $grpdescription);
			if ($insert_stmt->execute())  //(! $insert_stmt->execute())
			{
				if($slcstmt = $mysqlcon->prepare ("SELECT group_id FROM user_group WHERE name =:GRPNAME AND description = :DESCRIPTION LIMIT 1"))
				{
					$slcstmt->bindParam(':GRPNAME', $grpname);
					$slcstmt->bindParam(':DESCRIPTION', $grpdescription);
					if ($slcstmt->execute())  //(! $insert_stmt->execute())
					{
						$dbresult = $slcstmt->fetch(PDO::FETCH_ASSOC);
						if(count($dbresult) > 0)
						{
							return $dbresult['group_id'];
						}
						else
						{
							return false;
						}
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
			else
			{
				 return false;
			}
		}
	}
	else
	{
		return $error_msg;
	}
}

function search_grpid_by_name($mysqlcon, $grpname) //ungetestet
{
	if ($stmt = $mysqlcon->prepare("SELECT group_id FROM user_group WHERE name = :GRPNAME LIMIT 1")) //if ($insert_stmt = $mysqlcon->prepare("INSERT INTO location (name", type") VALUES (:GRPNAME", :TYPE")"))
	{
		$stmt->bindParam(':GRPNAME', $grpname);
		if ($stmt->execute())  //(! $insert_stmt->execute())
		{
			$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
			return $dbresult['group_id'];
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}

function get_all_by_admin($mysqlcon, $isBatman)
{
	$stmt = $mysqlcon->prepare("SELECT COUNT(*) as user_count FROM members WHERE isBatman = :ISBATMAN LIMIT 1 ");
  $stmt->bindParam(':ISBATMAN', $isBatman);
   // Execute the prepared query.
  $stmt->execute();

    $adminCount = Defaultvalues::DEFAULT_ADMIN;

  $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);

    if($dbresult["user_count"] == $adminCount){
      return true;
    }
    else {
      return false;
    }
}


?>
