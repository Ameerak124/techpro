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
$accesskey = trim($data->accesskey);
$ipno = trim($data->ipno);
$searchterm = trim($data->searchterm);
try{
if(!empty($accesskey) && !empty($searchterm) && !empty($ipno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$stmt = $pdoread->prepare("SELECT COUNT(`category`) AS allcount FROM `billing_history` WHERE `ipno`=:ipno AND `credit_debit` LIKE 'CREDIT' AND `status` = 'Visible'");
$stmt->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$stmt->execute();
$records = $stmt->fetch();
$totalRecords = $records['allcount'];
## Fetch records
$stmt1 = $pdoread->prepare("SELECT @a:=@a+1 AS sno,`sno` AS track,`category`,`subcategory`,`servicecode`,`services`,`hsn_sac`,`quantity`,`rate`,`total`,DATE_FORMAT(`createdon`,'%d-%b-%Y')AS cdate FROM (SELECT @a:=0) AS a,`billing_history` WHERE `ipno`=:ipno and (servicecode like :searchterm || services like :searchterm) AND `credit_debit` LIKE 'CREDIT' AND `status` = 'Visible' order by createdon desc");
$stmt1->bindParam(':ipno',$ipno, PDO::PARAM_STR);
$stmt1->bindValue(':searchterm',"%{$searchterm}%", PDO::PARAM_STR);
$stmt1->execute();
if($stmt1->rowCount()>0){
$result=$stmt1->fetchAll(PDO::FETCH_ASSOC);
http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
	$response['finalsummarylist']= $result;	
}else{
		http_response_code(503);
	$response['error']=true;
	$response['message']="No data found";	
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	http_response_code(400);
	$response['error']=true;
	$response['message']="Sorry! some details are missing";
}
echo json_encode($response);
}
catch(PDOException $err){
     echo $err -> getMessage();
}
$pdoread = null;
?>