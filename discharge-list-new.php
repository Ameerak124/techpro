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
$response =array();
$response1 =array();
try {
 if(!empty($accesskey)){
$check = $pdoread->prepare("SELECT `userid` ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$checkresult=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount() > 0){
$query=$pdoread->prepare("SELECT `admissionno` AS ipno,UPPER(`patientname`) AS patientname,DATE_FORMAT(`admittedon`,' %h:%i %p') AS admittedon,DATE_FORMAT(`admittedon`,'%d-%b-%y') AS admitteddate,DATE_FORMAT(`dischargedon`,'%d-%b-%y') AS dischargedon,DATE_FORMAT(`registration`.`dischargedon`,'%h:%i %p')AS dischargetime,`contactno`,`consultantname`,`department`,`payment_history`.`amount` AS total_bill,`registration`.`admissionstatus`,IFNULL((SELECT `sponsor_master`.`organization_name` FROM `sponsor_master` INNER JOIN `umr_registration` ON `umr_registration`.`organization_code`=`sponsor_master`.`organization_code` WHERE `umr_registration`.`umrno`=`registration`.`umrno` LIMIT 1),'GENERAL')AS sponsor_name,
 'Yes' as trackbutton, 'No' as checkoutbutton, 'Yes' as viewbillbutton FROM (SELECT @a:=0) AS a, `registration`
LEFT JOIN `payment_history` ON `payment_history`.`admissionon`=`registration`.`admissionno`
WHERE `admissionstatus` = 'Discharged' GROUP BY ipno ");
$query->execute();
if($query->rowCount()>0){
	$sn=1;
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 $response['deliv_date']="Deliv Date";
 $response['viewbill']="View Bill";
 $response['track']="Track";
 $response['checkout']="Check - Out";
$regres = $query->fetchAll(PDO::FETCH_ASSOC);
		$response['dischargelist'] = $regres;
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