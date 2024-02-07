<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$response = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);

		$my_array = array(">400","<400 221-301","<300 142-220","<200 67-141","<100 <67");
		$my_array1 = array(">150","<150","<100","<50","<20");
		$my_array2 = array("<1.2","1.2-1.9","2.0-5.9","6.0-11.9",">12.0");
		$my_array3 = array("No hypotension","MAP <70","Dopamine </=5 or dobutamine(any)","Dopamine>5 or norepinephrine </=0.1","Dopamine > 15 or norepinephrine >0.1");
	    $my_array4 = array("15","13-14","10-12","6-9","<6");
		$my_array5 = array("<1.2","1.2-1.9","2.0-3.4","3.5-4.9 or <500",">5.0 or <200");
        
		
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
	for($x = 0; $x < sizeof($my_array); $x++){	
	$response['respirational_list'][$x]['score']=$my_array[$x];	
     }
    for($x = 0; $x < sizeof($my_array1); $x++){	
	$response['coagulation_list'][$x]['score']=$my_array1[$x];	
     }
for($x = 0; $x < sizeof($my_array2); $x++){	
	$response['liver_billirubin_list'][$x]['score']=$my_array2[$x];	
     }
for($x = 0; $x < sizeof($my_array3); $x++){	
	$response['cardiovascular_list'][$x]['score']=$my_array3[$x];	
     }
for($x = 0; $x < sizeof($my_array4); $x++){	
	$response['cns_glasgow_list'][$x]['score']=$my_array4[$x];	
     }
for($x = 0; $x < sizeof($my_array5); $x++){	
	$response['renal_createinine_list'][$x]['score']=$my_array5[$x];	
     }	 
		  
     
     
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
   }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
}
 
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
     
     //"Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>