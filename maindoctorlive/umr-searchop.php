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
$search =$data->umrno;
$response =array();
try {
      if(!empty($accesskey)){
   
$check = $pdoread->prepare("SELECT `userid` ,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$checkresult=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount() > 0){
$query=$pdoread->prepare("SELECT @a:=@a+1 AS sno,`patient_type`,`umr_no`,op_biling_history.`requisition_no`,`services`, `quantity`,`total`,(CASE WHEN op_biling_history.`visit_type`='REVISIT' THEN 'label-warning' ELSE 'label-info' END)AS visit_type,op_biling_history.`visit_type`AS pt_visit,(CASE WHEN `refreral_doctor`='' THEN 'NA' ELSE `refreral_doctor` END)AS refreral_doctor,DATE_FORMAT(`op_biling_history`.`createdon`,'%d-%b-%Y')AS billdate ,IFNULL((SELECT `sponsor_master`.`category` FROM `sponsor_master` WHERE `sponsor_master`.`organization_code`=`umr_registration`.`organization_code` AND sponsor_master.cost_center=umr_registration.branch),'NA') AS sponsor_category,`op_biling_history`.`servicecode`
,(CASE WHEN `umr_registration`.`organization_name`='No Update' THEN 'NA' ELSE `umr_registration`.`organization_name` END)AS sponsor_name,(CASE WHEN`umr_registration`.`vip_patient`='0' THEN 'd-none' ELSE 'label-danger' END)AS vip
,(SELECT `op_billing_generate`.`invoice_no` FROM `op_billing_generate` WHERE `op_billing_generate`.`status`!='Cancelled' AND `op_billing_generate`.`inv_no`=`op_biling_history`.`billno`)AS billno,CONCAT(`umr_registration`.`patient_name`,'',`umr_registration`.`middle_name`,'',`umr_registration`.`last_name`)AS patient_name, DATE_FORMAT(`slot`,'%H:%i')AS slot ,(SELECT `op_billing_generate`.`invoice_no` FROM `op_billing_generate` WHERE `op_billing_generate`.`status`!='Cancelled' AND `op_billing_generate`.`inv_no`=`op_biling_history`.`billno`)AS invoice_no FROM (SELECT @a:=0) AS a,`op_biling_history` INNER JOIN `umr_registration` ON `umr_registration`.`umrno`=`op_biling_history`.`umr_no` inner join patient_details on umr_registration.umrno=patient_details.umrno
WHERE (`op_biling_history`.`category`='CONSULTATION' AND `op_biling_history`.`status`='Visible' AND  `costcenter`=:branch  AND `umr_registration`.`umrno` LIKE :search ) OR (`op_biling_history`.`category`='Consultation' AND `op_biling_history`.`status`='Visible' AND  `costcenter`=:branch AND `umr_registration`.`umrno` LIKE :search)  order by sno desc limit 1 ");
$query->bindParam(':branch', $checkresult['cost_center'], PDO::PARAM_STR);
$query -> bindValue(":search", "%{$search}%", PDO::PARAM_STR);
$query->execute();
$s=0;
if($query->rowCount()>0){
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 while($res=$query->fetch(PDO::FETCH_ASSOC)){
       $response['opconsultationslist'][]=$res;
           
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