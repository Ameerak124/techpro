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
$accesskey = trim($data->accesskey);
$fromdate = date_format(date_create(trim($data->fromdate)),"Y-m-d");
$todate = date_format(date_create(trim($data->todate)),"Y-m-d");
try {	
if(!empty($accesskey) && !empty($fromdate) && !empty($todate)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `op_billing_generate`.`umrno`AS umrno,`op_billing_generate`.`inv_no`AS billno, `op_billing_generate`.`invoice_no`AS transaction_id,`op_billing_generate`.`patient_name`,`op_billing_generate`.`item_count`AS netservices ,`op_billing_generate`.`qty_count` AS netquantity ,`op_billing_generate`.`original_value` AS billvalue,(CASE WHEN `op_billing_generate`.`approved_status`='No Update' THEN 'Pending' WHEN `op_billing_generate`.`approved_status`='Cancelled' THEN 'Cancelled' ELSE `op_billing_generate`.`approved_status`END )AS approved_status,(CASE WHEN `op_billing_generate`.`approved_status`='No Update' THEN '#fe9669' WHEN `op_billing_generate`.`approved_status`='Cancelled' THEN '#e01414' ELSE '#18c73f' END) AS textcolor,(CASE WHEN `op_billing_generate`.`approved_status`='No Update' THEN '#fedbcc'
WHEN `op_billing_generate`.`approved_status`='Cancelled' THEN '#ff8f8f' ELSE '#b5ebc1' END) AS bgcolor,(`op_billing_generate`.`itemwise_disc`+`op_billing_generate`.`discount_val`) AS discountval,`op_billing_generate`.`after_val`AS netamount,`op_billing_generate`.`paymentmode`AS paymentmode,`op_billing_generate`.`status` AS status,'#b5ebc1' AS bagstatus,'#18c73f' AS textstatus,CONCAT(TIMESTAMPDIFF(YEAR,`umr_registration`.`patient_age`, CURDATE()),'-', SUBSTRING(`umr_registration`.`patient_gender`,1,1))AS agegender,DATE_FORMAT(`op_billing_generate`.`created_on`,'%d-%b-%Y')AS billdate FROM `op_billing_generate` LEFT JOIN `umr_registration` ON `umr_registration`.`umrno`=`op_billing_generate`.`umrno` WHERE DATE(`created_on`) BETWEEN :fromdate AND :todate AND `cost_center`= :cost_center AND `op_billing_generate`.`status` !='Pending' ORDER BY `op_billing_generate`.`id` DESC;");
    $reglist->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
	$reglist->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
	$reglist->bindParam(':todate', $todate, PDO::PARAM_STR);

$reglist -> execute();
if($reglist -> rowCount() > 0){
	    http_response_code(200);
		$response['error']= false;
	$response['message']= "Data found";
	$response['netamount']= "Net Amount";
	$response['print']= "PRINT";
	while($listwise = $reglist->fetch(PDO::FETCH_ASSOC)){
			$response['optransactionlist'][] = $listwise;
		}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>