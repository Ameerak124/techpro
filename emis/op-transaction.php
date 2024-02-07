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
$accesskey =$data->accesskey;
$fromdate=date_format(date_create($data->fromdate),"Y-m-d");
$todate=date_format(date_create($data->todate),"Y-m-d");

$response =array();
try {
 if(!empty($accesskey) && !empty($fromdate)&& !empty($todate)){
   
$check = $pdoread->prepare("SELECT `userid` ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$checkresult=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount() > 0){
$query=$pdoread->prepare("SELECT @a:=@a+1 serial_number,`op_billing_generate`.`umrno`AS umrno, `op_billing_generate`.`inv_no`AS transaction_id,`op_billing_generate`.`patient_name`AS patient_name,`op_billing_generate`.`item_count`AS netservices ,`op_billing_generate`.`qty_count` AS netquantity ,`op_billing_generate`.`item_val` AS billvalue,(CASE WHEN `op_billing_generate`.`approved_status`='No Update' THEN 'Pending' ELSE `op_billing_generate`.`approved_status`END)AS approved_status,(CASE WHEN `op_billing_generate`.`approved_status` IN ('No Update') THEN '#fedbcc' WHEN `op_billing_generate`.`approved_status` IN ('Pending For Clarification') THEN '#e6ccff' ELSE '#e6ffea' END) AS backgroundcolor,(CASE WHEN `op_billing_generate`.`approved_status` IN ('No Update') THEN '#fe9669' WHEN `op_billing_generate`.`approved_status` IN ('Pending For Clarification') THEN '#8c1aff' ELSE '#53ed6c' END) AS textcolor,`op_billing_generate`.`discount_val`AS discountval,`op_billing_generate`.`after_val`AS netamount,`op_billing_generate`.`paymentmode`AS paymentmode,`op_billing_generate`.`status`AS status ,CONCAT(TIMESTAMPDIFF(YEAR,`umr_registration`.`patient_age`, CURDATE()),'-', SUBSTRING(`umr_registration`.`patient_gender`,1,1))AS agegender,`op_billing_generate`.`remarks`,`op_billing_generate`.`discount` AS discpercent FROM (SELECT @a:= 0) AS a,`op_billing_generate` LEFT JOIN `umr_registration` ON `umr_registration`.`umrno`=`op_billing_generate`.`umrno` WHERE DATE(`created_on`) BETWEEN :fromdate AND :todate AND `cost_center`= :costcenter AND `op_billing_generate`.`status` = 'Confirmed' AND `op_billing_generate`.`discount`!='0' ORDER BY `op_billing_generate`.`id` DESC");
	
$query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$query->bindParam(':todate', $todate, PDO::PARAM_STR);
$query->bindParam(':costcenter', $checkresult['cost_center'], PDO::PARAM_STR);
$query->execute();

if($query->rowCount()>0){
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 while($res=$query->fetch(PDO::FETCH_ASSOC)){
    	$response['optransactionlist'][] = $res;
           
 }
}else{
	http_response_code(503);
$response['error']=true;
$response['message']='No Data Found';
}
}else {
	http_response_code(400);
$response['error']=true;
$response['message']='Access denied!';
}
}else {
	http_response_code(400);
$response['error']=true;
$response['message']='Sorry! Some Details Are Missing';
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread=null;
?>