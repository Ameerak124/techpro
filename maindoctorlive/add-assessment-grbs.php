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
$grbs=trim($data->grbs);
$route=trim($data->route);
$insuline_type=trim($data->insuline_type);
$insuline_dose=trim($data->insuline_dose);
$adv_bydoctor=strtoupper($data->adv_bydoctor);
$other_insuline_type=($data->other_insuline_type);
$doc_uid=trim($data->doc_uid);
$umrno=trim($data->umrno);
$ipno=trim($data->ipno);
/* $grbs_date_time=trim($data->grbs_date_time); */
$remarks=str_ireplace("'","",$data->remarks);
$grbs_date_time = date('Y-m-d H:i:s', strtotime($data->grbs_date_time));
try {
     if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//check if details exist 
/*
$check_details=$con->prepare("SELECT `umrno` FROM `doctor_assessment_grbs` WHERE `status`='Active' AND `ipno`=:ipno AND `umrno`=:umrno AND `doc_uid`=:doc_uid AND `cost_center`=:branch ");
$check_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$check_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$check_details->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$check_details->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$check_details->execute();

if($check_details->rowCount () > 0){
    
	//if data exists update the data on umr
$update_details=$con->prepare("UPDATE `doctor_assessment_grbs` SET `grbs_rate`=:grbs_rate,`route`=:route ,`insulin_type`=:insulin_type ,`other_insulin_type`=:other_insulin_type ,`insulin_dose`=:insulin_dose,`referred_doctor`=:referred_doctor,`modified_on`=CURRENT_TIMESTAMP,`modified_by`=:userid WHERE `status`='Active' AND `cost_center`=:branch AND `umrno`=:umrno AND `ipno`=:ipno AND `doc_uid`=:doc_uid");
$update_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$update_details->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$update_details->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
$update_details->bindParam(':grbs_rate', $grbs_rate, PDO::PARAM_STR);
$update_details->bindParam(':route', $route, PDO::PARAM_STR);
$update_details->bindParam(':insulin_type', $insulin_type, PDO::PARAM_STR);
$update_details->bindParam(':other_insulin_type', $other_insulin_type, PDO::PARAM_STR);
$update_details->bindParam(':insulin_dose', $insulin_dose, PDO::PARAM_STR);
$update_details->bindParam(':referred_doctor', $referred_doctor, PDO::PARAM_STR);
$update_details->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$update_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$update_details->execute();
if($update_details->rowCount()>0){
	$response['error']=false;
	$response['message']="Data Updated Sucessfully";

}else{
	$response['error']=true;
	$response['message']="Please Try Again";
}
//if data is not there go on inserting
// }else{
*/
 //check if patient discharged or not
 $validate = $pdoread->prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
 $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
 $validate -> execute();
 $validates = $validate->fetch(PDO::FETCH_ASSOC);
 if($validate -> rowCount() > 0){

//insertion of data start
$adddata=$pdo4-> prepare ("INSERT INTO `doctor_assessment_grbs`(`sno`, `ipno`, `umrno`, `doc_uid`, `grbs_rate`, `route`, `insulin_type`, `other_insulin_type`,`insulin_dose`, `referred_doctor`, `created_on`, `created_by`, `modified_on`, `modified_by`, `cost_center`, `status`,`comments`,`grbs_date_time`) VALUES (NULL,:ipnum,:umrnum,:docuid,:grbsrate,:routes,:insulintype,:other_insulin_type,:insulindose,:referreddoctor,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:branch,'Active',:remarks,:grbs_date_time)");	
$adddata->bindParam(':ipnum', $ipno, PDO::PARAM_STR);
$adddata->bindParam(':umrnum', $umrno, PDO::PARAM_STR);
$adddata->bindParam(':docuid', $doc_uid, PDO::PARAM_STR);
$adddata->bindParam(':grbsrate', $grbs, PDO::PARAM_STR);
$adddata->bindParam(':routes', $route, PDO::PARAM_STR);
$adddata->bindParam(':insulintype', $insuline_type, PDO::PARAM_STR);
$adddata->bindParam(':other_insulin_type', $other_insuline_type, PDO::PARAM_STR);
$adddata->bindParam(':insulindose', $insuline_dose, PDO::PARAM_STR);
$adddata->bindParam(':referreddoctor', $adv_bydoctor, PDO::PARAM_STR);
$adddata->bindParam(':grbs_date_time', $grbs_date_time, PDO::PARAM_STR);
$adddata->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$adddata->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$adddata->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
$adddata->execute();
if($adddata->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Inserted Sucessfully";
    // $response['DETAILS']=$fetch['umrno'];

}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Please Try Again";
}
// }
}else{
	http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
}
}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
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