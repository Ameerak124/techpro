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
$accesskey= $data-> accesskey;
$active = "Active";
$one = "1";
$response = array();
try{
if(!empty($accesskey)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
     $empdata = $check -> fetch(PDO::FETCH_ASSOC);
     $empname = $empdata['userid'];
      $stmt = $pdoread -> prepare("SELECT`userid`, `username`, `department`, `sp_code`, `branch`, IFNULL(`stock_point`.`stock_point`,'NA') AS stock_point FROM `user_logins` LEFT JOIN `stock_point` ON `stock_point`.`stockpoint_code` = `user_logins`.`sp_code` WHERE `stock_point`.`is_active` = :one AND `user_logins`.`status` = :active AND `userid` = :userid LIMIT 1");
	 $stmt->bindParam(':userid', $empname, PDO::PARAM_STR);
	 $stmt->bindParam(':active', $active, PDO::PARAM_STR);
	 $stmt->bindParam(':one', $one, PDO::PARAM_STR);
      $stmt -> execute(); 
     if($stmt -> rowCount() > 0){
          $data = $stmt -> fetch(PDO::FETCH_ASSOC);
		  http_response_code(200);
          $response['error']= false;
	     $response['message']="Data Found";
          $response['empdetails']= $data;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
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
   unset($pdoread);
?>

	 