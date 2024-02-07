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
$orderno =  $data->orderno;
$response = array();
try{
if(!empty($accesskey) && !empty($orderno)){
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
$emp = $check -> fetch(PDO::FETCH_ASSOC);
     //approve indent 
     $approveod_query = "UPDATE `pharmcy_orders` SET `is_accepted`='1',`accepted_on`=CURRENT_TIMESTAMP,`accepted_by`= :empname WHERE `order_no` = :order_no";
     $approveod_sbmt = $pdo4 -> prepare($approveod_query);
     $approveod_sbmt -> bindParam(":order_no", $orderno, PDO::PARAM_STR);
     $approveod_sbmt -> bindParam(":empname", $name, PDO::PARAM_STR);
     $approveod_sbmt -> execute();
     if($approveod_sbmt -> rowCount() > 0){
     $getitems_query  = "SELECT `drug_code`, `ip_no`,`drug_name`, `quantity`, `drug_price`, `hsn`, `pharmcy_orders`.`category`,`pharmcy_orders`.`sub_category` FROM `pharmcy_indent` INNER JOIN `pharmcy_orders` ON `pharmcy_orders`.`order_no` = `pharmcy_indent`.`order_no` WHERE `pharmcy_orders`.`order_no` = :order_no GROUP BY `pharmcy_indent`.`sno`";
     $getitems_sbmt = $pdoread -> prepare($getitems_query);
     $getitems_sbmt -> bindParam(":order_no", $orderno, PDO::PARAM_STR);
     $getitems_sbmt -> execute();
     if($getitems_sbmt -> rowCount() > 0){
		 while($list = $getitems_sbmt -> fetch(PDO::FETCH_ASSOC)){
               $ch = curl_init();
                    $headers  = [
                                'Content-Type: text/plain'
                            ];
                    $postData = [
                         "accesskey" => $accesskey,
                         "admissionno" => $list['ip_no'],
                         "category" => $list['category'],
                         "subcategory" => $list['sub_category'],
                         "servicecode" => $list['drug_code'],
                         "servicestatus" => "Credit",
                         "service" => $list['drug_name'],
                         "hsn_sac" => $list['hsn'],
                         "quantity" => $list['quantity'],
                         "rate" => $list['drug_price']
                    ];
                    curl_setopt($ch, CURLOPT_URL,"https://65.1.244.68/ms-billing/api/add-bill-record.php");
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));           
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    $result     = curl_exec ($ch);
                    $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          }
	 }
	 }
	 if($getitems_sbmt -> rowCount() > 0){
		 $data = $getitems_sbmt -> fetchAll(PDO::FETCH_ASSOC);
		 http_response_code(200);
		 $response['error']= false;
		 $response['message']="pharmindent Approved";
	      
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']="Not approved";
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
   unset($pdo4);
?>
	 
          


