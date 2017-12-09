<?php
define("CAN_REGISTER", "any");
define("DEFAULT_ROLE", "member");

define("SECURE", FALSE);    // For development purposes only!!!!

function sec_session_start()
{
    $session_name = 'sec_session_id';   // Set a custom session name 
    $secure = SECURE;

    // This stops JavaScript being able to access the session id.
    $httponly = true;
    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }
    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

    // Sets the session name to the one set above.
    session_name($session_name);

    session_start();            // Start the PHP session 
    session_regenerate_id();    // regenerated the session, delete the old one. 
}

function esc_url($url)
{
    if ('' == $url) {
        return $url;
    }
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string)$url;

    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
    $url = str_replace(';//', '://', $url);
    $url = htmlentities($url);
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);

    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}

function set_default_room_usr() //Ruft den Raum mit der kleinsten Id auf, auf die der User berechtigt ist. Danach muss dieser in die Session geschrieben werden, um bei Initialload eines Filters als Defaultroom benutzt werden zu können
{

}


function login_check($mysqlcon) //Ist sauber zu überprüfen /implementieren
{
    // Check if all session variables are set
    if (isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
        $user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];

        if ($stmt = $mysqlcon->prepare("SELECT password, deactivatedon FROM members WHERE id = :ID LIMIT 1")) {
            $stmt->bindParam(':ID', $user_id);
            $stmt->execute();
            $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($dbresult['password'] !== false AND ($dbresult['deactivatedon'] == 0)) // if($dbresult['password'] !== false AND ((is_null($dbresult['deactivatedon'])) OR (empty($dbresult['deactivatedon']))))
            {
                $login_check = hash('sha512', $dbresult['password'] . $user_browser);
                if ($login_check == $login_string) {
                    // Logged In!!!! 
                    return true;
                } else {
                    // Not logged in 
                    return false;
                }
            } else {
                if ($dbresult['deactivatedon'] == 1) {
                    //$human_time = date("d.m.Y H:i:s", $dbresult['deactivatedon']); //Gibt aktuelle Zeit zurück, nicht zeit aus Tiemstamp aus der DB
                    //header('Location: index.php?err=Deactivated User since ' . $human_time);//Ist für die Verlinkung aufs Dashboard verantwortlich?!
                    header('Location: index.php?error=Deactivated User');
                    exit();
                }
                //else
                //{
                //    
                //}
                // Not logged in 
                return false;
            }
        } else {
            // Could not prepare statement
            header("Location: ../error.php?err=Database error: cannot prepare statement");
            exit();
        }
    } else {
        // Not logged in 
        return false;
    }
}

function login($username, $input_password, $mysqlcon)
{
    /* if ($username == 'Admin') {*/

    if ($stmt = $mysqlcon->prepare("SELECT usr_id, usr_name, usr_password, usr_salt FROM User WHERE  usr_name = :USER LIMIT 1")) {
        $stmt->bindParam(':USER', $username);
        $stmt->execute();
        $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dbresult !== false) {
            $password = hash('sha512', $input_password . $dbresult['usr_salt']);


            if (($dbresult['usr_password'] == $password)) {
                $user_browser = $_SERVER['HTTP_USER_AGENT'];

                // XSS protection as we might print this value
                $user_id = preg_replace("/[^0-9]+/", "", $dbresult['usr_id']);
                $_SESSION['user_id'] = $user_id;

                // XSS protection as we might print this value
                $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $dbresult['usr_name']);

                $_SESSION['username'] = $username;
                $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                // Login successful.
                return true;

            }

        }
    }


}/*else{
		
		if ($stmt = $mysqlcon->prepare("SELECT id, username, password, salt, deactivatedon FROM members WHERE email = :EMAIL") )
		{
			$stmt->bindParam(':EMAIL', $email);
			$stmt->execute();
			$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
			if($dbresult !== false)
			{
				$password = hash('sha512', $input_password. $dbresult['salt']);
				//Checkbrute
				if(checkbrute($dbresult['id'], $mysqlcon) == true)
				{
						
					return false;
				}
				else
				{
					if (($dbresult['password'] == $password) AND ($dbresult['deactivatedon'] == 0))//if (($dbresult['password'] == $password) AND ((is_null($dbresult['deactivatedon'])) OR (empty($dbresult['deactivatedon']))))
					{
						$user_browser = $_SERVER['HTTP_USER_AGENT'];
		
						// XSS protection as we might print this value
						$user_id = preg_replace("/[^0-9]+/", "", $dbresult['id']);
						$_SESSION['user_id'] = $user_id;
		
						// XSS protection as we might print this value
						$username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $dbresult['username']);
		
						$_SESSION['username'] = $username;
						$_SESSION['login_string'] = hash('sha512', $password . $user_browser);
						// Login successful.
						return true;
					}
					else
					{
						if($dbresult['deactivatedon'] == 1) //if(!is_null($dbresult['deactivatedon']))
						{
							header('Location: ../index.php?error=Deactivated User');
							exit();
						}
						else
						{
							// Password is not correct
							// We record this attempt in the database
							$now = time();
							$idofuser= $dbresult['id'];
							$mysqlquery= "INSERT INTO login_attempts(user_id, time) VALUES ('.$idofuser.', '$now')";
							if (!$mysqlcon->query($mysqlquery))
							{
								header("Location: ../error.php?err=Database error: login_attempts");
								exit();
							}
							return false;
						}
					}
				}
			}
		}
		
	}
	
  return false;
}*/


function checkbrute($user_id, $mysqlcon, int $timeinterval = NULL, int $loginatempts = NULL) //$timeinterval in Seconds, if NULL, defaultvalue 2h, $loginatempts in timespan (in Seconds), if NULL, default 5
{
    // Get timestamp of current time 
    $now = time();
    $valid_attempts = NULL;
    $attemptstocheck = NULL;
    // All login attempts are counted from the past 2 hours.
    if (isset($timeinterval, $loginatempts)) {
        $valid_attempts = $now - ($timeinterval);
        $attemptstocheck = $loginatempts;
    } else {
        $valid_attempts = $now - (2 * 60 * 60);
        $attemptstocheck = 5;
    }
    if ($stmt = $mysqlcon->prepare("SELECT time FROM login_attempts WHERE user_id = :id AND time > '$valid_attempts'")) {
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $dbresult = $stmt->fetchAll();
        if (count($dbresult) > $attemptstocheck) {
            return true;
        } else {
            return false;
        }
    } else {
        // Could not create a prepared statement
        header("Location: ../error.php?err=Database error: cannot prepare statement");
        exit();
    }
}


function clean_login_attempts($mysqlcon, $usrid = NULL)
{
    $stmt = NULL;
    if (isset($usrid)) {
        $stmt = "DELETE FROM login_attempts WHERE user_id = :ID";
    } else {
        $stmt = "DELETE FROM login_attempts";
    }
    if ($stmt = $mysqlcon->prepare($stmt)) {
        if (isset($usrid)) {
            $stmt->bindParam(':ID', $usrid);
        }
        $stmt->execute();
        $slcstmt = NULL;
        if (isset($usrid)) {
            $slcstmt = " SELECT * FROM login_attempts WHERE user_id = :ID";
        } else {
            $slcstmt = "SELECT * FROM login_attempts";
        }
        if ($slcstmt = $mysqlcon->prepare($slcstmt)) {
            if (isset($usrid)) {
                $slcstmt->bindParam(':ID', $usrid);
            }
            $slcstmt->execute();
            $slcdbresult = $slcstmt->fetchAll();
            if (count($slcdbresult) == 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}

?>