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
$sno= $data->sno;
$response = array();
try{
if(!empty($accesskey) &&!empty($sno)){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
	$stmt1=$pdoread->prepare("INSERT INTO `indent_queue`( `indent_no`, `indent_sno`, `branch`, `item_category`, `item_group`, `item_subgroup`, `department`, `stcokpoint`, `itemcode`, `itemname`, `qty`, `priority`, `status`, `created_on`, `createdby`,  `ref_itemno`, `stock_status`) SELECT `indent_no`,`indent_sno`,  `branch`, `item_category`, `item_group`, `item_subgroup`, `department`, `stcokpoint`, `itemcode`, `itemname`, `qty`, `priority`, `status`, CURRENT_TIMESTAMP,:userid,`ref_itemno`, `stock_status` FROM `indent_queue` WHERE `sno`=:sno");
$stmt1->bindParam(':sno', $sno, PDO::PARAM_STR);
$stmt1->bindParam(':userid', $emp['userid'], PDO::PARAM_STR);
$stmt1-> execute();
	  if($stmt1 -> rowCount() > 0){
			//indent not approved
	   http_response_code(200);
          $response['error']= false;
	     $response['message']="Data inserted";
	  }else{
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Not Inserted";
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

}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
    }
	echo json_encode($response);
   unset($pdoread);
?>
	
	
	
	
	