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
$fdate=$data->fdate;
$tdate=$data->tdate;
$delete = 'delete';
$response = array();
try{
if(!empty($accesskey)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
if(empty($fdate) && empty($tdate)){
$stmt2=$pdoread->prepare("SELECT `voucher_id`, `amount`, `branch`, `expense_cat_master`.`expense_category`,`expense_type_master`.`expense_type` AS expensetype,`expense_details`, DATE(`cashvoucher`.`created_on`) AS raisedon, `cashvoucher`.`created_by`, `status` FROM `cashvoucher` INNER JOIN `expense_type_master` ON `expense_type_master`.`sno` = `cashvoucher`.`expense_type_ref` INNER JOIN `expense_cat_master` ON `expense_cat_master`.`sno` = `cashvoucher`.`expense_cat_ref` WHERE `status` != 'delete' GROUP BY `cashvoucher`.`sno`");
}
else{
$stmt2=$pdoread->prepare("SELECT `voucher_id`, `amount`, `branch`, `expense_cat_master`.`expense_category`,`expense_type_master`.`expense_type` AS expensetype,`expense_details`, DATE(`cashvoucher`.`created_on`) AS raisedon, `cashvoucher`.`created_by`, `status` FROM `cashvoucher` INNER JOIN `expense_type_master` ON `expense_type_master`.`sno` = `cashvoucher`.`expense_type_ref` INNER JOIN `expense_cat_master` ON `expense_cat_master`.`sno` = `cashvoucher`.`expense_cat_ref` WHERE `status` != :delete AND DATE(cashvoucher.created_on) BETWEEN :fdate AND :tdate GROUP BY `cashvoucher`.`sno`");
$stmt2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$stmt2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
$stmt2->bindParam(':delete', $delete, PDO::PARAM_STR);
}
$stmt2-> execute();
	if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
           $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
	      $response['voucherlist']= $data;
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
