<?php
//Test the whole thing
class SecureSessionHandler extends SessionHandler /*implements SessionHandlerInterface*/
{

	//COOKIES
	const LIFETIME = 0;
	const ONLYHTTPS = false;//Set to false for Development
	const HTTPONLY = true;
	
	//GENERAL
	const USETRANSID = 0;
	const ONLYCOOKIES = 1;
	const RANDOMIZE_REGEN=4;
	const MODIFY_INI_STARTUP=true;
	const SESSION_ENCRYPT=true;
	const SESSION_ENCRYPT_LIB='openssl';
	const SESSION_ENCRYPT_ALGO='aes-256-ctr';
	const HASHING_ALGO='sha512';
	const SESSION_NAME='my_xaxada_session';
	const SESSION_TTL=30;//In Minutes
	const FINGERPRINT_CONTAINS_IP=true;
	const FINGERPRINT_CONTAINS_SALT=true;

	//protected $key, $session_name, $cookie;
	//private $salt
	
	protected $key, $name, $cookie, $salt, $iv;
	//$key = hash( 'sha256', $secret_key );
	//$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

    public function __construct($key, $name = self::SESSION_NAME, $cookie = [])//$cookies ist norwendig?
    {
		try
		{
			$session_encrypt=false;
			if(self::SESSION_ENCRYPT)
			{
				if(extension_loaded(self::SESSION_ENCRYPT_LIB))
				{
					$session_encrypt=true;
				}
				else
				{
					throw new Exception('Extension "'.self::SESSION_ENCRYPT_LIB.'" konnte nicht geladen werden!');
				}
			}
			$this->key = hash( self::HASHING_ALGO, $key );
			/*$this->iv = hash( self::HASHING_ALGO, mt_rand() );*/ //substr( hash( self::HASHING_ALGO, $secret_iv ), 0, 16 );
			$this->iv = substr( hash( self::HASHING_ALGO, mt_rand() ), 0, 16 );
			//$this->key = $key;
			$this->name = $name;
			$this->cookie = $cookie;
			
			//$key = hash( self::HASHING_ALGO, $secret_key );
			//$iv = substr( hash( self::HASHING_ALGO, $secret_iv ), 0, 16 );
	
			$secure_http=false;
			if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']))
			{
				if(self::ONLYHTTPS)
				{
					$secure_http=true;
				}
			}
			
			$this->cookie += [
				'lifetime' => 0,
				'path'     => ini_get('session.cookie_path'),
				'domain'   => ini_get('session.cookie_domain'),
				'secure'   => $secure_http,
				'httponly' => true
			];
	
			$this->setup();
		}
		catch (Exception $ex)
		{
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//die();
			return false;
		}
    }
	
	
	protected function setup()
    {
		$out=false;
		try
		{
			if(self::MODIFY_INI_STARTUP)
			{
				ini_set('session.use_cookies', self::ONLYCOOKIES);
				ini_set('session.use_only_cookies', self::ONLYCOOKIES);
				ini_set('session.use_trans_sid',self::USETRANSID);
			}
	
			session_name($this->name);
	
			session_set_cookie_params(
				$this->cookie['lifetime'], $this->cookie['path'],
				$this->cookie['domain'], $this->cookie['secure'],
				$this->cookie['httponly']
			);
			$out=true;
		}
		catch (Exception $ex)
		{
			error_log('Session-Setup-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			//echo "Session-Setup-Error:";
			//$ex->getMessage() . "<br/>";
			$out=false;
			//die();
		}
		return $out;
    }
	
	public /*static*/ function start()
	{
		$out=false;
		try
		{
			//self::initialize();
			
			//$secure_http=false;
			//if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']))
			//{
			//	if(self::ONLYHTTPS)
			//	{
			//		$secure_http=true;
			//	}
			//}
			
			//if (session_id() === '') {
			
			//$cookieParams = session_get_cookie_params();
			//$errors = array_filter($cookieParams);
			
			//if (!empty($errors))
			//{
				if (session_id() === '')
				{
					//session_set_cookie_params(self::LIFETIME, $cookieParams["path"],$cookieParams["domain"], $secure_http,self::HTTPONLY);
					//session_name($session_name);
				    if (session_start())
				    {
					 
					 if(mt_rand(0, self::RANDOMIZE_REGEN) === 0)
					 {
						  //$classObj->refresh();
						   $this->refresh();
					 }
					 //$classObj->key=session_id();
					 //return (mt_rand(0, RANDOMIZE_REGEN) === 0) ? $this->refresh() : true; // 1/5
					 $out=true;
				    }
				}
			//}
		}
		catch (Exception $ex)
		{
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}
	public /*static*/ function stop()
	{
	    $out=false;
	    try
	    {
			if(session_id() !== '')
			{
				$out=false;

				 $_SESSION = [];
		  
				 setcookie(
					$this->name, '', time() - 42000,
					$this->cookie['path'], $this->cookie['domain'],
					$this->cookie['secure'], $this->cookie['httponly']
				 );
				 $out= session_destroy();
			}
	    }
	    catch (Exception $ex)
	    {
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			$out=false;
			//$out=false;
			//die();
		}
		return $out;
	}

	
	public /*static*/ function refresh()
	{
		$out=false;
		try
		{
			session_regenerate_id(true);
			//$this->key=session_id();
			$out=true;
		}
		catch (Exception $ex)
		{
			 //self::stop();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}

	public function encrypt($data, &$data_to_write_to, $lib=self::SESSION_ENCRYPT_LIB, $algorithm=self::SESSION_ENCRYPT_ALGO)
	{
		$out=false;
		try
		{
			$lib=strtolower($lib);
			if(extension_loaded($lib))
			{
				switch($lib)
				{
					case 'openssl':
						if(in_array($algorithm, openssl_get_cipher_methods()))
						{
							$data_to_write_to=openssl_encrypt($data, $algorithm,$this->key,0,$this->iv);
							$out=true;
						}
						else
						{
							throw new Exception($lib.'" konnte nicht mit Algorithmus '.$algorithm.' initialisiert werden!');
						}
						break;
					default:
						throw new Exception('Verschlüsselung "'.$lib.'" wird nicht unterstützt!');
						break;
				}
			}
			else
			{
				throw new Exception('Extension "'.$lib.'" konnte nicht geladen werden!');
			}
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}
	public function decrypt($data,$lib=self::SESSION_ENCRYPT_LIB, $algorithm=self::SESSION_ENCRYPT_ALGO)
	{
		$out=false;
		try
		{
			$lib=strtolower($lib);
			if(extension_loaded($lib))
			{
				switch($lib)
				{
					case 'openssl':
						if(in_array($algorithm, openssl_get_cipher_methods()))
						{
							 $out = openssl_decrypt($data, $algorithm, $this->key, 0, $this->iv);
						}
						else
						{
							throw new Exception($lib.'" konnte nicht mit Algorithmus '.$algorithm.' initialisiert werden!');
						}
						break;
					default:
						throw new Exception('Verschlüsselung "'.$lib.'" wird nicht unterstützt!');
						break;
				}
			}
			else
			{
				throw new Exception('Extension "'.$lib.'" konnte nicht geladen werden!');
			}
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
		return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
	}
	//read/write Session itself, not Session-Data
	public function read($id, $encrypt=self::SESSION_ENCRYPT)
	{
		$out=false;
		try
		{
			if($encrypt)
			{
				//return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
				if($this->decrypt(parent::read($id)) !==false)
				{
					//parent::write($id,$data);
					$out=$this->decrypt(parent::read($id));
					//$out=parent::write($id,$data);
				}
				else
				{
					throw new Exception('Session konnte nicht entschlüsselt werden!');
				}
				//$key = hash( 'sha256', $secret_key );
				//$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
				
				//return parent::write($id, openssl_encrypt($data, self::SESSION_ENCRYPT_ALGO,$this->key,0,$this->iv));
				 //return parent::write($id, mcrypt_encrypt(MCRYPT_3DES, $this->key, $data, MCRYPT_MODE_ECB));
			}
			else
			{
				$out= parent::read($id);
			}
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
		//return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
		//if(extension_loaded('openssl'))
		//{
		//	return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
		//}
		//else
		//{
		//	
		//}
		//return mcrypt_decrypt(MCRYPT_3DES, $this->key, parent::read($id), MCRYPT_MODE_ECB);
	}
	public function write($id, $data, $encrypt=self::SESSION_ENCRYPT)
	{
		$out=false;
		try
		{
			if($encrypt)
			{
				if($this->encrypt($data,$data))
				{
					parent::write($id,$data);
					$out=true;
				}
				else
				{
					throw new Exception('Session konnte nicht verschlüsselt werden!');
				}
				//$key = hash( 'sha256', $secret_key );
				//$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
				
				//return parent::write($id, openssl_encrypt($data, self::SESSION_ENCRYPT_ALGO,$this->key,0,$this->iv));
				 //return parent::write($id, mcrypt_encrypt(MCRYPT_3DES, $this->key, $data, MCRYPT_MODE_ECB));
			}
			else
			{
				parent::write($id,$data);
				$out=true;
			}
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}
	public function is_Fingerprint($use_ip=self::FINGERPRINT_CONTAINS_IP, $use_salt=self::FINGERPRINT_CONTAINS_SALT)
	{
		$out=false;
		try
		{
			//$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
			//$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$this->salt);
			if (isset($_SESSION['_fingerprint']))
			{
				$hash='';
				//$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
				if($use_ip)
				{
					if($use_salt)
					{
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$this->salt);
					}
					else
					{
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')));
					}
				}
				else
				{
					if($use_salt)
					{
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .$this->salt);
					}
					else
					{
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT']);
					}
				}
				//$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$this->salt);
				if($_SESSION['_fingerprint'] === $hash)
				{
					//$random_salt='';
					if($use_ip)
					{
						if($use_salt)
						{
							$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
							$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$random_salt);
							//$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$this->salt);
						}
						else
						{
							$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')));
						}
					}
					else
					{
						if($use_salt)
						{
							$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
							$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .$random_salt);
						}
						else
						{
							$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT']);
						}
					}
					//$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
					//$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$random_salt);
					
					$_SESSION['_fingerprint'] = $hash;
					$this->salt=$random_salt;
					$out=1;
				}
				else
				{
					$out=0;
				}
				
				//$out = $_SESSION['_fingerprint'] === $hash;
			}
			else
			{
				$hash='';
				if($use_ip)
				{
					if($use_salt)
					{
						$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$random_salt);
					}
					else
					{
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')));
					}
				}
				else
				{
					if($use_salt)
					{
						$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .$random_salt);
					}
					else
					{
						$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT']);
					}
				}
				//$random_salt = hash(self::HASHING_ALGO, uniqid(openssl_random_pseudo_bytes(16), TRUE));
				//$hash = hash(self::HASHING_ALGO, $_SERVER['HTTP_USER_AGENT'] .(ip2long($_SERVER['REMOTE_ADDR']) & ip2long('255.255.0.0')).$random_salt);
				$_SESSION['_fingerprint'] = $hash;
				$this->salt=$random_salt;
				$out=true;
			}
			//$_SESSION['_fingerprint'] = $hash;
			//return true;
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;

	}
	public function isExpired($ttl = self::SESSION_TTL)//TTL in Minutes
	{
		//Only function where output = true is bad
		$out=false;
		try
		{
			if(isset($_SESSION['_last_activity']))
			{
				$activity = ($_SESSION['_last_activity']);
				//if ($activity !== false && time() - $activity > $ttl * 60)
				if (time() - $activity > $ttl * 60)//migrate to datetimeobject//@ToDo
				{
					$out=true;
				}
				else
				{
					$_SESSION['_last_activity'] = time();
				}
			}
			else
			{
				$_SESSION['_last_activity'] = time();
			}
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=true;
		}
		return $out;
	}
	public function isValid($ttl = self::SESSION_TTL, $use_ip=self::FINGERPRINT_CONTAINS_IP, $use_salt=self::FINGERPRINT_CONTAINS_SALT)
	{
		//is_Fingerprint($use_ip=self::FINGERPRINT_CONTAINS_IP, $use_salt=self::FINGERPRINT_CONTAINS_SALT)
		//return ! $this->isExpired($ttl) && $this->isFingerprint();
		$out=false;
		try
		{
			if(!$this->isExpired($ttl) && $this->is_Fingerprint($use_ip, $use_salt) ===1)
			{
				$out=true;
			}
			
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}
	public function get($name)
	{
		$out=false;
		try
		{
			$parsed = explode('.', $name);
	
			$result = $_SESSION;
		
			while ($parsed)
			{
				$next = array_shift($parsed);
		
				if (isset($result[$next]))
				{
					//$result = $result[$next];
					$out=$result[$next];
				} else
				{
					$out=null;
					//return null;
				}
			}
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}
	
	public function put($name, $value)
	{
		$out=false;
		try
		{
			$parsed = explode('.', $name);
	
			$session =& $_SESSION;//Referenz
		
			while (count($parsed) > 1)
			{
				$next = array_shift($parsed);
		
				if ( ! isset($session[$next]) || ! is_array($session[$next]))
				{
					$session[$next] = [];
				}
		
				$session =& $session[$next];
			}
		
			$session[array_shift($parsed)] = $value;
			$out=true;
		}
		catch (Exception $ex)
		{
			//echo "Session-Error: ";
			//$ex->getMessage() . "<br/>";
			//self::stop();
			//die();
			error_log('Session-Error('.__FUNCTION__.'): '.$ex->getMessage().' ');
			self::stop();
			$out=false;
		}
		return $out;
	}
}
?>