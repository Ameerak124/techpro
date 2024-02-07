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
$ip_no=trim($data->ip_no);
$umr_no=trim($data->umr_no);
$medicine_name=trim($data->medicine_name);
$medicine_code=trim($data->medicine_code);
/* $dosage_date=trim($data->dosage_date); */
$dosage_date = date('Y-m-d', strtotime($data->dosage_date));
$dosage_time=trim($data->dosage_time);
$remarks=trim($data->remarks);

try {
     if(!empty($accesskey) && !empty($ip_no) && !empty($umr_no) && !empty($medicine_name) && !empty($medicine_code) && !empty($dosage_date) && !empty($dosage_time)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//insert data
$insert_data=$pdo4->prepare("INSERT INTO `drug_medication`(`sno`, `ip_no`, `umr_no`, `medicine_name`, `medicine_code`, `dosage_date`, `dosage_time`, `remarks`, `created_by`, `created_on`, `modified_by`, `modified_on`, `status`) VALUES (NULL,:ip_no,:umr_no,:medicine_name,:medicine_code,:dosage_date,:dosage_time,:remarks,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active')");
$insert_data->bindParam(':ip_no', $ip_no, PDO::PARAM_STR);
$insert_data->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
$insert_data->bindParam(':medicine_name', $medicine_name, PDO::PARAM_STR);
$insert_data->bindParam(':medicine_code', $medicine_code, PDO::PARAM_STR);
$insert_data->bindParam(':dosage_time', $dosage_time, PDO::PARAM_STR);
$insert_data->bindParam(':dosage_date', $dosage_date, PDO::PARAM_STR);
$insert_data->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$insert_data->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$insert_data-> execute();
if($insert_data->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Saved Successfully";
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Something Went Wrong";
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
	$response['message']= "Connection failed" .$e->getmessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>