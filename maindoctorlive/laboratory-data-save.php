<?php
function laboratoryworklist($con,$billno,$userid,$patienttype){
$radiology = $con->prepare("INSERT IGNORE INTO `lab_worklist`(`sno`, `trans_id`, `parametercode`, `parametername`, `vaccutainer`, `service_name`, `umrno`, `patient_name`, `gender`, `bill_no`, `age`, `servicecategory`, `service_code`, `group_code`, `created_by`, `created_on`, `modified_by`, `modified_on`, `cost_center`, `requisition`, `bill_date`, `service_type`, `patient_type`, `patient_category`, `bill_status`, `status`,`admission_no`, `track_status`,`barcode`, `print_orders`, `referral_doctorname`) (SELECT NULL,'' AS trans_id,lab.parametercode,lab.parametername,lab.vaccutainer,lab.services,lab.umr_no,TRIM(CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`, ' ',`umr_registration`.`last_name`)) AS patientname,`umr_registration`.`patient_gender`,lab.billno,(CASE WHEN  TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT('0',TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y') WHEN  TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN  CONCAT('0',TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT('0',FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D') ELSE 0 END) AS age,lab.subcategory,lab.servicecode,lab.servicegroupcode,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,lab.costcenter,lab.requisition_no,lab.createdon,lab.sample_type,:patienttype,'OPD','B','A','','Pending','',lab.print_order,lab.refreral_doctor FROM (SELECT `testcode_labparameters`.`parametercode`,`testcode_labparameters`.`parametername`,`testcode_labparameters`.`vaccutainer`,`op_biling_history`.`services`,`op_biling_history`.`umr_no`,`op_biling_history`.`billno`,'AGE',`op_biling_history`.`subcategory`,`op_biling_history`.`servicecode`,`op_biling_history`.`servicegroupcode`,`op_biling_history`.`costcenter`,`op_biling_history`.`requisition_no`,`op_biling_history`.`modifiedon` AS createdon,`testcode_labparameters`.`sample_type`,`testcode_labparameters`.`print_order`,`op_biling_history`.`refreral_doctor` FROM `op_biling_history` LEFT JOIN `testcode_labparameters` ON `testcode_labparameters`.`testcode` = `op_biling_history`.`servicecode` WHERE `billno` = :billno AND TRIM(`op_biling_history`.`category`) = TRIM('LABORATORY') AND TRIM(`op_biling_history`.`subcategory`) != TRIM('PROFILES') AND `op_biling_history`.`status` = 'Visible') AS lab LEFT JOIN `umr_registration` ON `umr_registration`.`umrno` = lab.umr_no)");
	$radiology->bindParam(':billno', $billno, PDO::PARAM_STR);
	$radiology->bindParam(':userid', $userid, PDO::PARAM_STR);
	$radiology->bindParam(':patienttype', $patienttype, PDO::PARAM_STR);
	$radiology->execute();
if($radiology -> rowCount() > 0){
$response = 'Data Saved';
}else{
    $response = 'Data not saved';
}
return $response;
}
function iplaboratoryworklist($con,$admissionno,$userid,$patienttype){
	
	
	$mydata=$con->prepare("SELECT `billing_history`.`billno`,`billing_history`.`requisition_no`,`billing_history`.`servicecode`,`billing_history`.`services`,`billing_history`.`category` ,`billing_history`.`status`,`billing_history`.`subcategory`,`registration`.`cost_center`,`registration`.`consultantname` FROM `billing_history` INNER JOIN `registration` ON `registration`.`admissionno` = `billing_history`.`ipno`  WHERE `ipno`= :admissionno AND  `category` IN ('LABORATORY','WELLNESS') AND `billing_history`.`status`IN('Hold','Transit')");
	$mydata->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
	$mydata->execute();
	if($mydata -> rowCount() > 0){
		
		while($result = $mydata->fetch(PDO::FETCH_ASSOC)){
		if($result['subcategory']=="PROFILES"){
			$mydata1=$con->prepare("SELECT `service_cd`,`service_name` FROM `profile_items` WHERE `branch`=:branch AND `pkg_code`=:servicecode AND  dept_name IN ('BIOCHEMISTRY','BLOOD BANK','CLINICAL PATHOLOGY','SEROLOGY','CYTOGENETICS','CYTOLOGY','GASTROENTEROLOGY','GYNAECOLOGY','HAEMATOLOGY','HISTOPATHOLOGY','MICROBIOLOGY','MOLECULAR BIOLOGY','MOLECULAR BIOLOGY','PULMONOLOGY')");
	$mydata1->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
	$mydata1->bindParam(':servicecode', $result['servicecode'], PDO::PARAM_STR);
	$mydata1->execute();
	if($mydata1 -> rowCount() > 0){
	while($result1 = $mydata1->fetch(PDO::FETCH_ASSOC)){		
		$datalist=$con->prepare("INSERT IGNORE INTO `lab_worklist`(`parametercode`, `parametername`, `vaccutainer`, `service_name`, `umrno`, `patient_name`, `gender`, `bill_no`, `age`, `servicecategory`, `service_code`, `group_code`, `cost_center`, `requisition`, `bill_date`, `service_type`, `patient_type`, `patient_category`, `bill_status`, `status`, `admission_no`, `track_status`,`print_orders`, `referral_doctorname`)  (SELECT `testcode_labparameters`.`parametercode`,`testcode_labparameters`.`parametername`,`testcode_labparameters`.`vaccutainer`,`testcode_labparameters`.`testname`,`billing_history`.`umr_no`,TRIM(CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`, ' ',`umr_registration`.`last_name`)) AS patientname,`umr_registration`.`patient_gender`,`billing_history`.`billno`,(CASE WHEN  TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT('0',TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y') WHEN  TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN  CONCAT('0',TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT('0',FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D') ELSE 0 END) AS age,`billing_history`.`subcategory`,`testcode_labparameters`.`testcode`,`billing_history`.`servicegroupcode`,`billing_history`.`costcenter`,`billing_history`.`requisition_no`,`billing_history`.`modifiedon`,`testcode_labparameters`.`sample_type`,:patienttype,'IPD','B'
,'A',`billing_history`.`ipno`,'Pending',testcode_labparameters.print_order,:consultantname FROM `billing_history` INNER JOIN `testcode_labparameters` ON `testcode_labparameters`.`testcode` =:servicecode INNER JOIN `umr_registration` ON `umr_registration`.`umrno` = billing_history.umr_no WHERE `billing_history`.`requisition_no` =:requisition_no AND TRIM(`billing_history`.`category`) = TRIM('LABORATORY') AND `billing_history`.`status`IN('Hold','Transit'))");
    $datalist->bindParam(':consultantname', $result['consultantname'] , PDO::PARAM_STR);
    $datalist->bindParam(':requisition_no', $result['requisition_no'] , PDO::PARAM_STR);
    $datalist->bindParam(':servicecode',$result1['service_cd'] , PDO::PARAM_STR);
    $datalist->bindParam(':patienttype',$patienttype , PDO::PARAM_STR);
	$datalist->execute();
	}
	}else{
			$response3 = 'Data not saved1';
	}
			}else if($result['subcategory']=="HEALTH CHECK UPS"){
			$mydata1=$con->prepare("SELECT `service_cd`,`service_name` FROM `health_checkup_items` WHERE `branch`=:branch AND `pkg_code`=:servicecode AND  dept_name IN ('BIOCHEMISTRY','BLOOD BANK','CLINICAL PATHOLOGY','SEROLOGY','CYTOGENETICS','CYTOLOGY','GASTROENTEROLOGY','GYNAECOLOGY','HAEMATOLOGY','HISTOPATHOLOGY','MICROBIOLOGY','MOLECULAR BIOLOGY','MOLECULAR BIOLOGY','PULMONOLOGY')");
	$mydata1->bindParam(':branch', $result['costcenter'], PDO::PARAM_STR);
	$mydata1->bindParam(':servicecode', $result['servicecode'], PDO::PARAM_STR);
	$mydata1->execute();
	if($mydata1 -> rowCount() > 0){
	while($result1 = $mydata1->fetch(PDO::FETCH_ASSOC)){		
		$datalist=$con->prepare("INSERT IGNORE INTO `lab_worklist`(`parametercode`, `parametername`, `vaccutainer`, `service_name`, `umrno`, `patient_name`, `gender`, `bill_no`, `age`, `servicecategory`, `service_code`, `group_code`, `cost_center`, `requisition`, `bill_date`, `service_type`, `patient_type`, `patient_category`, `bill_status`, `status`, `admission_no`, `track_status`,`print_orders`,`referral_doctorname`)  (SELECT `testcode_labparameters`.`parametercode`,`testcode_labparameters`.`parametername`,`testcode_labparameters`.`vaccutainer`,`testcode_labparameters`.`testname`,`billing_history`.`umr_no`,TRIM(CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`, ' ',`umr_registration`.`last_name`)) AS patientname,`umr_registration`.`patient_gender`,`billing_history`.`billno`,(CASE WHEN  TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT('0',TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y') WHEN  TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN  CONCAT('0',TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT('0',FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D') ELSE 0 END) AS age,`billing_history`.`subcategory`,`testcode_labparameters`.`testcode`,`billing_history`.`servicegroupcode`,`billing_history`.`costcenter`,`billing_history`.`requisition_no`,`billing_history`.`modifiedon`,`testcode_labparameters`.`sample_type`,:patienttype,'IPD','B'
,'A',`billing_history`.`ipno`,'Pending',testcode_labparameters.print_order,:consultantname FROM `billing_history` INNER JOIN `testcode_labparameters` ON `testcode_labparameters`.`testcode` =:servicecode INNER JOIN `umr_registration` ON `umr_registration`.`umrno` = billing_history.umr_no WHERE `billing_history`.`requisition_no` =:requisition_no AND TRIM(`billing_history`.`category`) = TRIM('WELLNESS') AND `billing_history`.`status`IN('Hold','Transit'))");
	$datalist->bindParam(':consultantname', $result['consultantname'] , PDO::PARAM_STR);
    $datalist->bindParam(':requisition_no', $result['requisition_no'] , PDO::PARAM_STR);
    $datalist->bindParam(':servicecode',$result1['service_cd'] , PDO::PARAM_STR);
    $datalist->bindParam(':patienttype',$patienttype , PDO::PARAM_STR);
	$datalist->execute();
	}
	}else{
			$response3 = 'Data not saved1';
	}
			}
			else{
				$datalist=$con->prepare("INSERT INTO `lab_worklist`(`sno`, `trans_id`, `parametercode`, `parametername`, `vaccutainer`, `service_name`, `umrno`, `patient_name`, `gender`, `bill_no`, `age`, `servicecategory`, `service_code`, `group_code`, `created_by`, `created_on`, `modified_by`, `modified_on`, `cost_center`, `requisition`, `bill_date`, `service_type`, `patient_type`, `patient_category`, `bill_status`, `status`, `admission_no`, `track_status`, `barcode`, `print_orders`, `referral_doctorname`)  (SELECT NULL,'' AS trans_id,lab.parametercode,lab.parametername,lab.vaccutainer,lab.services,lab.umr_no,TRIM(CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`, ' ',`umr_registration`.`last_name`)) AS patientname,`umr_registration`.`patient_gender`,lab.billno,(CASE WHEN  TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT('0',TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y') WHEN  TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN  CONCAT('0',TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT('0',FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D') ELSE 0 END) AS age,lab.subcategory,lab.servicecode,lab.servicegroupcode,lab.userid,lab.createdon,lab.userid,lab.createdon,lab.costcenter,lab.requisition_no,lab.createdon,lab.sample_type,:patienttype,'IPD','B','A',lab.ipno,'Pending','',lab.print_order,lab.refreral_doctor FROM (SELECT `testcode_labparameters`.`parametercode`,`testcode_labparameters`.`parametername`,`testcode_labparameters`.`vaccutainer`,`billing_history`.`services`,`billing_history`.`umr_no`,`billing_history`.`billno`,`billing_history`.`subcategory`,`billing_history`.`servicecode`,`billing_history`.`servicegroupcode`,`billing_history`.`costcenter`,`billing_history`.`requisition_no`,`billing_history`.`modifiedon` AS createdon,`testcode_labparameters`.`sample_type`,`testcode_labparameters`.`print_order`,:consultantname AS refreral_doctor,`billing_history`.`ipno`,`billing_history`.`createdby` AS userid FROM `billing_history` LEFT JOIN `testcode_labparameters` ON `testcode_labparameters`.`testcode` = `billing_history`.`servicecode` WHERE `requisition_no` = :requisition_no AND TRIM(`category`) = TRIM('LABORATORY') AND TRIM(`billing_history`.`subcategory`) != TRIM('PROFILES') AND `billing_history`.`status` IN('Hold','Transit')) AS lab LEFT JOIN `umr_registration` ON `umr_registration`.`umrno` = lab.umr_no)");
	$datalist->bindParam(':consultantname', $result['consultantname'] , PDO::PARAM_STR);
    $datalist->bindParam(':requisition_no', $result['requisition_no'] , PDO::PARAM_STR);
    //$datalist->bindParam(':servicecode',$result['servicecode'] , PDO::PARAM_STR);
    $datalist->bindParam(':patienttype',$patienttype , PDO::PARAM_STR);
	$datalist->execute();
			}
		
		if($datalist -> rowCount() > 0){
	$response3 = 'Data Saved';
	}else{
		$response3 = 'Data not saved1';
	}
} 
	}else{
		$response3 ='Data not saved';
	}
	
	/* $radiology = $con->prepare("INSERT IGNORE INTO `lab_worklist`(`sno`, `trans_id`, `parametercode`, `parametername`, `vaccutainer`, `service_name`, `umrno`, `patient_name`, `gender`, `bill_no`, `age`, `servicecategory`, `service_code`, `group_code`, `created_by`, `created_on`, `modified_by`, `modified_on`, `cost_center`, `requisition`, `bill_date`, `service_type`, `patient_type`, `patient_category`, `bill_status`, `status`, `admission_no`, `track_status`) (SELECT NULL,'' AS trans_id,lab.parametercode,lab.parametername,lab.vaccutainer,lab.services,lab.umr_no,TRIM(CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`, ' ',`umr_registration`.`last_name`)) AS patientname,`umr_registration`.`patient_gender`,lab.billno,(CASE WHEN  TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT('0',TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y') WHEN  TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN  CONCAT('0',TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT('0',FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D') ELSE 0 END) AS age,lab.subcategory,lab.servicecode,lab.servicegroupcode,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,lab.costcenter,lab.requisition_no,lab.createdon,lab.sample_type,:patienttype,'IPD','B','A',lab.ipno,'Pending' FROM (SELECT `testcode_labparameters`.`parametercode`,`testcode_labparameters`.`parametername`,`testcode_labparameters`.`vaccutainer`,`billing_history`.`services`,`billing_history`.`umr_no`,`billing_history`.`billno`,'AGE',`billing_history`.`subcategory`,`billing_history`.`servicecode`,`billing_history`.`servicegroupcode`,`billing_history`.`costcenter`,`billing_history`.`requisition_no`,`billing_history`.`createdon`,`testcode_labparameters`.`sample_type`,`billing_history`.`ipno` FROM `billing_history` LEFT JOIN `testcode_labparameters` ON `testcode_labparameters`.`testcode` = `billing_history`.`servicecode` WHERE `billing_history`.`requisition_no` = :admissionno AND `billing_history`.`status` = 'Hold' AND TRIM(`billing_history`.`category`) = TRIM('LABORATORY')) AS lab LEFT JOIN `umr_registration` ON `umr_registration`.`umrno` = lab.umr_no)");
		$radiology->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
		$radiology->bindParam(':userid', $userid, PDO::PARAM_STR);
		$radiology->bindParam(':patienttype', $patienttype, PDO::PARAM_STR);
		$radiology->execute();
	if($radiology -> rowCount() > 0){
	$response3 = 'Data Saved';
	}else{
		$response3 = 'Data not saved';
	} */
	return $response3;
	}
	
	function healthchecklab($con,$billno,$userid,$patienttype,$branch){
		$radiology = $con->prepare("INSERT IGNORE INTO `lab_worklist`(`sno`, `trans_id`, `parametercode`, `parametername`, `vaccutainer`, `service_name`, `umrno`, `patient_name`, `gender`, `bill_no`, `age`, `servicecategory`, `service_code`, `group_code`, `created_by`, `created_on`, `modified_by`, `modified_on`, `cost_center`, `requisition`, `bill_date`, `service_type`, `patient_type`, `patient_category`, `bill_status`, `status`, `admission_no`, `track_status`, `barcode`,print_orders, `referral_doctorname`) (SELECT NULL,'',LAB.parametercode,LAB.parametername,LAB.vaccutainer,LAB.service_name,LAB.umr_no,TRIM(CONCAT(`umr_registration`.`title`,'. ',`umr_registration`.`patient_name`,' ',`umr_registration`.`middle_name`, ' ',`umr_registration`.`last_name`)) AS patientname,`umr_registration`.`patient_gender`,LAB.billno,(CASE WHEN TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now())!='0' THEN CONCAT('0',TIMESTAMPDIFF(YEAR, `umr_registration`.`patient_age`, now()),'Y') WHEN TIMESTAMPDIFF( MONTH,`umr_registration`.`patient_age`, now() ) % 12 !='0' THEN CONCAT('0',TIMESTAMPDIFF( MONTH, `umr_registration`.`patient_age`, now() ) % 12,'M') WHEN FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 )!='0' THEN CONCAT('0',FLOOR( TIMESTAMPDIFF( DAY, `umr_registration`.`patient_age`, now() ) % 30.4375 ),'D') ELSE 0 END) AS age,LAB.service_department,LAB.service_code,LAB.service_group_code,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,LAB.costcenter,LAB.requisition_no,LAB.createdon,LAB.sample_type,:patienttype,'OPD','B','A','','Pending','',LAB.print_order,LAB.refreral_doctor FROM (SELECT NULL, '',`testcode_labparameters`.`parametercode`,`testcode_labparameters`.`parametername`,`testcode_labparameters`.`vaccutainer`,`package_list`.`service_name`,`op_biling_history`.`umr_no`,`op_biling_history`.`billno`,`package_list`.`service_department`,`package_list`.`service_code`,IFNULL(`services_master`.`service_group_code`,'') AS service_group_code,`op_biling_history`.`costcenter`,`op_biling_history`.`requisition_no`,`op_biling_history`.`modifiedon` AS createdon,`testcode_labparameters`.`sample_type`,`op_biling_history`.`patient_type`,`testcode_labparameters`.`print_order`,`op_biling_history`.`refreral_doctor`,`op_biling_history`.`modifiedby`,`op_biling_history`.`modifiedon` FROM `op_biling_history` INNER JOIN `package_list` ON `package_list`.`package_id` = `op_biling_history`.`servicecode` AND `op_biling_history`.`category` IN ('LABORATORY','HEALTH CHECK UPS','HEALTH CHECKUP') AND `op_biling_history`.`subcategory` IN ('PROFILES','HEALTH CHECK UPS','HEALTH CHECKUP') AND `package_list`.`status` = 'Active' AND `package_list`.`branch` =:branch AND `op_biling_history`.`status` = 'Visible' LEFT JOIN `testcode_labparameters` ON `package_list`.`service_code` = `testcode_labparameters`.`testcode` LEFT JOIN `services_master` ON `package_list`.`package_id` = `services_master`.`service_code` WHERE `op_biling_history`.`billno` = :billno AND `package_list`.`service_department` NOT IN ('RADIOLOGY','CARDIOLOGY')) AS LAB INNER JOIN `umr_registration` ON `umr_registration`.`umrno` = LAB.umr_no  WHERE LAB.parametercode IS NOT NULL)");
			$radiology->bindParam(':billno', $billno, PDO::PARAM_STR);
			$radiology->bindParam(':userid', $userid, PDO::PARAM_STR);
			$radiology->bindParam(':patienttype', $patienttype, PDO::PARAM_STR);
			$radiology->bindParam(':branch', $branch, PDO::PARAM_STR);
			$radiology->execute();
		if($radiology -> rowCount() > 0){
		$response4 = 'Data Saved';
		}else{
			$response4 = 'Data not saved';
		}
		return $response4;
		}
?>