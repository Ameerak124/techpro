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
$response = array();
try {	
if(!empty($accesskey) &&!empty($pname)){ 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$userid = $result['userid'];
if($check -> rowCount() > 0){
$stmt=$pdoread->prepare("SELECT `doctorname`,`doctorid`, `ref_doctorid`, `ref_doctorname`, `priority`,`pk_clinicalproblems`,`reason_refferal`,`reffered_for`,DATE_FORMAT(`created_on`,'%d-%b-%Y') as created_on, `created_by`,`status` FROM `cross_consultation` where `pname`=:pname and`doctorid`=:doctorid order by `created_on` desc");
$stmt -> bindParam(":pname", $pname, PDO::PARAM_STR);
$stmt -> bindParam(":doctorid", $userid, PDO::PARAM_STR);
$stmt -> execute();
if($stmt -> rowCount() > 0){
	 $data = $stmt -> fetchAll(PDO::FETCH_ASSOC);
		 http_response_code(200);  
		 $response['error']= false;
		 $response['message']="Data Found";
	     $response['crossconsultationlist']=$data;
     }
	 else
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
 }catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
	echo json_encode($response);
   unset($pdoread);
?>