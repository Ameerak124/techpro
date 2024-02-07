<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
if(!empty($data)){  
}else{
$dataa = json_encode($_POST);
$data = json_decode($dataa);
}
$accesskey = $data->accesskey;
$active = 'Active';
$vendorid = $data->id;
$paymentterms = $data->paymentterms;
$deliveryterms = $data->deliveryterms;
$creditperiod = $data->creditperiod;
$termination = $data->termination;
$invoiceunit = $data->invoiceunit;
$remarks = $data->remarks;
if(!empty($accesskey) && !empty($vendorid) && !empty($paymentterms) && !empty($deliveryterms) && !empty($creditperiod) && !empty($termination) && !empty($invoiceunit) && !empty($remarks)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $query = "SELECT IFNULL(MAX(`po_number`),'MCPOMWC000000') AS ponumber FROM `po_generate` LIMIT 1";   
     $sbmt = $pdoread -> prepare($query);   
     $sbmt -> execute();
     if($sbmt -> rowCount() > 0){
          $data = $sbmt -> fetch(PDO::FETCH_ASSOC);
          $ponumber  = $data['ponumber'];
          ++$ponumber;
          if($ponumber != ''){
               $poquery =  "INSERT IGNORE INTO `po_generate`(`po_number`,`vendor_sno`, `payment_terms`, `delivery_terms`, `termination`, `remarks`, `invoice_unit`, `delivery_unit`,`created_on`, `emp_details`) VALUES (:po_number, :vendor_sno, :payment_terms, :delivery_terms,  :termination, :remarks, :invoice_unit, :delivery_unit, CURRENT_TIMESTAMP, :emp_details)"; 
               $po_sbmt = $pdo4 -> prepare($poquery);
               $po_sbmt -> bindParam(":po_number",$ponumber,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":vendor_sno",$vendorid,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":payment_terms",$paymentterms,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":delivery_terms",$deliveryterms,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":termination",$termination,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":remarks",$remarks,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":invoice_unit",$invoiceunit,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":delivery_unit",$invoiceunit,PDO::PARAM_STR); 
               $po_sbmt -> bindParam(":emp_details",$empname,PDO::PARAM_STR); 
               $po_sbmt -> execute();
               if($po_sbmt -> rowCount() > 0){
                    $data = $sbmt -> fetchAll(PDO::FETCH_ASSOC);
					http_response_code(200);
                    $response['error']= false;
                    $response['message']="Data Found";
                    $response['ponumber'] = $ponumber;
               }
               else
               {
				   http_response_code(503);
                    $response['error']= true;
                    $response['message']="No Data Found!";
               }
          }
          else{
			  http_response_code(503);
               $response['error'] = true;
               $response['message'] = "Something went wrong";
          }
     }
     else{http_response_code(503);
          $response['error'] = true;
          $response['message'] = "Something went wrong";
     }
}
else
{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
  
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>