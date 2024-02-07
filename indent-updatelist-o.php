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
$accesskey =  trim($data->accesskey);
$sno =  trim($data->sno);
$cen_sno =  trim($data->cen_sno);
$mrp =  trim($data->mrp);
$hsn=  trim($data->hsn);
$exp_date =  trim($data->exp_date);
$batch_no =  trim($data->batch_no);
$issued_qty =  trim($data->issued_qty);
$purchase_rate =  trim($data->purchase_rate);
$sale_rate =  trim($data->sale_rate);
$sale_value =  trim($data->sale_value);
$uom =  trim($data->uom);
$response = array();
try {
if(!empty($accesskey) && !empty($sno) && !empty($mrp)&& !empty($hsn)&& !empty($exp_date)&& !empty($batch_no)&& !empty($issued_qty)&& !empty($purchase_rate)&& !empty($sale_rate)&& !empty($sale_value)&& !empty($uom)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$stmt = $check -> fetch(PDO::FETCH_ASSOC);
	$updt = $pdo4->prepare("UPDATE `indent_queue` SET `mrp`= :mrp ,`hsn`=:hsn,`exp_date`=:exp_date,`batch_no`=:batch_no,`issued_qty`=:issued_qty,`purchase_rate`=:purchase_rate,`sale_rate`=:sale_rate,`sale_value`=:sale_value,`uom`=:uom,`issued_status`='Updated',`updated_by` = :userid,`updated_on`=CURRENT_TIMESTAMP WHERE `sno` =:sno");
	$updt->bindParam(':sno',$sno, PDO::PARAM_STR);
	$updt->bindParam(':mrp',$mrp, PDO::PARAM_STR);
	$updt->bindParam(':hsn',$hsn, PDO::PARAM_STR);
	$updt->bindParam(':exp_date',$exp_date, PDO::PARAM_STR);
	$updt->bindParam(':batch_no',$batch_no, PDO::PARAM_STR);
	$updt->bindParam(':issued_qty',$issued_qty, PDO::PARAM_STR);
	$updt->bindParam(':purchase_rate',$purchase_rate, PDO::PARAM_STR);
	$updt->bindParam(':sale_rate',$sale_rate, PDO::PARAM_STR);
	$updt->bindParam(':sale_value',$sale_value, PDO::PARAM_STR);
	$updt->bindParam(':uom',$uom, PDO::PARAM_STR);
	$updt->bindParam(':userid',$stmt['userid'], PDO::PARAM_STR);
    $updt-> execute();
if($updt -> rowCount() > 0){
	 $updt1 = $pdo4->prepare("UPDATE `central_store` SET `onhand`=(`onhand`-:issued_qty) WHERE `sno`=:cen_sno");
	 $updt1->bindParam(':cen_sno',$cen_sno, PDO::PARAM_STR);
	 $updt1->bindParam(':issued_qty',$issued_qty, PDO::PARAM_STR);
	 $updt1-> execute();
      http_response_code(200);
	  $response['error']= false;
	  $response['message']= "Data updated";
	
	}
	 else{
			http_response_code(503);
			$response['error']= true;
	          $response['message']="NO data updated"; 
		}
		}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(Exception $e) {

	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
	 
	