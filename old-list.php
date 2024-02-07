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
$accesskey =trim($data->accesskey);
$searchterm = trim($data->searchterm);
try{
if(!empty($accesskey) && !empty($searchterm)){
$check = $pdoread-> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();

if($check->rowCount() > 0){
if($searchterm != ''){
    $searchQuery = $pdoread->prepare("SELECT `sno`,`category`,`subcategory`,`servicecode`,`services`,`hsn_sac`,`quantity`,`rate`,`total` FROM `billing_history` WHERE `ipno` = :searchterm AND `credit_debit` LIKE 'CREDIT' AND `status` = 'Visible' ");
$searchQuery->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
$searchQuery->execute(); 
 
}
 $stmt = $pdoread->prepare("SELECT COUNT(`category`) AS allcount FROM `billing_history`  WHERE `ipno` = :searchterm AND `credit_debit` LIKE 'CREDIT' AND `status` = 'Visible' ");
 $stmt->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
$stmt->execute();
$records = $stmt->fetch(PDO::FETCH_ASSOC);
$totalRecords = $records['allcount']; 
if($searchQuery -> rowCount() > 0){
$records1 = $searchQuery->fetchAll(PDO::FETCH_ASSOC);
http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	$response['allcount']=$totalRecords ;
	$response['finalsummaryoldlist'] = $records1 ;
}
else{
		http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";	
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
}catch(PDOException $e){
     echo $e -> getMessage();
}

echo json_encode($response);
$pdoread = null;
?>