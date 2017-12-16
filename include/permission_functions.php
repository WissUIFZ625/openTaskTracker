<?php
include_once 'usr_grp_functions.php';


//Permissions für Seiten

define ("SITES", serialize (array ("installation.php", "enoceaninstall.php", "enoceanconfig.php", "webswitch.php", "connectmiddle.php", "switchgui.php", "users.php", "access_management.php", "location.php", "mobswitch2.php", "mobswitch3.php")));
$sites_permission = unserialize (SITES);

function isAdmin ($pdo, $user_id)
{

	$stmt = $pdo->prepare("SELECT usr_id, usr_name, is_register_user FROM User WHERE usr_id = :USERID");
	$stmt->bindParam(':USERID', $user_id);
	$stmt->execute();

	$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);

	if($dbresult){
        $is_register_user = $dbresult['is_register_user'];

        if($is_register_user==1){
                return true;
            }
	}
	return false;

}

function firstLogin ($pdo, $first_login)
{
	$stmt = $pdo->prepare("SELECT first_login FROM members WHERE isBatman And first_login = :FIRSTLOGIN LIMIT 1");
	$stmt->bindParam(':FIRSTLOGIN', $first_login);
	$stmt->execute();

	$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);

	if($dbresult){
		$firstLogin = $dbresult['first_login'];
		if($firstLogin == 1){
		return true;
		}
	}
	else {
		return false;
	}

}

function is_first_Login_approved ($pdo, $user_id)
{
    $firstLogin = 1;
    $stmt = $pdo->prepare("SELECT first_login FROM members WHERE id = :USERID And first_login = :FIRSTLOGIN LIMIT 1");
    $stmt->bindParam(':USERID', $user_id);
    $stmt->bindParam(':FIRSTLOGIN', $firstLogin);
    $stmt->execute();

    $dbresult = $stmt->fetch(PDO::FETCH_ASSOC);

    if($dbresult){
        $result = $dbresult['first_login'];
        if($result == 1){
            return false;
        }
    }
    else {
        return true;
    }

}



function registerSite ($pdo)
{
	$url = $_SERVER['REQUEST_URI'];
	$user_id;
	if(isset($_SESSION['user_id']) && !empty($_SESSION['user_id']))
	{
		$user_id = $_SESSION['user_id'];
	}
	else
	{
		$user_id = "";
	}
	$location_id = (isset($_SESSION['location_id']) )? $_SESSION['location_id']:null;

	$stmt = $pdo->prepare("SELECT COUNT(*) as user_count FROM user_custom_setting WHERE user_id = :USERID");
	$stmt->bindParam(':USERID', $user_id);
	$stmt->execute();
	
	$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);

	$exist = ($dbresult && ($dbresult["user_count"] == 1) )?true:false;
	
	
	$query = ($exist) ? "UPDATE  user_custom_setting SET last_url = :LASTURL, location_id = :LOCATIONID WHERE user_id = :USERID ":
									"INSERT INTO  user_custom_setting (user_id, location_id, last_url) VALUES (:USERID, :LOCATIONID, :LASTURL)";
	
	$stmt = $pdo->prepare($query);
	
	if($location_id){
		$stmt->bindParam(':LOCATIONID', $location_id, PDO::PARAM_INT);
	}else{
		$stmt->bindParam(':LOCATIONID', $location_id, PDO::PARAM_NULL);
	}
	
	$stmt->bindParam(':LASTURL', $url, PDO::PARAM_STR, 256);
	$stmt->bindParam(':USERID', $user_id, PDO::PARAM_INT);
	
	$stmt->execute();

}





