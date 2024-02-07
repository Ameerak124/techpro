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
$response = array();
$accesskey=trim($data->accesskey);
$doc_uid=trim($data->doc_uid);
$umrno=trim($data->umrno);
$ipno=trim($data->ipno);
// $ipaddress=$_SERVER['REMOTE_ADDR'];
// $apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
// $mybrowser = get_browser(null, true);
try {
	if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)){ 
// $con = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// // set the PDO error mode to exception
// $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$procedure_fetch=$pdoread->prepare("SELECT  `procedure_details`, `procedure_name`, `indication`, `procedure_notes`, `pre_procedure_complications`, `post_procedure_complications` FROM `doctor_assessment_procedure_record` WHERE  `status`='Active' AND `umrno`=:umrno AND `ipno`=:ipno AND `doc_uid`=:doc_uid AND `cost_center`=:branch ORDER BY `modified_on` DESC");
$procedure_fetch->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$procedure_fetch->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$procedure_fetch->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$procedure_fetch->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$procedure_fetch->execute();
if($procedure_fetch->rowCount() > 0){
	$response['error']=false;
	$response['message']="Data Found";
	$get_details=$procedure_fetch->fetch(PDO::FETCH_ASSOC);
	$response['procedure_details']=$get_details['procedure_details'];
	$response['procedure_name']=$get_details['procedure_name'];
	$response['indication']=$get_details['indication'];
	$response['procedure_notes']=$get_details['procedure_notes'];
	$response['pre_procedure_complications']=$get_details['pre_procedure_complications'];
	$response['post_procedure_complications']=$get_details['post_procedure_complications'];
}else{
	http_response_code(200);
	$response['error']=true;
	$response['message']="No Data Found";
}

}else {	
	http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
}
}else {	
	http_response_code(503);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	
}
echo json_encode($response);
$pdoread = null;
?>