<?php 
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey=trim($data->accesskey);
$check_status=trim($data->check_status);
$transid=trim($data->transid);


$ipaddress=$_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
     if(!empty($accesskey)&& !empty($transid)&& !empty($transid)){    

$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$update_check_in=$pdo4->prepare("UPDATE `patient_details` SET `walkin_status`=:check_status,`doc_suggestion` = :userid,`doc_suggestion_on` = CURRENT_TIMESTAMP,`modifiedon` = CURRENT_TIMESTAMP,`modifiedby` = :userid WHERE `transid`=:transid AND `slot_status`='booked' ");
$update_check_in->bindParam(':check_status', $check_status, PDO::PARAM_STR);
$update_check_in->bindParam(':transid', $transid, PDO::PARAM_STR);
$update_check_in->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update_check_in->execute();
if($update_check_in->rowCount() > 0){
	$response['error']=false;
	$response['message']=$check_status." Successfully";
}else{
	$response['error']=true;
	$response['message']='Please Try Again';
}

}else {	
    $response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
}
}else {	
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
	$errorlog = $pdoread -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>