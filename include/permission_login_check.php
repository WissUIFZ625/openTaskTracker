<?php

include_once 'loginfunctions.php';
include_once 'permission_functions.php';

function is_logedin_and_permitted_orig ($mysqlcon, $user_id, $request_uri, $not_logedIn_url, $not_permission_url, $message_toDisplay_NotPermited, $return_back = NULL)
{
    //Validate
    if(!isset($return_back))
    {
        $return_back = false;
    }
    $request_uri = end(explode("/", $request_uri));
    $request_uri = explode("?",$request_uri)[0];
    if(!login_check($mysqlcon))
    {
        header('Location:'.$not_logedIn_url); 
    }
    else
    {
        if(!permission_check($mysqlcon, $user_id, $request_uri))//$pdo, $_SESSION['user_id'], $uri
        {
            if($return_back == true)
            {
                echo "<script>
                alert('".$message_toDisplay_NotPermited."');
                window.history.back();
                </script>";
                //header('Location:http://www.google.ch');
            }
            else
            {
				//echo
				//'
				//<html ng-app="noPermitApp">
				//	<head>
				//		<script src="bower_components/jquery/dist/jquery.min.js"></script>
				//		<script src="bower_components/angular/angular.min.js"></script>
				//		
				//		<script src="bower_components/angular-animate/angular-animate.min.js"></script>
				//		<script src="bower_components/angular-aria/angular-aria.min.js"></script>
				//		<script src="bower_components/angular-material/angular-material.min.js"></script>
				//		<link rel="stylesheet" type="text/css" media="screen" href="bower_components/angular-material/angular-material.min.css" />
				//		<script src="js/ctrl/nopermitctrl.js"></script>
				//		<script src="js/ctrl/nopermit.js"></script>
				//	</head>
				//	<body ng-controller="noPermitCtrl">
				//	<script>
				//		$(document).ready(function () {
				//			handleNoPermitAlert('.$message_toDisplay_NotPermited.');
				//		});
				//		
				//	</script>
				//	</body>
				//</html>
				//';
                echo "<script>
                alert('".$message_toDisplay_NotPermited."');
                window.location.href='".$not_permission_url."';
                </script>";
            //header('Location:http://www.google.ch');
            }
        }
        else
        {
            return true;
        }
    }
    
}


function go_back($display_url, $moveback, $message_toDisplay_NotPermited) {
	
	if($moveback == true)
	{
		echo "<script>
                alert('".$message_toDisplay_NotPermited."');
                window.history.back();
                </script>";
	}
	else
	{
		echo "<script>
                alert('".$message_toDisplay_NotPermited."');
                window.location.href='".$display_url."';
                </script>";
		
		//echo	'	
		//		<html ng-app="noPermitApp">
		//			<head>
		//				<script src="bower_components/jquery/dist/jquery.min.js"></script>
		//				<script src="bower_components/angular/angular.min.js"></script>
		//				
		//				<script src="bower_components/angular-animate/angular-animate.min.js"></script>
		//				<script src="bower_components/angular-aria/angular-aria.min.js"></script>
		//				<script src="bower_components/angular-material/angular-material.min.js"></script>
		//				<link rel="stylesheet" type="text/css" media="screen" href="bower_components/angular-material/angular-material.min.css" />
		//				<script src="js/ctrl/nopermitctrl.js"></script>
		//				<script src="js/ctrl/nopermit.js"></script>
		//			</head>
		//			<body ng-controller="noPermitCtrl">
		//			<script>
		//				//$(document).ready(function () {
		//					handleNoPermitAlert('.$message_toDisplay_NotPermited.');
		//				});
		//				
		//			</script>
		//			</body>
		//		</html>
		//		';
	}
}

