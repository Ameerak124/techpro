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

$category=$data->category;
$accesskey=$data->accesskey;
$umrno= trim($data->umrno);
$grouplevel= trim($data->grouplevel);
$contact = "Tel. No: 040 6833 4455 (24/7)";
$response = array();
try { 

if(!empty($category)&&!empty($accesskey)&&!empty($umrno))
{
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
//accesskey verified//
$cc=$check->fetch(PDO::FETCH_ASSOC);
if($check->rowcount()>0) {
if($category=="umr" && str_replace(" ","",$umrno)) {
if($grouplevel == 'grouplevel'){
	
	$query1=$pdoread->prepare("SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`category`,`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`email_id`, CURRENT_TIMESTAMP AS printedon,( `umr_registration`.`createdon`) AS createdon, (SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`umr_registration`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby,`payment_history`.`receiptno` AS receiptno,`payment_history`.`bill_type`,`payment_history`.`amount` AS billamount,
	`payment_history`.`discount_value`AS discountamount,`payment_history`.`paymentmode` AS paymentmode,`payment_history`.`total`AS total,`branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, :contact as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name
	 FROM `umr_registration`  LEFT JOIN `payment_history` ON `payment_history`.`admissionon`=`umr_registration`.`umrno` AND `payment_history`.`bill_type`='registration'  LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch`
	 WHERE  `umr_registration`.`umrno`LIKE :umrno AND `umr_registration`.`status` ='Visible' GROUP BY `umr_registration`.`umrno` LIMIT 10  ");
	 $query1->bindValue(':umrno', "%{$umrno}%", PDO::PARAM_STR);
	 $query1->bindParam(':userid', $cc['userid'], PDO::PARAM_STR);
	 $query1->bindParam(':contact', $contact, PDO::PARAM_STR);
	 $query1->execute();
}else{
	$query1=$pdoread->prepare("SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, `umr_registration`.`patient_age` AS dob_format, CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`category`,`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`email_id`, CURRENT_TIMESTAMP AS printedon,( `umr_registration`.`createdon`) AS createdon, (SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`umr_registration`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby,`payment_history`.`receiptno` AS receiptno,`payment_history`.`bill_type`,`payment_history`.`amount` AS billamount,
	`payment_history`.`discount_value`AS discountamount,`payment_history`.`paymentmode` AS paymentmode,`payment_history`.`total`AS total,`branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, :contact as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name
	 FROM `umr_registration`  LEFT JOIN `payment_history` ON `payment_history`.`admissionon`=`umr_registration`.`umrno` AND `payment_history`.`bill_type`='registration' LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch`
	 WHERE `umr_registration`.`umrno`LIKE :umrno AND `umr_registration`.`status` ='Visible' AND `umr_registration`.`branch` = :cost_center GROUP BY `umr_registration`.`umrno` LIMIT 10");
	 $query1->bindValue(':umrno', "%{$umrno}%", PDO::PARAM_STR);
	 $query1->bindParam(':userid', $cc['userid'], PDO::PARAM_STR);
	 $query1->bindParam(':cost_center', $cc['cost_center'], PDO::PARAM_STR);
	 $query1->bindParam(':contact', $contact, PDO::PARAM_STR);
	 $query1->execute();
}
if($query1->rowCount()>0){
	 http_response_code(200);
	     $response['error']=false;
     	 $response['message']='Data found1';

while($result=$query1->fetch(PDO::FETCH_ASSOC)) {
	  $response['data'][]=$result;
	  
	
}    
}else {
	    http_response_code(503);
	     $response['error']=true;
     	 $response['message']='No Data found1';
}
}else if($category=="mobile" && str_replace(" ","",$umrno)) {
	if($grouplevel == 'grouplevel'){
		$query2=$pdoread->prepare("SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`category`,`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`email_id`, 
CURRENT_TIMESTAMP AS printedon,DATE(`umr_registration`.`createdon`) AS createdon,
(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`umr_registration`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby, `branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, :contact as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name
FROM `umr_registration` LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch` WHERE `umr_registration`.`mobile_no`LIKE :mobileno AND `umr_registration`.`status` = 'Visible' GROUP BY `umr_registration`.`umrno` LIMIT 5 ");
$query2->bindValue(':mobileno', "%{$umrno}%" , PDO::PARAM_STR);
$query2->bindParam(':userid', $cc['userid'], PDO::PARAM_STR);
$query2->bindParam(':contact', $contact, PDO::PARAM_STR);
$query2->execute();
	}else{
		$query2=$pdoread->prepare("SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`category`,`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`email_id`,`umr_registration`.`patient_age` AS  dob_format,
CURRENT_TIMESTAMP AS printedon,DATE(`umr_registration`.`createdon`) AS createdon,
(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`umr_registration`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby, `branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, :contact as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name
FROM `umr_registration` LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch` WHERE `umr_registration`.`mobile_no`LIKE :mobileno AND `umr_registration`.`status` = 'Visible' AND `umr_registration`.`branch` = :cost_center GROUP BY `umr_registration`.`umrno` LIMIT 5 ");
$query2->bindValue(':mobileno', "%{$umrno}%" , PDO::PARAM_STR);
$query2->bindParam(':userid', $cc['userid'], PDO::PARAM_STR);
$query2->bindParam(':contact', $contact, PDO::PARAM_STR);
$query2->bindParam(':cost_center', $cc['cost_center'], PDO::PARAM_STR);
$query2->execute();
	}

if($query2->rowCount()>0){
	http_response_code(200);
    $response['error']=false;
    $response['message']='Data found';
  while($result1=$query2->fetch(PDO::FETCH_ASSOC)) {
   $response['data'][]=$result1; 
  }  
  
 }else{
http_response_code(503);	 
 $response['error']=true;
     	$response['message']='No data found';
}
}elseif ($category=="empid" && str_replace(" ","",$umrno)) {
$query3=$pdoread->prepare("SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`category`,`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`email_id`, 
CURRENT_TIMESTAMP AS printedon,DATE(`umr_registration`.`createdon`) AS createdon,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`umr_registration`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby,`umr_registration`.`createdby` AS empid, `branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, :contact as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name
FROM `umr_registration` LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch` WHERE `umr_registration`.`createdby` LIKE :empid AND `umr_registration`.`status` = 'Visible' GROUP BY `umr_registration`.`umrno` LIMIT 5 ");
$query3->bindValue(':empid', "%{$umrno}%" , PDO::PARAM_STR);
$query3->bindParam(':userid', $cc['userid'], PDO::PARAM_STR);
$query3->bindParam(':contact', $contact, PDO::PARAM_STR);
$query3->execute();
if($query3->rowCount()>0){
	http_response_code(200);
    $response['error']=false;
    $response['message']='Data found';
  while($result2=$query3->fetch(PDO::FETCH_ASSOC)) {
   $response['data'][]=$result2; 
  }  
  
 }else{
	 http_response_code(503);
	 $response['error']=true;
     	$response['message']='No data found';
}
/*
SELECT `doctor_mediciation`.`billno`, `umr_registration`.`umrno`,`umr_registration`.`patient_name` AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`umr_registration`.`patient_gender` AS gender,`umr_registration`.`mobile_no` AS contactno,`umr_registration`.`bloodgroup`,`umr_registration`.`category`,`umr_registration`.`country` AS nationality,`umr_registration`.`createdby`, `branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, :contact as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name FROM `doctor_mediciation` LEFT JOIN `umr_registration` ON `doctor_mediciation`.`umrno` = `umr_registration`.`umrno` LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch` WHERE (umr_registration.`mobile_no` LIKE :prescriptionid AND `vstatus` = 'Active' AND `source`='OPD') OR (`doctor_mediciation`.`umrno` LIKE :prescriptionid AND `vstatus` = 'Active' AND `source`='OPD') GROUP BY `doctor_mediciation`.`billno` LIMIT 5
*/
}else if($category=="preid"){
	$query=$pdoread->prepare("SELECT `doctor_mediciation`.`billno`, `umr_registration`.`umrno`,`umr_registration`.`patient_name` AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age,`umr_registration`.`patient_gender` AS gender,`umr_registration`.`mobile_no` AS contactno,`umr_registration`.`bloodgroup`,`umr_registration`.`category`,`umr_registration`.`country` AS nationality,`umr_registration`.`createdby`, '' AS display_name,'' as addr,'' AS cit,'' AS gst_no,'' as contact,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name FROM `doctor_mediciation` LEFT JOIN `umr_registration` ON `doctor_mediciation`.`umrno` = `umr_registration`.`umrno` WHERE (umr_registration.mobile_no LIKE :prescriptionid AND `vstatus` = 'Active' AND `source`='OPD' AND umr_registration.branch=:branch)
OR (`doctor_mediciation`.`umrno` LIKE :prescriptionid AND `vstatus` = 'Active' AND `source`='OPD' AND umr_registration.branch=:branch) 
GROUP BY `doctor_mediciation`.`billno` LIMIT 5");
	$query->bindValue(':prescriptionid', "%{$umrno}%", PDO::PARAM_STR);
	$query->bindParam(':branch', $cc['cost_center'], PDO::PARAM_STR);
  $query->execute();
  if($query->rowCount()>0){
	  http_response_code(200);
	  $response['error']=false;
	  $response['message']='Data found';
		  while($queryres=$query->fetch(PDO::FETCH_ASSOC)){
		  $response['data'][]=$queryres;
		  }
		 
		  }else{
http_response_code(503);			  
	  $response['error']=true;
	  $response['message']='No data found';
		  }
		  /*
 SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),`umr_registration`.`patient_age`)), '%Y')+0 AS Age1
,CONCAT(TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, CURDATE()),'Yrs/',LEFT(`umr_registration`.`patient_gender`,1))AS Age ,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`category`,`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`email_id`, CURRENT_TIMESTAMP AS printedon,( `umr_registration`.`createdon`) AS createdon, (SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=`umr_registration`.`createdby` LIMIT 1)AS createdby,(SELECT CONCAT(`username`,' - ',`userid`)  FROM `user_logins` WHERE `userid`=:userid LIMIT 1)AS printedby,`payment_history`.`receiptno` AS receiptno,`payment_history`.`bill_type`,`payment_history`.`amount` AS billamount,`payment_history`.`discount_value`AS discountamount,`payment_history`.`paymentmode` AS paymentmode,`payment_history`.`total`AS total,`branch_master`.`unit` AS display_name, `branch_master`.`address` as addr, CONCAT(`branch_master`.`city`,', ',`branch_master`.`state`,' - ',`branch_master`.`pincode`) AS cit,CONCAT('GST No - ',`branch_master`.`gst_no`) AS gst_no, '' as contact,`umr_registration`.`organization_name`,`organization_code`  FROM `umr_registration`  LEFT JOIN `payment_history` ON `payment_history`.`admissionon`=`umr_registration`.`umrno` LEFT JOIN `branch_master` ON `branch_master`.`cost_center` = `umr_registration`.`branch`  WHERE `umr_registration`.`umrno`LIKE :umrno AND `umr_registration`.`status` ='Visible' AND umr_registration.organization_code IN('MOR0523','MOR0002') GROUP BY `umr_registration`.`umrno` ORDER BY  `umr_registration`.`createdon` DESC LIMIT 15
		  */
}else if($category=='SCHEMES'){
	$sponsor_search=$pdoread->prepare("SELECT `umrno`,CONCAT(UPPER(`patient_name`),' ',UPPER(`middle_name`),' ',UPPER(`last_name`)) AS patientname,DATE_FORMAT(`umr_registration`.`patient_age`,'%Y') AS dob,DATE_FORMAT(`patient_age`,'%d')AS datee,DATE_FORMAT(`patient_age`,'%m')AS month,DATE_FORMAT(`patient_age`,'%Y')AS year, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),`umr_registration`.`patient_age`)), '%Y')+0 AS Age1
	,CONCAT(TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs/',LEFT(`patient_gender`,1)) AS Age ,`patient_gender` AS gender,`mobile_no` AS contactno,`bloodgroup`,`umr_registration`.`category`,`umr_registration`.`country`  AS nationality,`mobile_no`,`umr_registration`.`address`,`umr_registration`.`state`,`umr_registration`.`city`,`umr_registration`.`pincode`,`umr_registration`.`email_id`, CURRENT_TIMESTAMP AS printedon,(CASE WHEN `umr_registration`.`organization_name` IN('No Update','') THEN 'GENERAL' ELSE `umr_registration`.`organization_name` END) AS organization_name,`umr_registration`.`organization_code`  FROM `umr_registration` LEFT JOIN `sponsor_master` ON `sponsor_master`.`organization_code`=`umr_registration`.`organization_code` WHERE umr_registration.status='Visible' AND umr_registration.branch=:cost_center AND umr_registration.category IN ('GOVERNMENT SCHEMES','CORPORATE') AND sponsor_master.status='Active' AND umr_registration.umrno LIKE :umrno GROUP BY `umr_registration`.`umrno` ORDER BY  `umr_registration`.`createdon` DESC LIMIT 5;");
 $sponsor_search->bindValue(':umrno', "%{$umrno}%", PDO::PARAM_STR);
 $sponsor_search->bindParam(':cost_center', $cc['cost_center'], PDO::PARAM_STR);
 $sponsor_search->execute();
 if($sponsor_search -> rowCount() > 0){

http_response_code(200);
    $response['error']=false;
    $response['message']="Data Found";
while($sponsor_details=$sponsor_search->fetch(PDO::FETCH_ASSOC)){
        $response['data'][]=$sponsor_details;
    }
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="No Data Found";
}
	    
}else{
	http_response_code(503);
	$response['error']=true;
	 $response['message']='please select category';
}
} else {
	http_response_code(400);
	$response['error']=true;
	$response['message']='Access Denied';
}
}else {
	http_response_code(400);
	$response['error']=true;
	$response['message']='Some Details are Missing';
}
}
 catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>