function get_user_right_for_location($pdo, $user_id, $location_id){

   if(isAdmin($pdo, $user_id)) return LocationRight::CONF;
 
	$respermession=LocationRight::UNDEFINED;

	try{

		$statgrp=$pdo->prepare("SELECT ug.group_id, ml.user_id FROM user_group ug LEFT JOIN member_location ml ON ml.group_id=ug.group_id WHERE ml.user_id = :USERID");
		$statgrp->bindParam(':USERID', $user_id,  PDO::PARAM_INT);
		$statgrp->execute();
		$usergroups=$statgrp->fetchAll(PDO::FETCH_ASSOC);

		$statloc=$pdo->prepare("SELECT * from location");
		$statloc->execute();
		$locs=$statloc->fetchAll(PDO::FETCH_ASSOC);

		$statperm=$pdo->prepare("SELECT * from rights_level_permission");
		$statperm->execute();
		$permission=$statperm->fetchAll(PDO::FETCH_ASSOC);

		$respermission_grp=get_usr_right_for_location($location_id, $usergroups, $locs, $permission);
		$respermission_usr=get_usr_directright_for_location($user_id, $location_id,  $locs, $permission);
		
		if( ($respermission_grp == LocationRight::NOACCESS) || ($respermission_usr == LocationRight::NOACCESS)){
			$respermession=LocationRight::NOACCESS;
		}else{
			$respermession = ($respermission_grp | $respermission_usr);
		}

	}catch (Exception $e) {
		
		return LocationRight::NOACCESS;
		
	}

	return $respermession;

}


function get_usr_directright_for_location( $user_id, $location_id, $locs, $perms){

	$respermission=LocationRight::UNDEFINED;

	foreach  ( $locs as $loc ) {
	
		$lid = $loc["location_id"];
		$pid = $loc["parent_id"];
	
		if($lid != $location_id) continue;
		$type =$loc["type"];
	
		foreach  ( $perms as $p ){
				
			if($p["user_id"]==$user_id && $p["location_id"]==$location_id){
				$_p=intval($p["permission_level"]);
				if($_p==LocationRight::NOACCESS)return LocationRight::NOACCESS;
				$respermission=$respermission|$_p;
				if($type==3)return $respermission;
			}
				
		}
	
		reset($locs);reset($perms);
	
		$result=get_grp_right_for_location($user_id, $pid, $locs, $perms);
		if($result == LocationRight::NOACCESS) return LocationRight::NOACCESS;
		$respermission=$respermission|$result;
	
	}
	return $respermission;

}



function get_usr_right_for_location($location_id, $usrgrps, $locs, $perms){

	$respermition=LocationRight::UNDEFINED;

	foreach  ( $usrgrps as $group) {

		$gid = $group["group_id"];
		$result=get_grp_right_for_location($gid, $location_id, $locs, $perms);
		if($result == LocationRight::NOACCESS) return LocationRight::NOACCESS;
		$respermition=$respermition|$result;

	}

	return $respermition;

}


function get_grp_right_for_location($group_id, $location_id, $locs, $perms){

	$respermission=LocationRight::UNDEFINED;

	foreach  ( $locs as $loc ) {

		$lid = $loc["location_id"];
		$pid = $loc["parent_id"];

		if($lid != $location_id) continue;
		$type =$loc["type"];

		foreach  ( $perms as $p ){
			
			if($p["group_id"]==$group_id && $p["location_id"]==$location_id){
				$_p=intval($p["permission_level"]);
				if($_p==LocationRight::NOACCESS)return LocationRight::NOACCESS;
				$respermission=$respermission|$_p;
				if($type==3)return $respermission;
			}
			
		}

		reset($locs);reset($perms);

		$result=get_grp_right_for_location($group_id, $pid, $locs, $perms);
		if($result == LocationRight::NOACCESS) return LocationRight::NOACCESS;
		$respermission=$respermission|$result;

	}
	return $respermission;
	
}


