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
$data=json_decode(file_get_contents("php://input"));
$response = array();
$accesskey=trim($data->accesskey);
$searchterm=$data->searchterm;

$ipaddress=$_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
     if(!empty($accesskey)&& !empty($searchterm)){    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$frequency=$pdoread->prepare("SELECT `route` FROM `doctor_mediciation` WHERE `route` LIKE :search AND `vstatus`='Active' GROUP BY `route` ");
$frequency -> bindValue(":search", "%{$searchterm}%", PDO::PARAM_STR);
$frequency->execute();
if($frequency->rowCount() > 0){
	http_response_code(200);
    $response['error'] = false;
	$response['message']= "Data Found";
	while($res=$frequency->fetch(PDO::FETCH_ASSOC)){
		$response['searchroute'][]=$res;
	}
}else{
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "No Data Found";
}

}else {	
http_response_code(503);
    $response['error'] = true;
	$response['message']='Access denied!';
}
}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "pdonection failed";
	$errorlog = $pdoread -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
echo json_encode($response);
$pdoread = null;
?>