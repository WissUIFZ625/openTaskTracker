<?php
require_once 'validate.php';
require_once 'handle_session.php';
require_once 'pdoinit.php';

//$con = new connect_pdo();

function val_san_input($rawdata,&$var_to_write_to, $validator, $filter, $strict=true)//Maybe add Options later?
{
	$output=false;
	try
	{
		$filter_id_arr=[];
		foreach (filter_list() as  $filter_name)
		{
			if(filter_id($filter_name) !==false)
			{
				array_push($filter_id_arr,filter_id($filter_name));
			}
		}
		$is_valid_validator=false;
		$is_valid_filter=false;
		if(filter_id($validator)!== false)
		{
			$validator = filter_id($validator);
			$is_valid_validator=true;
		}
		elseif(in_array($validator,$filter_id_arr))
		{
			$is_valid_validator=true;
		}
		
		if(filter_id($filter)!== false)
		{
			$filter = filter_id($filter);
			$is_valid_filter=true;
		}
		elseif(in_array($filter,$filter_id_arr))
		{
			$is_valid_filter=true;
		}
		if($is_valid_validator && $is_valid_filter)
		{
			if(isset($rawdata) && !empty($rawdata))
			{
				if(filter_var($rawdata, $validator) !== false)
				{
                         //speciality :email-adress
                         //if(filter_var($rawdata, FILTER_VALIDATE_EMAIL) !== false && Validate_UInput::ValidateWConst(Validate_UInput::RegEMAIL, $rawdata))
                         //{
                         //     $clean_email = filter_var($rawdata,FILTER_SANITIZE_EMAIL);
                         //     if ($rawdata == $clean_email)
                         //     {
                         //          // now you know the original email was safe 
                         //          $rawdata = $clean_email;
                         //     }
                         //}
					if(filter_var($rawdata, $filter) !== false)
					{
						$var_to_write_to= filter_var($rawdata, $filter);
						if(Validate_UInput::Sanitize($rawdata, true, true))
						{
							$var_to_write_to = Validate_UInput::Sanitize($rawdata, true);
							//if(Validate_UInput::IsSanitized($var_to_write_to))
							//{
							$output=true;
							//}
						}
					}
				}
			}
		}
	}
	catch(Exception $ex)
	{
		if($strict)
		{
			$output=false;
		}
		else
		{
			$output=-1;
		}
		
	}
	return $output;
//Filterwerte als ints
//0=int=257
//1=boolean=258
//2=float=259
//3=validate_regexp=272
//4=validate_url=273
//5=validate_email=274
//6=validate_ip=275
//7=string=513
//8=stripped=513
//9=encoded=514
//10=special_chars=515
//11=unsafe_raw=516
//12=email=517
//13=url=518
//14=number_int=519
//15=number_float=520
//16=magic_quotes=521
//17=callback=1024
}
function getall_admins_remote_db($pdo, $idhash)
{
	$output=false;
	$con = new connect_pdo();//connect_pdo::check_db_exists<--Darauf umstellen
	//echo 'Hello there';
	//return;
	$db_name=$con->check_hash_exists_mapping_rdbn($pdo,$idhash);
	//var_dump($db_name);
	//return;
	if(!empty($db_name) && $db_name !== false)
	{
		$db_exists=$con->check_db_exists($pdo, $db_name);
		if($db_exists)
		{
			$pdo = $con->change_db($pdo, $db_name, false);
			//var_dump($pdo);
			//return;
			if($pdo !== false)
			{
				//var_dump($pdo);
				//return;
				
				$statement =$pdo->prepare(
					"
						SELECT
						email
						FROM `members`
						WHERE isBatman=1 AND deactivatedon=0
					"
					);
				if($statement->execute())
				{
					$db_result=$statement->fetchAll(PDO::FETCH_ASSOC);
					if(isset($db_result) && !empty($db_result))
					{
						
						$output= $db_result;
					}
				}
			}
		}
	}
	$pdo = $con->change_db($pdo, $db_name, true);//macht Probleme
	return $output;
}
function check_dbhash_exists($pdo, $db_hash)
{
	//echo '<hr>';
	//var_dump($db_hash);
	//echo '<hr>';
	$output=false;
	//$statement =$pdo->prepare(
	//				"
	//					SELECT
	//					ident_confirmcode
	//					FROM `customer`
	//					WHERE ident_confirmcode IS NOT NULL
	//				"
	//				);
	$statement =$pdo->prepare(
					"
						SELECT
						ident_confirmcode
						FROM `customer_mapping`
						WHERE ident_confirmcode IS NOT NULL
					"
					);
	if($statement->execute())
	{
		$dbresult=$statement->fetchAll(PDO::FETCH_ASSOC);
		$arr_to_chk=[];
		foreach($dbresult as $db_cc)
		{
			array_push($arr_to_chk,$db_cc['ident_confirmcode']);
		}
		//echo '<hr>';
		//var_dump($arr_to_chk);
		//echo '<hr>';
		//echo '<hr>';
		//var_dump(in_array($db_hash, $arr_to_chk));
		//echo '<hr>';
		if(in_array($db_hash, $arr_to_chk))
		{
			$output = true;
		}
	}
	return $output;
}
function empty_multi($first,$raw_return=false, ...$others)
{
	return false;
	//funktioniert noch nicht ganz, ist zu überprüfen//zu wenig Zeit
	$out=[];
	if(empty($first))
	{
		array_push($out,true);
	}
	else
	{
		array_push($out,false);
	}

	foreach($others as $var)
	{
		if(empty($var))
		{
			array_push($out,true);
		}
		else
		{
			array_push($out,false);
		}
	}
	if($raw_return)
	{
		return $out;
	}
	else
	{
		if(in_array(false, $out))
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
?>