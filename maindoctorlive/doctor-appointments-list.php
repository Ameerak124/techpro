<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$invoice_no = trim($data->invoice_no);
$doctorcode = trim($data->doctorcode);
$category=trim($data->category);
$selectdate = date('Y-m-d', strtotime($data->selectdate));
try {

if(!empty($accesskey)&& !empty($doctorcode) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	/* get item data start*/
	if($category=='ALL' && !empty($selectdate) && !empty($doctorcode)){
		/* $getitem = $pdo -> prepare("SELECT `location`, `doctor_code`,`umrno`,Concat('https://meet.daylo.com/r/',MD5(`bill_no`)) As url , DATE_FORMAT(`date`,'%d-%b-%Y') AS date, DATE_FORMAT(`slot`,'%h:%i %p')AS slot, `requisition_no`, `bill_no`,`transid`,`patient_name`,CONCAT('Age : ',TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs - ' ,`gender`)AS patient_age,invoice_no,if(amount = '0','',CONCAT('₹',`amount`,' /-')) AS fees,`gender`, `patient_number`, `patient_mail`, `vip_status` ,CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()))AS age,IF(`amount`='0','Unpaid','Paid')  as paymentstatus, IF(`amount`='0','#a51e23','#236525') as paymentcolour, '#2CBA32' as timecolour,  '#2CBA32' as costcolour FROM `patient_details` WHERE `slot_status`='booked' AND `doctor_code`=:doctorcode AND `location`=:branch AND `date`=:selectdate AND `requisition_no` NOT IN ('OTHERS') AND invoice_no NOT IN('OTHERS') GROUP BY invoice_no ");
		 */
		$getitem = $pdoread -> prepare("SELECT location, doctor_code,umrno, Concat('https://meet.daylo.com/r/',MD5(`bill_no`)) As url ,DATE_FORMAT(`date`,'%d-%b-%Y') AS date, DATE_FORMAT(slot,'%h:%i %p')AS slot, requisition_no, bill_no,transid,patient_name,CONCAT('Age : ',TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs - ' ,`gender`)AS patient_age,(CASE WHEN invoice_no='OTHERS' THEN ''ELSE  invoice_no END) AS invoice_no,if(amount = '0','',CONCAT('₹',`amount`,' /-')) AS fees,gender, patient_number, patient_mail, vip_status ,CONCAT(TIMESTAMPDIFF(YEAR, patient_age, CURDATE()))AS age ,IF(`amount`='0','Unpaid','Paid')  as paymentstatus, IF(`amount`='0','#a51e23','#236525') as paymentcolour, '#2CBA32' as timecolour,  '#2CBA32' as costcolour FROM patient_details WHERE slot_status='booked' AND doctor_code=:doctorcode AND location=:branch AND date=:selectdate GROUP BY invoice_no");
		
		// $getitem -> bindValue(':invoice_no', "%{$invoice_no}%", PDO::PARAM_STR);
		$getitem -> bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
		$getitem -> bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
		$getitem -> bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
		$getitem -> execute();
		if($getitem -> rowCount() > 0){
            http_response_code(200);
			$response['error'] = false;
			$response['message'] = 'Data found';	
			while($invres = $getitem->fetch(PDO::FETCH_ASSOC)){
				$response['appointmentlist'][] = $invres;
			}
		}else{
			                 http_response_code(503);
							$response['error'] = true;
							$response['message'] = 'No data found';
						}
					 /* update issue item end */

	}else if($category=='FETCH' && !empty($invoice_no)){
		$getitem = $pdoread -> prepare("SELECT `invoice_no`,`patient_name`,`inv_no`,DATE_FORMAT(`bill_date`,'%d-%b-%Y %h:%i %p') AS BILLDATE,`requisition_no`,`umrno`,Concat('https://meet.daylo.com/r/',MD5(op_billing_generate.`invoice_no`)) As url,`op_billing_generate`.`patient_gender`,
	IFNULL((SELECT 	CONCAT(TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, CURDATE()),'Yrs')AS patient_age
	 FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS patient_age,
     IFNULL((SELECT 	CONCAT(TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, CURDATE()))AS patient_age
	 FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS age,
	IFNULL((SELECT umr_registration.email_id FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS email, IFNULL((SELECT umr_registration.mobile_no FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS mobile_no	 FROM `op_billing_generate` LEFT JOIN `op_biling_history` ON `op_biling_history`.`billno` = `op_billing_generate`.`inv_no` WHERE (`invoice_no`=:invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible') OR (`umrno` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible') OR (`op_billing_generate`.`mobile` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible')");
		// $getitem -> bindValue(':invoice_no', "%{$invoice_no}%", PDO::PARAM_STR);
		$getitem -> bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
		$getitem -> bindParam(':invoice_no', $invoice_no, PDO::PARAM_STR);
		$getitem -> execute();
		if($getitem -> rowCount() > 0){
             http_response_code(200);
			$response['error'] = false;
			$response['message'] = 'Data found';	
			while($invres = $getitem->fetch(PDO::FETCH_ASSOC)){
				$response['appointmentlist'][] = $invres;
			}
}else{
	     http_response_code(503);
		$response['error'] = true;
		$response['message'] = 'No data found';
}

					
	}else{
/*
SELECT `invoice_no`,`patient_name`,`inv_no`,DATE_FORMAT(`bill_date`,'%d-%b-%Y %h:%i %p') AS BILLDATE,`requisition_no`,`umrno`,IFNULL((SELECT `umr_registration`.`patient_age` FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS patient_age,`op_billing_generate`.`patient_gender`,
	IFNULL((SELECT umr_registration.vip_patient FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS vip_patient,  `op_biling_history`.`total` AS fees ,
	(CASE WHEN op_biling_history.visit_type='REVISIT' THEN '2' ELSE '1'END) AS visit_count,
	IFNULL((SELECT umr_registration.email_id FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS email,IFNULL((SELECT umr_registration.mobile_no FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS mobile_no FROM `op_billing_generate` LEFT JOIN `op_biling_history` ON `op_biling_history`.`billno` = `op_billing_generate`.`inv_no` WHERE (`invoice_no` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible') OR (`umrno` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible') OR (`op_billing_generate`.`mobile` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible' )LIMIT 5
*/
$getitem = $pdoread -> prepare("SELECT `op_billing_generate`.`invoice_no`,`op_billing_generate`.`patient_name`,`op_billing_generate`.`inv_no`,DATE_FORMAT(`bill_date`,'%d-%b-%Y %h:%i %p') AS BILLDATE,`op_biling_history`.`requisition_no`,`op_billing_generate`.`umrno`,Concat('https://meet.daylo.com/r/',MD5(op_billing_generate.`invoice_no`)) As url,IFNULL((SELECT `umr_registration`.`patient_age` FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS patient_age,`op_billing_generate`.`patient_gender`,
	IFNULL((SELECT umr_registration.vip_patient FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS vip_patient,  `op_biling_history`.`total` AS fees ,
	(CASE WHEN op_biling_history.visit_type='REVISIT' THEN '2' ELSE '1'END) AS visit_count,
	IFNULL((SELECT umr_registration.email_id FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS email,IFNULL((SELECT umr_registration.mobile_no FROM umr_registration WHERE umr_registration.umrno=op_billing_generate.umrno),'')AS mobile_no FROM `op_billing_generate` INNER JOIN `op_biling_history` ON `op_biling_history`.`billno` = `op_billing_generate`.`inv_no` LEFT JOIN `patient_details` ON `op_billing_generate`.`invoice_no`=`patient_details`.`invoice_no` AND `patient_details`.`slot_status`!='booked' WHERE (`op_billing_generate`.`invoice_no` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible') OR (op_billing_generate.`umrno` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible') OR (`op_billing_generate`.`mobile` LIKE :invoice_no AND `op_billing_generate`.`status` = 'Confirmed' AND `op_biling_history`.`servicecode` = :doctorcode AND `op_biling_history`.`status` = 'Visible' )LIMIT 5");
$getitem -> bindValue(':invoice_no', "%{$invoice_no}%", PDO::PARAM_STR);
$getitem -> bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
$getitem -> execute();
if($getitem -> rowCount() > 0){
              http_response_code(200);
			$response['error'] = false;
			$response['message'] = 'Data found';	
			while($invres = $getitem->fetch(PDO::FETCH_ASSOC)){
				$response['appointmentlist'][] = $invres;
			}
}else{
	     http_response_code(503);
		$response['error'] = true;
		$response['message'] = 'No data found';
}

					}

}
else
{
	 http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
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
$pdoread = null;
?>