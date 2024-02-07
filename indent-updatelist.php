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
$issued_qty =  trim($data->issued_qty);
$response = array();
try {
if(!empty($accesskey) && !empty($sno) && !empty($cen_sno)&& !empty($issued_qty)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$stmt = $check -> fetch(PDO::FETCH_ASSOC);
	$check1 = $pdoread->prepare("SELECT `onhand` as issue_qty1, `expiry` as exp_date, `batch`as batch_no, `purchase_rate`, `sale_rate`, `sale_value`, `mrp`, `hsn`, `uom` FROM `central_store` WHERE `sno` =:cen_sno AND `onhand`>0");
     $check1->bindParam(':cen_sno',$cen_sno, PDO::PARAM_STR);
       $check1-> execute();
if($check1-> rowCount() > 0){
	$chk = $check1-> fetch(PDO::FETCH_ASSOC);
	if($chk['issue_qty1'] > $issued_qty)
{
  $val=$issued_qty;
   }
   else{
   $val=$chk['issue_qty1'];
	 }
	 if($check1-> rowCount() > 0){
		
	$updt = $pdo4->prepare("UPDATE `indent_queue` SET `mrp`= :mrp ,`hsn`=:hsn,`exp_date`=:exp_date,`batch_no`=:batch_no,`issued_qty`=:val,`purchase_rate`=:purchase_rate,`sale_rate`=:sale_rate,`sale_value`=:sale_value,`uom`=:uom,`issued_status`='Updated',`updated_by` = :userid,`updated_on`=CURRENT_TIMESTAMP,
	`cen_sno`=:cen_sno WHERE `sno` =:sno");
	$updt->bindParam(':sno',$sno, PDO::PARAM_STR);
	$updt->bindParam(':mrp',$chk['mrp'], PDO::PARAM_STR);
	$updt->bindParam(':hsn',$chk['hsn'], PDO::PARAM_STR);
	$updt->bindParam(':exp_date',$chk['exp_date'], PDO::PARAM_STR);
	$updt->bindParam(':batch_no',$chk['batch_no'], PDO::PARAM_STR);
	$updt->bindParam(':val',$val, PDO::PARAM_STR);
	$updt->bindParam(':purchase_rate',$chk['purchase_rate'], PDO::PARAM_STR);
	$updt->bindParam(':sale_rate',$chk['sale_rate'], PDO::PARAM_STR);
	$updt->bindParam(':sale_value',$chk['sale_value'], PDO::PARAM_STR);
	$updt->bindParam(':uom',$chk['uom'], PDO::PARAM_STR);
	$updt->bindParam(':userid',$stmt['userid'], PDO::PARAM_STR);
	$updt->bindParam(':cen_sno',$cen_sno, PDO::PARAM_STR);
    $updt-> execute();
if($updt -> rowCount() > 0){
	
	$stmt1 = $pdo4->prepare("INSERT INTO `department_issued_logs`(`queue_sno`, `indent_no`, `branch`, `itemcode`, `itemname`, `indent_qty`, `issued_qty`, `batch_no`, `expiry_date`, `priority`, `status`, `created_on`, `createdby`, `ref_itemno`,`cen_sno`)SELECT `sno`, `indent_no`,  `branch`,  `itemcode`, `itemname`, `qty`,`issued_qty`, `batch_no`,`exp_date`, `priority`,'added', `created_on`, `createdby`, `ref_itemno`,`cen_sno` FROM `indent_queue` WHERE `sno` =:sno");
	 $stmt1->bindParam(':sno',$sno, PDO::PARAM_STR);
  $stmt1-> execute();
	if($stmt1 -> rowCount() > 0){
	
	 $updt1 = $pdo4->prepare("UPDATE `central_store` SET `onhand`=(`onhand`-:val) WHERE `sno`=:cen_sno");
	$updt1->bindParam(':cen_sno',$cen_sno, PDO::PARAM_STR);
	$updt1->bindParam(':val',$val, PDO::PARAM_STR);
	$updt1-> execute();
      http_response_code(200);
	  $response['error']= false;
	  $response['message']= "Data inserted Stock ".$val." Available";
	
	}
	 else{
			http_response_code(503);
			$response['error']= true;
	        $response['message']="NO data inserted"; 
		}
		}
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Stock Not Available";
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