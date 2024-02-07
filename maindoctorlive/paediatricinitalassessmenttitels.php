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
		
		  
		
		/* $my_array = array("Admission Mode","Information Obtained From","Child Accompanied by","Babies (Below 3yr)","Language Spoken","School Grade","Favorite Toy/Hobbies"); */
		$my_array = array("Ambulatory","Wheel chair","Stretcher","Cuddled","Other");
		$my_array1 = array("Patient","Family/Friend","Other");
		$my_array2 = array("Parents","Guardian","Stretcher","Cuddled","Other");
		$my_array3 = array("Ambulatory","Wheel chair","Stretcher","Cuddled","Other");
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
/*    for ($i = 0; $i < count($flatArray); $i += $elementsPerSubarray) {
    $subarrayList[] = array_slice($flatArray, $i, $elementsPerSubarray);
}
 */
	for($x = 0; $x < sizeof($my_array); $x++){	
	$response['radiotitle'][$x]['admissionmode']=$my_array[$x];	
     }	
   $response['healthhistory']= "Information Obtained From";
    $response['miscellaneous']= "Child Accompanied by";
    $response['nighttomorning']= "Babies (Below 3yr)";
    $response['nighttomorning']= "School Grade";
    $response['nighttomorning']= "Favorite Toy/Hobbies";	 
     
     
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