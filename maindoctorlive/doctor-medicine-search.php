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
$productsearch = trim(strtoupper($data->productsearch));

try {

if(!empty($accesskey) && !empty($productsearch)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//Check Customer 
//$search = $con -> prepare("SELECT `sno` AS cp_sno,`itemname`,`batchcode`,`expiredon`,SUM(`on_hand`) AS availableqty FROM `op_pharmacy_inventory` WHERE `itemname` LIKE :search AND`status` = 'Visible' GROUP BY `itemcode`,`batchcode`");
//SELECT `cp_sno`,`itemcode`, `itemname`,`on_hand`AS qty, if(SUM(`on_hand`) > 0,'#b8e7ba','#ffd4d4') AS colorcode,if(SUM(`on_hand`) > 0,'In Stock','Out of Stock') AS stockstatus FROM `op_pharmacy_inventory`  WHERE `itemname`LIKE '%04%' AND `status`='Visible'
$search = $pdoread -> prepare("SELECT IFNULL(`itemcode`,'')AS cp_sno,IFNULL(`itemcode`,'')AS itemcode, IFNULL(`itemname`,'')AS itemname  FROM `stockpoint_inventory`  WHERE `itemname`LIKE :productsearch  LIMIT 5");
$search->bindValue(":productsearch", "%{$productsearch}%", PDO::PARAM_STR);
$search->execute();
if($search -> rowCount() > 0){
	http_response_code(200);
$response['error']= false;
$response['message']= "Data found";
while($result = $search->fetch(PDO::FETCH_ASSOC)){
		$response['doctormedicinesearchlist'][] = $result;
	}
}else{
	http_response_code(503);
	$response['error']= true;
$response['message']= "No such item found";
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>