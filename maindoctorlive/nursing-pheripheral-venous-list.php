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
 $data=json_decode(file_get_contents("php://input"));
 $umrno=$data->umrno;
 $accesskey =trim($data->accesskey);
 $umrno =trim($data->umrno);
 $response=array();
 try {
	 if(!empty($accesskey) && !empty($umrno)) {
$check = $pdoread->prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
//fetched details from user logins//
if($check->rowcount() > 0) {
//get fetch
$fetch_data=$pdoread->prepare("SELECT `umr_no`, `admission_no`, `created_name`, `consent_form`, `insertion_type`, `adhered_to`, `site_of_insertion`, `reason_for_insertion`, `size`, `number_of_attempts`, `failure_reason`, `created_by` ,DATE_FORMAT(`created_on`,'%d-%b-%Y') AS created_date, DATE_FORMAT(`created_on`,'%H:%i %p') AS created_time  FROM `nursing_pheripheral_venous` WHERE  `page_id`='V1' AND `umr_no`=:umrno AND `status`='Active' ORDER BY `modified_on` DESC;");
$fetch_data->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$fetch_data->execute();
if($fetch_data->rowCount() > 0){
$sn=0;
	http_response_code(200);
	$response['error']=false;
	$response['message']='Data Found';
	while($res=$fetch_data->fetch(PDO::FETCH_ASSOC)){
	$response['pheripheralvenouslist'][$sn]=$res;
     $sn++;
    }
}else{
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "No Data Found";
}

}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message']= "Access denied!";
}
//Check User Access End
}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed" .$e->getmessage();
 }
echo json_encode($response);
$pdoread = null;
?>