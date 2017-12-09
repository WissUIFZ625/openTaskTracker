<?php
require_once (realpath(dirname(__FILE__)."/../include/basesnippet.php"));


if (isset($_POST['target_id']) ) {

	$type = $_POST['target_id'];
	$filter = $_POST['filter_str'];
	

}else{
	return;
}

$collection = new GetSnipperCollection;

$snippet = $collection->doPractiframeCollection($type);
$snippet->buildContent($filter);
$output = $snippet->getJson();
echo $output;
//var_dump($output);