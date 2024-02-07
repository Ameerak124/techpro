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
$accesskey = $data->accesskey;
$pname = $data->pname;
$umrno = $data->umrno;
$ipno = $data->ipno;
$grbs = $data->grbs;
$route = $data->route;
$insuline_type = $data->insuline_type;
$insuline_dose = $data->insuline_dose;
$adv_bydoctor = $data->adv_bydoctor;
$response = array();
try {	
if(!empty($accesskey) &&!empty($pname) &&!empty($umrno) &&!empty($ipno) &&!empty($grbs) &&!empty($route) &&!empty($insuline_type) &&!empty($insuline_dose) &&!empty($adv_bydoctor)){ 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$userid = $result['userid'];
if($check -> rowCount() > 0){
$stmt=$pdo4->prepare("INSERT INTO `grbs`(`pname`, `umrno`, `ipno`,`grbs`,`route`,`insuline_type`,`insuline_dose`,`adv_bydoctor`,`created_by`, `created_on`) VALUES (:pname,:umrno,:ipno,:grbs,:route,:insuline_type,:insuline_dose,:adv_bydoctor,:userid,CURRENT_TIMESTAMP)");
$stmt -> bindParam(":pname", $pname, PDO::PARAM_STR);
$stmt -> bindParam(":umrno", $umrno, PDO::PARAM_STR);
$stmt -> bindParam(":ipno", $ipno, PDO::PARAM_STR);
$stmt -> bindParam(":grbs", $grbs, PDO::PARAM_STR);
$stmt -> bindParam(":route", $route, PDO::PARAM_STR);
$stmt -> bindParam(":insuline_type", $insuline_type, PDO::PARAM_STR);
$stmt -> bindParam(":insuline_dose", $insuline_dose, PDO::PARAM_STR);
$stmt -> bindParam(":adv_bydoctor", $adv_bydoctor, PDO::PARAM_STR);
$stmt -> bindParam(":userid", $userid, PDO::PARAM_STR);
$stmt -> execute();
if($stmt -> rowCount() > 0){
		 http_response_code(200);  
		 $response['error']= false;
		 $response['message']="Data saved successfully";
	      
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Data not saved";
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
 }catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdo4);
   unset($pdoread);
?>
