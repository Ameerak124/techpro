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
$reqstatus = $data->reqstatus;
$remarks = $data->remarks;
$voucherid = $data->voucherid;
$delete = 'delete';
$one = 1;
$response = array();
try{
if(!empty($accesskey) && !empty($reqstatus)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$empdata = $check -> fetch(PDO::FETCH_ASSOC);
$empname = $empdata['userid'];

if($reqstatus == 'Pending'){
	$stmt2 =  $pdo4 -> prepare("UPDATE ms_billing.cashvoucher SET  is_approved= :one, approved_by= :empname , approval_remarks= :remarks, status= :stat, approved_on= CURRENT_TIMESTAMP WHERE voucher_id= :voucherid AND status  = :reqstat");
	$stmt2 -> bindParam(":one", $one, PDO::PARAM_STR);
	$stmt2 -> bindParam(":empname", $empname, PDO::PARAM_STR);
	$stmt2 -> bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt2 -> bindParam(":stat", $status, PDO::PARAM_STR);
	$stmt2 -> bindParam(":voucherid", $voucherid, PDO::PARAM_STR);
	$stmt2 -> bindParam(":reqstat", $reqstatus, PDO::PARAM_STR);
	$status="Pending at audit";
	$stmt2-> execute();
}
else if($reqstatus == "Pending at audit"){
	$stmt2 =  $pdo4 -> prepare("UPDATE ms_billing.cashvoucher SET  is_audited = :one, audited_by= :empname , audit_remarks= :remarks, status= :stat, auditapproved_on = CURRENT_TIMESTAMP WHERE voucher_id= :voucherid AND status  = :reqstat");
	$stmt2 -> bindParam(":one", $one, PDO::PARAM_STR);
	$stmt2 -> bindParam(":empname", $empname, PDO::PARAM_STR);
	$stmt2 -> bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt2 -> bindParam(":stat", $status, PDO::PARAM_STR);
	$stmt2 -> bindParam(":voucherid", $voucherid, PDO::PARAM_STR);
	$stmt2 -> bindParam(":reqstat", $reqstatus, PDO::PARAM_STR);
	$status="Pending at finance";
	$stmt2-> execute();
}
else if($reqstatus == "Pending at finance"){
	$stmt2 = $pdo4-> prepare("UPDATE ms_billing.cashvoucher SET  is_finance_approved = :one, finance_approved_by = :empname , finance_remarks= :remarks, status= :stat, financeapproved_on = CURRENT_TIMESTAMP WHERE voucher_id= :voucherid AND status  = :reqstat");
	$stmt2 -> bindParam(":one", $one, PDO::PARAM_STR);
	$stmt2 -> bindParam(":empname", $empname, PDO::PARAM_STR);
	$stmt2 -> bindParam(":remarks", $remarks, PDO::PARAM_STR);
	$stmt2 -> bindParam(":stat", $status, PDO::PARAM_STR);
	$stmt2 -> bindParam(":voucherid", $voucherid, PDO::PARAM_STR);
	$stmt2 -> bindParam(":reqstat", $reqstatus, PDO::PARAM_STR);
	$status="Issued";
	$stmt2-> execute();
}
	if($stmt2 -> rowCount() > 0){
		 http_response_code(200);
		 $response['error']= false;
		 $response['message']="Appproved";
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Not Approved!";
     }
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}
else{
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
