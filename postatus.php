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
$fdate= $data->fdate;
$todate= $data->todate;
$status= $data->status;
$response = array();
try{
if(!empty($accesskey) && !empty($status)  && !empty($fdate) && !empty($todate)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
if($status == 'pending'){
$stmt1 = $pdoread->prepare("SELECT `po_generate`. `po_number` , `vendormaster`.`legalname`,`po_generate`.`po_total`,`unit_master`.`unit`,`po_generate`.`po_status` FROM `po_generate`INNER JOIN `vendormaster` ON `po_generate`.`vendor_sno` = `vendormaster`.`sno` INNER JOIN `unit_master` ON `po_generate`.`invoice_unit`= `unit_master`.`sno` WHERE (date(`created_on`) BETWEEN :fdate AND :todate ) AND `po_status` ='pending'");
$stmt1 -> bindParam(":fdate", $fdate, PDO::PARAM_STR); 
$stmt1 -> bindParam(":todate", $todate, PDO::PARAM_STR);  
$stmt1 -> execute(); 
   
}else{
     
   $stmt1 = $pdoread->prepare("SELECT `po_generate`. `po_number` , `vendormaster`.`legalname`,`po_generate`.`po_total`,`unit_master`.`unit`,`po_generate`.`po_status` FROM `po_generate`INNER JOIN `vendormaster` ON `po_generate`.`vendor_sno` = `vendormaster`.`sno` INNER JOIN `unit_master` ON `po_generate`.`invoice_unit`= `unit_master`.`sno` WHERE (date(`created_on`) BETWEEN :fdate AND :todate ) AND `po_status` ='approved'");
$stmt1 -> bindParam(":fdate", $fdate, PDO::PARAM_STR); 
$stmt1 -> bindParam(":todate", $todate, PDO::PARAM_STR);   
$stmt1 -> execute(); 
   $res = $stmt1->fetchAll(PDO::FETCH_ASSOC);
     if($stmt1 -> rowCount() > 0){
     http_response_code(200);
          $response['error']= false;
	     $response['message']=" Data found";
		 $response['polist']= $res;
     }
else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
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
 
}catch(Exception $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e; 
}
echo json_encode($response);
$pdoread = null;
?>