function is_user_permitted ($pdo, $request_permission, $not_logedIn_url, $not_permission_url, $message_toDisplay_NotPermited, $return_back = NULL)
{
	
	if(isset($_SESSION['location_id']) && isset($_SESSION['user_id'])){
		
		$location_id=$_SESSION['location_id'];
		$user_id=$_SESSION['user_id'];
		
		is_logedin_and_permitted ($pdo, $user_id, $location_id, $request_permission, $not_logedIn_url, $not_permission_url, $message_toDisplay_NotPermited, $return_back);
	
	}else{
	
		//go_back($not_permission_url, $return_back, $message_toDisplay_NotPermited);
		//header('Location: http://www.google.com/');
		//$index_url_temp = explode($_SERVER['PHP_SELF'];
		
		//$full_url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
		$full_url = $_SERVER['PHP_SELF'];
		$full_url = explode('/',$full_url);
		//$full_url = $full_url[0].'/'.$full_url[1].'/index.php';
		$full_url = '../index.php';
		//header('Location:http://'.$full_url);
		go_back($full_url, false, $message_toDisplay_NotPermited);
		
	}
	

}


function is_as_admin_permitted ($pdo,  $not_logedIn_url, $not_permission_url, $message_toDisplay_NotPermited, $return_back = true)
{

    if(isset($_SESSION['user_id'])){

        $user_id=$_SESSION['user_id'];

        if(isAdmin($pdo, $user_id)){
            return;
        }

    }

    //go_back($not_permission_url, $return_back, $message_toDisplay_NotPermited);
    //header('Location: http://www.google.com/');
    //$index_url_temp = explode($_SERVER['PHP_SELF'];

    //$full_url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $full_url = $_SERVER['PHP_SELF'];
    $full_url = explode('/',$full_url);
    //$full_url = $full_url[0].'/'.$full_url[1].'/index.php';
    $full_url = 'index.php';
    //header('Location:http://'.$full_url);
    go_back($full_url, false, $message_toDisplay_NotPermited);

}

function is_first_login_activ ($pdo, $not_logedIn_url, $not_permission_url, $message_toDisplay_NotPermited, $return_back = true )
{

    if(isset($_SESSION['user_id'])){

        $user_id=$_SESSION['user_id'];

        if(is_first_Login_approved($pdo, $user_id)){
            return;
        }

    }

    //go_back($not_permission_url, $return_back, $message_toDisplay_NotPermited);
    //header('Location: http://www.google.com/');
    //$index_url_temp = explode($_SERVER['PHP_SELF'];

    //$full_url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
    $full_url = $_SERVER['PHP_SELF'];
    $full_url = explode('/',$full_url);
    //$full_url = $full_url[0].'/'.$full_url[1].'/index.php';
    $full_url = '../index.php';
    //header('Location:http://'.$full_url);
    go_back($full_url, false, $message_toDisplay_NotPermited);

}


function is_logedin_and_permitted ($mysqlcon, $user_id, $location_id, $request_permission, $not_logedIn_url, $not_permission_url, $message_toDisplay_NotPermited, $return_back = NULL)
{

	
	if(!login_check($mysqlcon))
	{
		//Destroy Session manually?
		header('Location:'.$not_logedIn_url);
	}
	else
	{
		if(isAdmin($mysqlcon, $user_id)){
			return;
		}
		
		$permission=get_user_right_for_location($mysqlcon, $user_id, $location_id);

		$allowed=( ((intval($request_permission) & intval($permission)) == intval($request_permission)) &&  (intval($permission) != LocationRight::NOACCESS));
		
		if(!$allowed)//$pdo, $_SESSION['user_id'], $uri
		{
			go_back($not_permission_url, $return_back, $message_toDisplay_NotPermited);
		}
	
	}

}

//New
function is_logedin_and_allowed($mysqlcon, $user_id, $location_id, $request_permission)
{
	$permission=get_user_right_for_location($mysqlcon, $user_id, $location_id);
	$allowed=( ((intval($request_permission) & intval($permission)) == intval($request_permission)) &&  (intval($permission) != LocationRight::NOACCESS));
	
	//if((login_check($mysqlcon) && $allowed) || (login_check($mysqlcon) && isAdmin($mysqlcon, $user_id)))
	if(login_check($mysqlcon))
	{
		if(isAdmin($mysqlcon, $user_id))
		{
			return true;
		}
		else
		{
			if($allowed)
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
		return false;
	}
	
}
//--New






?>