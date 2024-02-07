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
$accesskey= $data->accesskey;
$lang= $data->lang;
$response = array();
try{
if(!empty($accesskey)){
	$check = $pdo -> prepare("SELECT `userid`,`department`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	
	$stmt2=$pdo->prepare("SELECT `title`, `consent_id`,case when :lang='English' then `content_eng` when :lang='Telugu' then `content_tel` when :lang='Hindi' then `content_hin`else ''end as contenttext, `patient_sign_one`, `patient_sign_two`, `emp_sign_one`, `emp_sign_two`, `photo`, `questionnaires`, `questions_id`, `questions_type`, `created_by`, `created_on`, `status` FROM `consent_form_master` WHERE `status`='1'");
	$stmt2->bindParam(':lang', $lang, PDO::PARAM_STR);
	$stmt2 -> execute(); 
	//,case when :lang='English' then `content_eng` when :lang='Telugu' then `content_tel` when :lang='Hindi' then `content_hin`else ''end as contenttext
	if($stmt2 -> rowCount() > 0){
	$data1 = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
	
	    http_response_code(200);
        $response['error']= false;
	    $response['message']="Data Found";
	    $response['consentlist']=$data1;
	} else
     {
	http_response_code(503);
    $response['error']= true;
  	$response['message']="No Data Found!";
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
} 
catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdo = null;
?>