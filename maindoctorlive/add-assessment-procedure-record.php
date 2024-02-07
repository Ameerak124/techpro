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
$procedure_details=($data->procedure_details);
$procedure_name=($data->procedure_name);
$indication=($data->indication);
$procedure_notes=($data->procedure_notes);
$pre_procedure_complications=($data->pre_procedure_complications);
$post_procedure_complications=($data->post_procedure_complications);
$doc_uid=trim($data->doc_uid);
$umrno=trim($data->umrno);
$ipno=trim($data->ipno);
try {
     if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//check if details exist 
$check_details=$pdoread->prepare("SELECT  `umrno` FROM  `doctor_assessment_procedure_record` WHERE `status`='Active' AND `cost_center`=:branch AND `ipno`=:ipno AND `umrno`=:umrno AND `doc_uid`=:doc_uid ");
$check_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$check_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$check_details->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$check_details->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$check_details->execute();
if($check_details->rowCount () > 0){
//if data exists update the data on umr
$update_details=$pdo4->prepare("UPDATE `doctor_assessment_procedure_record` SET `procedure_details`=:procedure_details,`procedure_name`=:procedure_name,`indication`=:indication,`procedure_notes`=:procedure_notes,`pre_procedure_complications`=:pre_procedure_complications,`post_procedure_complications`=:post_procedure_complications,`modified_on`=CURRENT_TIMESTAMP,`modified_by`=:userid WHERE `ipno`=:ipno AND `umrno`=:umrno AND `doc_uid`=:doc_uid AND `cost_center`=:branch AND `status`='Active'");
$update_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$update_details->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$update_details->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$update_details->bindParam(':procedure_details', $procedure_details, PDO::PARAM_STR);
$update_details->bindParam(':procedure_name', $procedure_name, PDO::PARAM_STR);
$update_details->bindParam(':indication', $indication, PDO::PARAM_STR);
$update_details->bindParam(':procedure_notes', $procedure_notes, PDO::PARAM_STR);
$update_details->bindParam(':pre_procedure_complications', $pre_procedure_complications, PDO::PARAM_STR);
$update_details->bindParam(':post_procedure_complications', $post_procedure_complications, PDO::PARAM_STR);
$update_details->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$update_details->execute();
if($update_details->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Updated Sucessfully";
}else{
	http_response_code(200);
	$response['error']=true;
	$response['message']="Please Try Again";
}
//if data is not there go on inserting
}else{
//insertion of data start
$adddata=$pdo4-> prepare ("INSERT IGNORE INTO `doctor_assessment_procedure_record`(`sno`, `ipno`, `umrno`, `doc_uid`, `procedure_details`, `procedure_name`, `indication`, `procedure_notes`, `pre_procedure_complications`, `post_procedure_complications`, `created_on`, `created_by`, `modified_on`, `modified_by`, `status`, `cost_center`) VALUES (NULL,:ipno,:umrno,:doc_uid,:procedure_details,:procedure_name,:indication,:procedure_notes,:pre_procedure_complications,:post_procedure_complications,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,'Active',:branch)");	
$adddata->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$adddata->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$adddata->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$adddata->bindParam(':procedure_details', $procedure_details, PDO::PARAM_STR);
$adddata->bindParam(':procedure_name', $procedure_name, PDO::PARAM_STR);
$adddata->bindParam(':indication', $indication, PDO::PARAM_STR);
$adddata->bindParam(':procedure_notes', $procedure_notes, PDO::PARAM_STR);
$adddata->bindParam(':pre_procedure_complications', $pre_procedure_complications, PDO::PARAM_STR);
$adddata->bindParam(':post_procedure_complications', $post_procedure_complications, PDO::PARAM_STR);
$adddata->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$adddata->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$adddata->execute();
if($adddata->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Inserted Sucessfully";
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Please Try Again";
}
}
}else {
http_response_code(400);	
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {
http_response_code(400);	
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
	
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>