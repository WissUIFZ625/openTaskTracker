<?php
//$pdo=new PDO("mysql:dbname=relay;host=127.0.0.1","root","");


class LocationRight {
	const NOACCESS = -1;
	const UNDEFINED = 0;
	const _SWITCH = 1;
	const CONF = 31;
}



class VirtualNode {

	const DIRECTORY = 10000;
	const ROOT = 10001;
}


class connect_pdo
{

    protected $con;
    protected $db_host;  //  hostname
    protected $db_name;  //  databasename
    protected $db_user;  //  username
    protected $user_pw;  //  password
    protected $user_db;  //  password
    protected $server_name; //Hostname (xaxad.xy)


        public function __construct(){


            try {

                        $this->db_host = 'localhost';  //  hostname
                        $this->db_name = 'ott';  //  databasename
                        $this->db_user = 'root';  //  username
                        $this->user_pw = '';  //  password


            $this->getSqlCredentials();
            $this->con = new PDO('mysql:host='.$this->db_host.'; dbname='.$this->db_name, $this->db_user, $this->user_pw, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET sql_mode='NO_AUTO_VALUE_ON_ZERO',  NAMES 'utf8'") );
			$this->con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			$this->con->exec("SET CHARACTER SET utf8");  //  return all sql requests as UTF-8
		}
		catch (PDOException $err) {
			echo "harmless error message if the connection fails";
			$err->getMessage() . "<br/>";

			die();  //  terminate connection
		}
	}


        public function getSqlCredentials(){


            $ini_array = @parse_ini_file("/opt/openTaskTracker/openTaskTracker.ini", TRUE);//pathmark
            if ($ini_array == FALSE) return;

            if( array_key_exists("dbhost", $ini_array["Database"])){

                $this->db_host=$ini_array["Database"]["dbhost"];
            }


            if( array_key_exists("database", $ini_array["Database"])){

                $this->db_name=$ini_array["Database"]["database"];
            }

            if( array_key_exists("user", $ini_array["Database"])){

                $this->db_user=$ini_array["Database"]["user"];
            }

            if( array_key_exists("password", $ini_array["Database"])){

                $this->user_pw=$ini_array["Database"]["password"];
            }


        }

	public function dbh()
	{
		return $this->con;
	}
	//Added für Kundenkonto
	
	public function get_all_tblview_from_db($con)
	{
		//funktion auch ins xpoinit kopiert
		$output=false;
		$statement=$con->prepare(
					   "
						SHOW FULL TABLES;
					   "
					  );
		if($statement->execute())
		{
			$all_tables=$statement->fetchAll(PDO::FETCH_ASSOC);
			$all_tables=self::old_tbl_set_generic_keys($all_tables);
			$output = $all_tables;
		}
		return $output;
	}
	function old_tbl_set_generic_keys($arr_old_tbls)
	{
		//funktion auch ins xpoinit kopiert
		$output=$arr_old_tbls;
		if(is_array($arr_old_tbls))
		{
			$all_tables_old_empty = array_filter($arr_old_tbls);
			if($all_tables_old_empty != false && !empty($all_tables_old_empty) && is_array($all_tables_old_empty))
			{
				$all_tables_old_new_keys=[];
				foreach($arr_old_tbls as $single_table)
				{
					$temp_new_keys=[];
					$arr_keys=array_keys($single_table);
					$temp_new_keys['tablename']=$single_table[$arr_keys[0]];
					$temp_new_keys['tablename']= strtolower($temp_new_keys['tablename']);
					$temp_new_keys['type']=$single_table[$arr_keys[1]];
					$temp_new_keys['type']= strtolower($temp_new_keys['type']);
					array_push($all_tables_old_new_keys, $temp_new_keys);
				}
				if(array_filter($all_tables_old_new_keys) != false && !empty(array_filter($all_tables_old_new_keys)) && is_array(array_filter($all_tables_old_new_keys)))
				{
					$output=$all_tables_old_new_keys;
				}
			}
		}
		return $output;
	}
}
