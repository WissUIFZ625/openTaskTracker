<?php
require_once (realpath(dirname(__FILE__)."/pdoinit.php"));
require_once (realpath(dirname(__FILE__)."/SocketLogicCollection.php"));
require_once (realpath(dirname(__FILE__)."/LogRuleCollection.php"));
require_once (realpath(dirname(__FILE__)."/LogRuleCollectionEx.php"));
require_once (realpath(dirname(__FILE__)."/SensorDataDemo.php"));
require_once (realpath(dirname(__FILE__)."/EnoDevConfCollection.php"));
require_once (realpath(dirname(__FILE__)."/WebSwitchCollection.php"));
require_once (realpath(dirname(__FILE__)."/Rel8BoardCollection.php"));
require_once (realpath(dirname(__FILE__)."/Rel16BoardCollection.php"));
require_once (realpath(dirname(__FILE__)."/EdimaxSwitchCollection.php"));
require_once (realpath(dirname(__FILE__)."/LogicFieldCollection.php"));
require_once (realpath(dirname(__FILE__)."/IPCamCollection.php"));
require_once (realpath(dirname(__FILE__)."/SensorState.php"));
require_once (realpath(dirname(__FILE__)."/UserCollection.php"));
require_once (realpath(dirname(__FILE__)."/GroupUserCollection.php"));
require_once (realpath(dirname(__FILE__)."/LocationCollection.php"));
require_once (realpath(dirname(__FILE__)."/Input16BoardCollection.php"));
require_once (realpath(dirname(__FILE__)."/SensorCollection.php"));
//RoomCollections
require_once (realpath(dirname(__FILE__)."/EnoDevConfCollectionLocation.php"));
require_once (realpath(dirname(__FILE__)."/SensorCollectionLocation.php"));
require_once (realpath(dirname(__FILE__)."/WebSwitchCollectionLocation.php"));
require_once (realpath(dirname(__FILE__)."/DevTreeCollection.php"));

require_once (realpath(dirname(__FILE__)."/LocationCollectionRooms.php"));
require_once (realpath(dirname(__FILE__)."/QuickswitchCollectionRooms.php"));

require_once (realpath(dirname(__FILE__)."/ShareItemCollection.php"));
require_once (realpath(dirname(__FILE__)."/ShareRoomCollection.php"));
require_once (realpath(dirname(__FILE__)."/WebSwitchCollectionShare.php"));
require_once (realpath(dirname(__FILE__)."/ShareHashCollection.php"));
require_once (realpath(dirname(__FILE__)."/QuickSwitchCollectionShare.php"));
require_once (realpath(dirname(__FILE__)."/LogicFieldCollectionShare.php"));

require_once (realpath(dirname(__FILE__)."/ShareHashMailCollection.php"));
require_once (realpath(dirname(__FILE__)."/WebswitchImgCollection.php"));

require_once (realpath(dirname(__FILE__)."/DashboardconfigCollection.php"));
require_once (realpath(dirname(__FILE__)."/WebSwitchCollectionDash.php"));
require_once (realpath(dirname(__FILE__)."/CustomButtonCollection.php"));




abstract class GetSnipper {
	
	abstract function doopenTaskTrackerCollection($type);
	
}

class GetSnipperCollection extends GetSnipper {
	
	private $context = "openTaskTrackerCollection";
	
	function doopenTaskTrackerCollection($type) {
		
		$collection = NULL;//testsynchrinsation
		
		switch ($type) {
			
			case "SocketLogicCollection":
				$collection = new SocketLogicCollection;
				break;
			case "LogRuleCollection":
				$collection = new LogRuleCollection;
				break;
			case "LogRuleCollectionEx":
				$collection = new LogRuleCollectionEx;
				break;
			case "SensorDataDemo":
				$collection = new SensorDataDemo;
				break;
			case "EnoDevConfCollection":
				$collection = new EnoDevConfCollection;
				break;
			case "WebSwitchCollection":
				$collection = new WebSwitchCollection;
				break;
			case "Rel8BoardCollection":
				$collection = new Rel8BoardCollection;
				break;
			case "Rel16BoardCollection":
				$collection = new Rel16BoardCollection;
				break;
			case "Input16BoardCollection":
				$collection = new Input16BoardCollection;
				break;
			case "EdimaxSwitchCollection":
				$collection = new EdimaxSwitchCollection;
				break;
			case "LogicFieldCollection":
				$collection = new LogicFieldCollection;
				break;
			case "IPCamCollection":
				$collection = new IPCamCollection;
				break;
			case "SensorState":
				$collection = new SensorState;
				break;
			case "UserCollection":
				$collection = new UserCollection;
				break;
			case "GroupUserCollection":
				$collection = new GroupUserCollection;
				break;
			case "LocationCollection":
				$collection = new LocationCollection;
				break;
			case "SensorCollection":
				$collection = new SensorCollection;
				break;
			//RoomCollections
			case "EnoDevConfCollectionLocation":
				$collection = new EnoDevConfCollectionLocation;
				break;
			case "SensorCollectionLocation":
				$collection = new SensorCollectionLocation;
				break;
			case "WebSwitchCollectionLocation":
				$collection = new WebSwitchCollectionLocation;
				break;
			case "DeviceTreeCollection":
				$collection = new DeviceTreeCollection;
				break;
			//um Usercase und Gruppencase erweitern
			case "LocationCollectionRooms":
				$collection = new LocationCollectionRooms;
				break;
			case "QuickSwitchCollectionLocation":
				$collection = new QuickSwitchCollectionLocation;
				break;
			case "ShareItemCollection":
				$collection = new ShareItemCollection;
				break;
			case "ShareRoomCollection":
				$collection = new ShareRoomCollection;
				break;
			case "WebSwitchCollectionShare":
				$collection = new WebSwitchCollectionShare;
				break;
			case "ShareHashCollection":
				$collection = new ShareHashCollection;
				break;
			case "QuickSwitchCollectionShare":
				$collection = new QuickSwitchCollectionShare;
				break;
			case "LogicFieldCollectionShare":
				$collection = new LogicFieldCollectionShare;
				break;
			case "ShareHashMailCollection":
				$collection = new ShareHashMailCollection;
				break;
			case "WebswitchImgCollection":
				$collection = new WebswitchImgCollection;
				break;
            case "DashboardconfigCollection":
                $collection = new DashboardconfigCollection;
                break;
            case "WebSwitchCollectionDash":
                $collection = new WebSwitchCollectionDash;
                break;
            case "CustomButtonCollection":
                $collection = new CustomButtonCollection;
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


