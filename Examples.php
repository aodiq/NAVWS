<?
ob_start();
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="is-IS">

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>NAV Web Service Examples via PHP</title>
</head>
<body>

<?php
//To Communicate With the Web Service Using NTLM You Must Override the HTTP with NTLMSteam to allow Windows Authentication to work.
//See https://thomas.rabaix.net/blog/2008/03/using-soap-php-with-ntlm-authentication for full Explanation
require_once("ntlm/NTLMStream.php");
require_once("ntlm/NTLMSoapClient.php");
stream_wrapper_unregister('http');
stream_wrapper_register('http', 'NTLMStream') or die("Failed to register protocol");


//Ensure you can get a list of services by entering the Services URL in a Web Browser - No Point Continuing until you verify that The Web Services is running.
$servicesURL = 'http://erp-dep-01.ondemand.in.th:8083/DynamicsNAV100/WS/Item';


// Initialize Soap Client URL
$baseURL = 'http://erp-dep-01.ondemand.in.th:8083/DynamicsNAV100/WS/'; 

//Define Company Name - This value will need to be urlencoded 
$CompanyName = "Demo"; 

//>>>>>>>>>>>>>>>>>Item Query>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
$pageURL = $baseURL.rawurlencode($CompanyName).'/Page/Item'; 
echo "<br>URL of Item page: $pageURL<br><br>";

// Read Single Record
try{
$service = new NTLMSoapClient($pageURL);
$params = array('No' => '*1*'); 
$result = $service->Read($params); 
$item = $result->Item; 
echo "Read Single Record Result: Item Filter No = *1*:" . $item->No . "&nbsp;" . $item->Description."<br><br>";
}
catch (Exception $e) {
	echo "<hr><b>ERROR: SoapException:</b> [".$e."]<hr>";
	echo "<pre>".htmlentities(print_r($service->__getLastRequest(),1))."</pre>";

}

//Read Multiple Records
try{
	 $params = array('filter' => array( array('Field' => 'No', 
											   'Criteria' => '*1*')
									   ), 
					 'setSize' => 40); //setSize =0 will return all rows - Can cause performance issue with large results set!
	 $result = $service->ReadMultiple($params); 
	 $resultSet = $result->ReadMultiple_Result->Item;

	 echo "Read Multiple Records Result: First 20 Items that Contain *1* :<br>";
	if (is_array($resultSet)) { 
		foreach($resultSet as $rec) { 
			echo $rec->No . "&nbsp;" . $rec->Description."<br>"; 
			} 
		} 
		else { 
			echo $resultSet->No . "&nbsp;" . $resultSet->Description."<br>"; 
		}
	}
catch (Exception $e) {
	echo "<hr><b>ERROR: SoapException:</b> [".$e."]<hr>";
	echo "<pre>".htmlentities(print_r($service->__getLastRequest(),1))."</pre>";
}

 
//<<<<<<<<<<<<<<<<<Item Query<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<






 
// Put back the HTTP protocal to esure we do not affect other operations.
stream_wrapper_restore('http');




//Display All Variables
//$a = print_r(var_dump($GLOBALS),1);
//echo htmlspecialchars($a);

?>
</body>
</html>



