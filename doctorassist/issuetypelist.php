<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey=trim($data->accesskey);
//$patient_id=trim($data->patient_id);

	
$response = array();
try{

 if(!empty($accesskey)){
$validate = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();

if($validate -> rowCount() > 0){
	  
 
    $check2 = $pdoread->prepare("SELECT `type` FROM `category_wise_issue_list` WHERE status='0' GROUP BY type");
	//$check2->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
	  //$check2->bindParam(':status', $status, PDO::PARAM_STR);
	
	$check2 -> execute();
    if($check2->rowCount() > 0){
		http_response_code(200);
		$response['error'] = false;
				$response['message']="Data found";
			

	while($check2list = $check2->fetch(PDO::FETCH_ASSOC)){
		
		
		$response['issuetypelist'][] = [
					'type'=>$check2list['type'],
					
					
				];
	}
}else{
	http_response_code(503);
      $response['error'] = true;
				$response['message']="No data found";
}
    	}else{
			http_response_code(400);
					     $response['error'] = true;
							$response['message']="Access denied!";
					}  
}else{
	          http_response_code(400);
				$response['error'] = true;
				$response['message'] ="Sorry! Some details are missing";
					}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdoread = null;
	?>