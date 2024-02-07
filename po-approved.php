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
$accesskey = $data->accesskey;
$posno = $data->posno;
$status = $data->status;
try {
if(!empty($accesskey)&&!empty($posno)&&!empty($status)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
$stmt = $pdoread -> prepare("SELECT  COALESCE(SUM(`base_total`),0) AS total FROM `po_item` INNER JOIN po_generate ON po_item.po_no=po_generate.po_number WHERE `po_generate`.`sno`=:posno");
$stmt->bindParam(':posno', $posno , PDO::PARAM_STR);
$stmt -> execute();
if($status=='approved'){
	$stmt1=$pdo4->prepare("UPDATE `po_generate` SET `po_total`=(SELECT  COALESCE(SUM(`base_total`),0) AS total FROM `po_item`),`po_status`='approved',`is_approved`='1',`approved_on`=CURRENT_TIMESTAMP,`approved_by`=:userid WHERE `po_status`='pending'");
$stmt1->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$stmt1 -> execute();
}
else{
$stmt1=$pdo4->prepare("UPDATE `po_generate` SET `po_status`='cancelled',`modifiedby`=:userid,`modified_on`=CURRENT_TIMESTAMP WHERE `po_status`='pending'");
$stmt1->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);	
$stmt1 -> execute();
}
if($stmt1 -> rowCount()>0){
		$result1 = $stmt->fetch(PDO::FETCH_ASSOC);
		 http_response_code(200);
         $response['error']= false;
		 $response['message']="Data found";
		 $response['po-approvedlist'][]=$result1;
	   
     }else
     {
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
}catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " ;
	$e;
	}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>