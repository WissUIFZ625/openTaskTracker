<?php
require_once 'loginfunctions_inter.php';
class CustomerDataCollection_inter extends openTaskTrackerSnippet_inter{

	function __construct() {
		parent::__construct();
		 
	}

//	function in_array_r($needle, $haystack, $strict = false) {
//    foreach ($haystack as $item) {
//        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && ShareItemCollection::in_array_r($needle, $item, $strict))) {
//            return true;
//        }
//    }
//    return false;
//	}

//make this ajax_secure
//needs access to session
//check session-fingerprint in new session
	function buildContent_inter($filter) {
		
		sec_session_start();
		$outtype='';
		$outmode='';
		$filtertype='';
		$filterdata='';
		$id_in_cffield='';
		if(is_logedin())
		{
			if( is_array($filter) && array_key_exists('outtype', $filter)){
				if(is_string($filter['outtype']))
				{
					$outtype_temp=strtolower($filter['outtype']);
					switch($outtype_temp)
					{
						case 'html';
							$outtype=$outtype_temp;
							if( is_array($filter) && array_key_exists('outmode', $filter))
							{
								if(is_int($filter['outmode']))
								{
									switch($filter['outmode'])
									{
										case 0:
											$outmode=0;
											break;
										case 1:
											$outmode=1;
											break;
										default:
											$outmode=-1;
											break;
									}
								}
							}
							else
							{
								return;
							}
							break;
						case 'json':
							$outtype=$outtype_temp;
							break;
						default:
							return;
							break;
					}
				}
				else
				{
					return;
				}
			}
			else
			{
				return;
			}
			//if(is_array($filter) && array_key_exists('id_in_cfield', $filter))
			//{
			//	if(is_int($filter['id_in_cfield']))
			//	{
			//		$id_in_cffield = $filter['id_in_cfield'];
			//	}
			//	
			//}
			if(is_array($filter) && array_key_exists('filtertype', $filter))
			{
				if(is_string($filter['filtertype']))
				{
					switch($filter['filtertype'])
					{
						case 'email';
								if(isset($_SESSION['user_mail']) && !empty($_SESSION['user_mail']) && filter_var($_SESSION['user_mail'], FILTER_VALIDATE_EMAIL))
								{
									$filtertype='email';
									$filterdata=$_SESSION['user_mail'];//Session hier zuordnen
								}
								//else
								//{
								//	return;//notwendig?
								//}
							break;
						default;
							break;
					}
				}
				
			}

			//$statement=$this->pdo->prepare(
			//							   "
			//							   SELECT
			//							   customer.id,email,gender,name,surname, address, plz, city, tel, deactivatedon, identify_hash
			//							   FROM
			//							   customer
			//							   left join clientdb_mapping on customer.which_DB=clientdb_mapping.id
			//							   ORDER BY surname ASC
			//							   "
			//							   );
			$statement=$this->pdo->prepare(
										   "
										   SELECT
										   customer.id,email,gender,name,surname, address, plz, city, tel, deactivatedon, identify_hash, ident_confirmcode, dbmail_sent
										   FROM
										   customer
                                           left join customer_mapping
                                           on customer_mapping.customer_id=customer.id
                                           left join clientdb_mapping 
                                           on customer_mapping.db_mapping_id=clientdb_mapping.id
										   ORDER BY surname ASC
										   "
										   );
			if(isset($filtertype) && !empty($filtertype))
			{

				switch($filtertype)
				{
					case 'email':
						//$statement=$this->pdo->prepare(
						//				   "
						//				   SELECT
						//				   customer.id,email,gender,name,surname, address, plz, city, tel, deactivatedon, identify_hash
						//				   FROM
						//				   customer
						//				   left join clientdb_mapping on customer.which_DB=clientdb_mapping.id
						//				   WHERE email=:EMAIL
						//				   ORDER BY surname ASC
						//				   "
						//				   );
						$statement=$this->pdo->prepare(
										   "
										   SELECT
										   customer.id,email,gender,name,surname, address, plz, city, tel, deactivatedon, identify_hash, customer_mapping.ident_confirmcode, customer_mapping.dbmail_sent, customer_mapping.land, customer_mapping.standort, customer_mapping.namerp
										   FROM
										   customer
                                           left join customer_mapping
                                           on customer_mapping.customer_id=customer.id
                                           left join clientdb_mapping 
                                           on customer_mapping.db_mapping_id=clientdb_mapping.id
										   WHERE email=:EMAIL
										   ORDER BY surname ASC
										   "
										   );
						$statement->bindParam(':EMAIL', $filterdata);

						break;
					default:
						break;
				}
				
			}
			$statement->execute();
			$customerdata=$statement->fetchAll(PDO::FETCH_ASSOC);
			//var_dump($customerdata);
			//return;

			foreach  ( $customerdata as $c_info)
			{
				
				switch($outtype)
				{
					case 'json':
						//Json Support
						$temp =array();
						$mail_used = array();
						if(!in_array($c_info["id"], $mail_used))
						{
							$temp["id"] = $c_info["id"];
							$temp["email"] = $c_info["email"];
							$temp["gender"] = $c_info["gender"];
							$temp["name"] = $c_info["name"];
							$temp["surname"] = $c_info["surname"];
							$temp["address"] = $c_info["address"];
							$temp["plz"] = $c_info["plz"];
							$temp["city"] = $c_info["city"];
							$temp["tel"] = $c_info["tel"];
							$temp["deactivatedon"] = $c_info["deactivatedon"];
							//$ident_hashes = array();
							$ident_hashes['identify_hash'] = array();
							$ident_hashes['ident_confirmcode']= array();
							$ident_hashes['dbmail_sent']= array();
							$ident_hashes['land']=array();
                            $ident_hashes['namerp']=array();
                            $ident_hashes['standort']=array();
							foreach( $customerdata as $c_ident)
							{
								//array_push($ident_hashes, $c_ident['identify_hash']);
								//array_push($ident_hashes['identify_hash'], $c_ident['identify_hash']);

                                if(isset($c_ident['identify_hash']) && !empty($c_ident['identify_hash']))
                                {
                                    array_push($ident_hashes['identify_hash'], $c_ident['identify_hash']);
                                }
                                else
                                {
                                    array_push($ident_hashes['identify_hash'], null);
                                }

								
								if(isset($c_ident['ident_confirmcode']) && !empty($c_ident['ident_confirmcode']))
								{
									array_push($ident_hashes['ident_confirmcode'], true);
								}
								else
								{
									array_push($ident_hashes['ident_confirmcode'], false);
								}

								if(isset($c_ident['dbmail_sent']) && !empty($c_ident['dbmail_sent']))
								{
									array_push($ident_hashes['dbmail_sent'], $c_ident['dbmail_sent']);
								}
								else
								{
									array_push($ident_hashes['dbmail_sent'], false);
								}
                                if(isset($c_ident['land']) && !empty($c_ident['land']))
                                {
                                    array_push($ident_hashes['land'], $c_ident['land']);
                                }
                                else
                                {
                                    array_push($ident_hashes['land'], 'Schweiz');
                                }
                                if(isset($c_ident['namerp']) && !empty($c_ident['namerp']))
                                {
                                    array_push($ident_hashes['namerp'], $c_ident['namerp']);
                                }
                                else
                                {
                                    array_push($ident_hashes['namerp'], 'Geben Sie Ihrem openTaskTracker einen Namen...');
                                }
                                if(isset($c_ident['standort']) && !empty($c_ident['standort']))
                                {
                                    array_push($ident_hashes['standort'], $c_ident['standort']);
                                }
                                else
                                {
                                    array_push($ident_hashes['standort'], 'Definieren Sie hier einen Standort...');
                                }
								//array_push($ident_hashes['dbmail_sent'], $c_ident['dbmail_sent']);
							}
							$temp["ident"] =$ident_hashes;
							array_push($mail_used, $c_info["id"]);
						}	
							
							
							$devjson=array();
							$devjson["customerdata"]=$temp;
							
							$frt= json_encode($devjson);
							$this->json=$frt;
						
						//}
						break;
					case 'html':
						$this->html  = '<div class="vswicont">'; //andere Klasse?
						switch($outmode)
						{
							//html IST GENAUER ZU TESTEN, WENN BENÖTIGT
							case 0:
								//Anpassen, wenn benötigt//stammt aus sharing, Code muss angepasst werden
								//$this->html.='<div draggable="true" ondragstart="drag(event)" title="Name:&nbsp;'.$share_item_name.'&nbsp;  Beschreibung:  &nbsp;'.$share_item_desc.'" id="shareitem_'.$share_item_share_id.'" name="'.$share_item_name.'" class="shareitem normal" data-toggle="tooltip"   >';
								//$this->html.= '<img src="img/share_item.png" draggable="false" class="bildtestweb">';
								//$this->html.='</br><span>'.$share_item_name.'</span></br>';
								//$this->html.="</div>";
								break;
							case 1:
								break;
							default:
								break;
						}
						$this->html.='</div>';
						break;
					default:
						break;
				}
			}
		}
	}
}
?>