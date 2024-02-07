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
$indent_no= $data->indent_no;
$response = array();
try{
if(!empty($accesskey) &&!empty($indent_no) ){
	$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$emp = $check -> fetch(PDO::FETCH_ASSOC);
$stmt=$pdoread->prepare("SELECT `id` FROM `department_indent` WHERE `status`IN (' ','pending') and indent_number=:indent_no");
$stmt->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
$stmt -> execute();
if($stmt-> rowCount() > 0){
	$stmtt=$pdoread->prepare("SELECT  `indent_no` FROM `indent_queue` where `indent_no`=:indent_no");
	$stmtt->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
	$stmtt->execute();
if($stmtt -> rowCount() > 0){
	
}else{
	$stmt1=$pdoread->prepare("INSERT INTO `indent_queue`( `indent_no`, `indent_sno`, `branch`, `item_category`, `item_group`, `item_subgroup`, `department`, `stcokpoint`, `itemcode`, `itemname`, `qty`, `priority`, `status`, `created_on`, `createdby`,  `ref_itemno`, `stock_status`) SELECT `indent_no`,`sno`,  `branch`, `item_category`, `item_group`, `item_subgroup`, `department`, `stcokpoint`, `itemcode`, `itemname`, `qty`, `priority`, `status`, CURRENT_TIMESTAMP, :userid,`ref_itemno`, `stock_status` FROM `department_indent_items` WHERE `indent_no`=:indent_no");
$stmt1->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
$stmt1->bindParam(':userid',$emp['userid'], PDO::PARAM_STR);
$stmt1-> execute();	
}//princeofindia12@
$getindent=$pdoread->prepare("SELECT `branch`, `branch_code`, `department`, `department_code`,`created_by`, `created_on` FROM `department_indent` WHERE `indent_number`=:indent_no");
$getindent->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
$getindent->execute();
$indentdetails = $getindent -> fetch(PDO::FETCH_ASSOC);
$stmt2=$pdoread->prepare("SELECT `sno`, `indent_no`, `indent_sno`, `branch`, `item_category`, `item_group`, `item_subgroup`, `department`, `stcokpoint`, `itemcode`, `itemname`, `qty`, `priority`, `status`, `created_on`, `createdby`, `modified_by`, `modified_on`, `status2`, `ref_itemno`, `stock_status`, `mrp`, `hsn`, `exp_date`, `batch_no`, `issued_qty`, `purchase_rate`, `sale_rate`, `sale_value`, `uom`, `issued_status` FROM `indent_queue` WHERE `indent_no`=:indent_no and `issued_status` not IN ('delete','Received')");
$stmt2->bindParam(':indent_no', $indent_no, PDO::PARAM_STR);
$stmt2-> execute();
	if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
          $data = $stmt2 -> fetchAll(PDO::FETCH_ASSOC);
		 $response['error']= false;
		 $response['message']="Data Found";
	     $response['departmentname']=$indentdetails['department'];
	     $response['createdby']=$indentdetails['created_by'];
	     $response['createdon']=$indentdetails['created_on'];
          $response['data']= $data;
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }	
}else{
	//indent not approved
	     http_response_code(503);
          $response['error']= true;
	     $response['message']="indent not approved";
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
	$response['message']= "Connection failed: " . $e;
}
echo json_encode($response);
unset($pdoread);
?>
