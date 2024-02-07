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
$id=trim($data->id);
	
$response = array();
try{

if(!empty($accesskey)&& !empty($id)){
$validate = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();

if($validate -> rowCount() > 0){
	$validres = $validate->fetch(PDO::FETCH_ASSOC);
	   $check2 = $pdo4->prepare("UPDATE `doctor_service_suggestion` SET `modified_by`=:userid,`modified_on`=CURRENT_TIMESTAMP,`status`='Inactive' WHERE `status`='Active' AND `sno`=:id AND `cost_center`=:branch");
	$check2->bindParam(':userid', $validres['userid'], PDO::PARAM_STR);
	$check2->bindParam(':id', $id, PDO::PARAM_STR);
	$check2->bindParam(':branch', $validres['cost_center'], PDO::PARAM_STR);
	
	$check2 -> execute();
  if($check2->rowCOunt()>0){
  http_response_code(200);
	$response['error'] = false;
	$response['message']= "Data Removed Sucessfully";
}else{
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Sorry! Please try Again";
}

}	
	else{
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
$pdo4 = null;
$pdoread = null;
?>