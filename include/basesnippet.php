<?php
require_once (realpath(dirname(__FILE__)."/pdoinit.php"));
require_once (realpath(dirname(__FILE__)."/ProjectCollection.php"));




abstract class GetSnipper {
	
	abstract function doopenTaskTrackerCollection($type);


}

class GetSnipperCollection extends GetSnipper {
	
	private $context = "openTaskTrackerCollection";
	
	function doopenTaskTrackerCollection($type) {
		
		$collection = NULL;//testsynchrinsation
		
		switch ($type) {
			
			case "ProjectCollection":
				$collection = new ProjectCollection;
				break;

			default:
				$collection = "";
				break;
				
		}
		return $collection;
	}
}




abstract class openTaskTrackerSnippet
{
	public $html;
	public $json;
	public $pdo=null;

 	function __construct() {
    	$con = new connect_pdo();
    	$this->pdo = $con->dbh();
     }
    
    function __destruct() {
    	$this->pdo=null;
     }

	public function getHtml(){
		return $this->html;
	}
	
	public function getJson(){
		return $this->json;
	}
	
	abstract function buildContent($filter);
}