function get_user_url($mysqlcon) 
{
	$user_url = openTaskTrackerUrl::DEFAULT_USER_URL;
	
	$allowedl=false;
	$stmtloc = $mysqlcon->prepare("SELECT * from location");
	$stmtloc->execute();
	$locs = $stmtloc->fetchAll(PDO::FETCH_ASSOC);

	if (isset($_SESSION['user_id'])){
		
		$user_id=$_SESSION['user_id'];
	
		$stmt = $mysqlcon->prepare("SELECT ucs.last_url as _last_url , ucs.location_id AS _location_id, l.name AS _name FROM user_custom_setting ucs LEFT JOIN location l ON l.location_id = ucs.location_id WHERE ucs.user_id = :USER_ID");
		$stmt->bindParam(':USER_ID', $user_id);
		$stmt->execute();
		$dbresult = $stmt->fetch(PDO::FETCH_ASSOC);
	
		
		if($dbresult){
			
			$location_id=$dbresult['_location_id']; 
			$name=$dbresult['_name'];

			$perm=get_user_right_for_location($mysqlcon, $user_id, $location_id);
			$_allowed=is_allowed_user_url($user_url, $perm);
			
			if($_allowed){
				$allowedl=true;
				$user_url=$dbresult['_last_url'];
				$_SESSION['location_id'] = $location_id;
				$_SESSION['location_name'] = $name;
			
			}

		}
	}
	
	
	if(!$allowedl){
		
		foreach($locs as $loc){
				
			$perm=get_user_right_for_location($mysqlcon, $user_id, $loc["location_id"]);
			$_allowed=is_allowed_user_url($user_url, $perm);
			
			if($_allowed){
				$allowedl=true;
				$_SESSION['location_id'] = $loc["location_id"];
				$_SESSION['location_name'] = $loc["name"];
				break;
			}
				
		}
	
	}
	
	if(!$allowedl){
		$user_url=openTaskTrackerUrl::NO_ROOM_URL;
	}


	//return $urlpath = ".." . DIRECTORY_SEPARATOR . $user_url;
	return $urlpath = $user_url;
}


function is_allowed_user_url($user_url, $perm)
{
	$result = false;
	$perm=intval($perm);
	if($perm == LocationRight::NOACCESS) return false;

	switch($user_url){

		case openTaskTrackerUrl::WEBSWITCH_URL :
			$result = ( (LocationRight::_SWITCH & intval($perm)) == LocationRight::_SWITCH );
			break;
		case openTaskTrackerUrl::WEBSWITCH_CONF_URL :
			$result = ( (LocationRight::CONF & intval($perm)) ==  LocationRight::CONF );
			break;
		case openTaskTrackerUrl::ENOSWITCH_CONF_URL :
			$result = ( (LocationRight::CONF & intval($perm)) ==  LocationRight::CONF );
			break;
		case openTaskTrackerUrl::ENOSWITCH_INSTALL_URL :
			$result = ( (LocationRight::CONF & intval($perm)) == LocationRight::CONF );
			break;
		case openTaskTrackerUrl::USER_URL :
				$result = ( (LocationRight::CONF & intval($perm)) == LocationRight::CONF );
				break;
		case openTaskTrackerUrl::LOCATION_URL :
			$result = ( (LocationRight::CONF & intval($perm)) == LocationRight::CONF );
			break;
		case openTaskTrackerUrl::ACCESS_URL :
			$result = ( (LocationRight::CONF & intval($perm)) == LocationRight::CONF );
			break;
		case openTaskTrackerUrl::MOBILE2_URL :
			$result = ( (LocationRight::_SWITCH & intval($perm)) == LocationRight::_SWITCH );
			break;
		case openTaskTrackerUrl::MOBILE3_URL :
			$result = ( (LocationRight::_SWITCH & intval($perm)) == LocationRight::_SWITCH );
			break;
		case openTaskTrackerUrl::DEFAULT_USER_URL : // Eingefügt, Pfad /Permissionbug zu beheben
			$result = ( (LocationRight::_SWITCH & intval($perm)) == LocationRight::_SWITCH );
			break;
		default:
			break;
	}

	return $result;

}


?>