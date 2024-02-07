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
$admissionno = strtoupper($data->admissionno);
$category = strtoupper($data->category);
$subcategory = strtoupper($data->subcategory);
$servicecode = strtoupper($data->servicecode);
$service = str_ireplace("'","",strtoupper($data->service));
$servicestatus = strtoupper($data->servicestatus);
$hsn_sac = strtoupper($data->hsn_sac);
$cp_sno = strtoupper($data->cp_sno);
$discount = 0;
$discatgeory = 'PERCENTAGE';
$quantity = (int) ($data->quantity);
$rate = (double) ROUND(($data->rate),2);
$total = ROUND(($quantity*$rate),2);
$ipaddress = $_SERVER['REMOTE_ADDR'];
try {
if(!empty($accesskey) && !empty($admissionno) && !empty($category) && !empty($subcategory) && !empty($servicecode) && !empty($service) && !empty($quantity) && !empty($rate) && !empty($cp_sno)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$costcenter = $result['cost_center'];
if($check -> rowCount() > 0){
//check ip status
$check_ip=$pdoread->prepare("SELECT `registration`.`admissionno`,`registration`.`umrno`,`registration`.`billno`,`sponsor_master`.`default_tariff` FROM `registration` LEFT JOIN `sponsor_master` ON `registration`.`organization_code` = `sponsor_master`.`organization_code` WHERE `registration`.`admissionstatus`='Admitted' AND `registration`.`status`='Visible' AND `registration`.`cost_center`= :branch AND `registration`.`admissionno`= :ipno LIMIT 1");
$check_ip->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
$check_ip->bindParam(':branch', $costcenter, PDO::PARAM_STR);
$check_ip->execute();
if($check_ip->rowCount() > 0){
$check_ip_res = $check_ip->fetch(PDO::FETCH_ASSOC);
$crosscheck = $pdoread->prepare("SELECT (CASE WHEN `service_category` IN ('LABORATORY') AND `service_sub_category` IN ('PROFILES') THEN 'PACKAGEBILL' ELSE 'OPENBILL' END) AS category FROM `services_master` WHERE `service_code` = (SELECT `service_code` FROM `tariff_master` WHERE `sno` = :cp_sno) AND `service_category` NOT IN ('HEALTH CHECK UPS')");	
$crosscheck->bindParam(':cp_sno', $cp_sno, PDO::PARAM_STR);
$crosscheck->execute();
if($crosscheck->rowCount() > 0){
	$resultadmno = $crosscheck->fetch(PDO::FETCH_ASSOC);
	if($resultadmno['category'] == 'PACKAGEBILL'){
		$check_item=$pdoread->prepare("SELECT `ipno` FROM `billing_history` WHERE `status` = 'Hold' AND `ipno`=:ipno AND  `bill_servicecode`=:servicecode");
$check_item->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
$check_item->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$check_item -> execute();
if($check_item-> rowCount() > 0){
	$response['error']=true;
	$response['message']="Profile is already added";
}else{
	$notnullfetch = $pdoread->prepare("SELECT NULL,'NORMAL' AS patient_type,:ipno AS ipno,M.tariff_name,M.tariff_code,M.tariff_category AS chargetype,:umrno AS umrno,:billno AS billno,'' AS reqno,`services_master`.`billing_head`,`services_master`.`service_group_code`,`services_master`.`service_category`,`services_master`.`service_sub_category`,M.ward_name,`services_master`.`service_code`,`services_master`.`services_name`,'','',IFNULL(M.itemcost,'check') AS cash_rate,'1' AS qty,M.itemcost AS rate,M.itemcost AS total,'PERCENTAGE' AS discounttype,0 AS discountper,0 AS discountvalue,M.itemcost AS afterrate,M.itemcost AS aftertotal,0,0,0,0,'CREDIT',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'',:ipaddress,CURRENT_TIMESTAMP,'No Update','No Update','Hold',`services_master`.`vaccutainer`,'',CURRENT_TIMESTAMP,:branch AS branch,M.display_code,M.display_name,M.sno AS serialno,'',0,M.billsercode,M.billsername,M.ward_charges from (SELECT * FROM (SELECT `tariff_master`.`tariff_name`,`tariff_master`.`display_name`,`tariff_master`.`display_code`,`tariff_master`.`sno`,F.service_code,F.service_name,`tariff_master`.`ward_charges` AS itemcost,F.tariff_category,F.tariff_code,F.ward_name,F.ward_charges,F.billsercode,F.billsername,F.serialno FROM (SELECT `package_list`.`service_code`,`package_list`.`service_name`,E.`service_code` AS billsercode,E.`service_name` AS billsername,E.ward_charges,E.`tariff_name`,E.`tariff_code`,E.`tariff_category`,E.ward_name,E.serialno FROM (SELECT `tariff_master`.`sno` AS serialno,`tariff_master`.`service_code`,`tariff_master`.`ward_name`,`tariff_master`.`service_name`,`tariff_master`.`ward_charges`,`tariff_master`.`tariff_category`,`tariff_master`.`tariff_name`,`tariff_master`.`tariff_code` FROM `tariff_master` WHERE `tariff_master`.`sno` = :cp_sno) AS E LEFT JOIN `package_list` ON E.service_code = `package_list`.`package_id` WHERE `package_list`.`status` = 'Active' AND `package_list`.`branch` = :branch) AS F LEFT JOIN `tariff_master` ON `tariff_master`.`tariff_code` = F.tariff_code AND `tariff_master`.`ward_name` = F.ward_name AND `tariff_master`.`service_code` = F.service_code) AS D) AS M LEFT JOIN `services_master` ON `services_master`.`service_code` = M.service_code WHERE M.sno IS NOT NULL AND `services_master`.`service_category` != 'CONSULTATION'");
	$notnullfetch->bindParam(':cp_sno', $cp_sno, PDO::PARAM_STR);
	$notnullfetch->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	//$notnullfetch->bindParam(':requestion', $requestion, PDO::PARAM_STR);
	$notnullfetch->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
	$notnullfetch->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$notnullfetch->bindParam(':umrno', $check_ip_res['umrno'], PDO::PARAM_STR);
	$notnullfetch->bindParam(':billno', $check_ip_res['billno'], PDO::PARAM_STR);
	$notnullfetch->bindParam(':branch', $costcenter, PDO::PARAM_STR);
	$notnullfetch->execute();
	//insert Matched items with Primary Tariff code
	while($notnullres = $notnullfetch->fetch(PDO::FETCH_ASSOC)){
		$requestionno = $pdoread -> prepare("SELECT Concat('IPRQ',DATE_FORMAT(CURRENT_DATE,'%y%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`requisition_no`),'00000'),Concat('IPRQ',DATE_FORMAT(CURRENT_DATE,'%y%m')),-1)+1),'6','0')) AS transid FROM `billing_history` WHERE `requisition_no` like'%IPRQ%'");
		$requestionno -> execute();
		$requestionres = $requestionno->fetch(PDO::FETCH_ASSOC);
		$requestion =  $requestionres['transid'];
	$notnullitems = $pdo4->prepare("INSERT IGNORE INTO `billing_history`(`sno`, `patient_type`, `ipno`, `tariff_category`, `tariff_code`, `charges_type`, `umr_no`, `billno`, `requisition_no`, `billinghead`, `servicegroupcode`, `category`, `subcategory`, `ward_type`, `servicecode`, `services`, `servicedoctor`, `hsn_sac`, `cash_rate`, `quantity`, `rate`, `total`, `discounttype`, `discount`, `discountvalue`, `afterrate`, `aftertotal`, `bill_included_package`, `bill_excluded_package`, `package_discount`, `pacakge_total`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `remarks`, `ipaddress`, `acknowledge_date`, `acknowledge_by`, `service_status`, `status`, `color_top`, `accepted_by`, `accepted_date`, `costcenter`, `displaycode`, `displayname`, `tariff_sno`, `service_type`, `is_cash`, `bill_servicecode`, `bill_servicename`, `bill_rate`) VALUES (NULL,'Normal',:ipno,:tariff_name,:tariff_code,:chargetype,:umrno,:billno,:requestion,:billing_head,:service_group_code,:service_category,:service_sub_category,:ward_name,:service_code,:services_name,'','',:itemcost,'1',:itemcost,:itemcost,'PERCENTAGE','0','0',:itemcost,:itemcost,0,0,0,0,'CREDIT',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'',:ipaddress,CURRENT_TIMESTAMP,'No Update','No Update','Hold',:vaccutainer,'',CURRENT_TIMESTAMP,:branch,:display_code,:display_name,:serialno,'',0,:billsercode,:billsername,:ward_charges)");
	$notnullitems->bindParam(':display_code', $notnullres['display_code'], PDO::PARAM_STR);
	$notnullitems->bindParam(':display_name', $notnullres['display_name'], PDO::PARAM_STR);
	$notnullitems->bindParam(':serialno', $notnullres['serialno'], PDO::PARAM_STR);
	$notnullitems->bindParam(':tariff_name', $notnullres['tariff_name'], PDO::PARAM_STR);
	$notnullitems->bindParam(':tariff_code', $notnullres['tariff_code'], PDO::PARAM_STR);
	$notnullitems->bindParam(':chargetype', $notnullres['chargetype'], PDO::PARAM_STR);
	$notnullitems->bindParam(':service_code', $notnullres['service_code'], PDO::PARAM_STR);
	$notnullitems->bindParam(':services_name', $notnullres['services_name'], PDO::PARAM_STR);
	$notnullitems->bindParam(':ward_name', $notnullres['ward_name'], PDO::PARAM_STR);
	$notnullitems->bindParam(':service_category', $notnullres['service_category'], PDO::PARAM_STR);
	$notnullitems->bindParam(':service_sub_category', $notnullres['service_sub_category'], PDO::PARAM_STR);
	$notnullitems->bindParam(':service_group_code', $notnullres['service_group_code'], PDO::PARAM_STR);
	$notnullitems->bindParam(':vaccutainer', $notnullres['vaccutainer'], PDO::PARAM_STR);
	$notnullitems->bindParam(':billing_head', $notnullres['billing_head'], PDO::PARAM_STR);
	$notnullitems->bindParam(':billsercode', $notnullres['billsercode'], PDO::PARAM_STR);
	$notnullitems->bindParam(':billsername', $notnullres['billsername'], PDO::PARAM_STR);
	$notnullitems->bindParam(':ward_charges', $notnullres['ward_charges'], PDO::PARAM_STR);
	$notnullitems->bindParam(':itemcost', $notnullres['rate'], PDO::PARAM_STR);
	$notnullitems->bindParam(':requestion', $requestion, PDO::PARAM_STR);
	$notnullitems->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
	$notnullitems->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$notnullitems->bindParam(':umrno', $check_ip_res['umrno'], PDO::PARAM_STR);
	$notnullitems->bindParam(':billno', $check_ip_res['billno'], PDO::PARAM_STR);
	$notnullitems->bindParam(':branch', $costcenter, PDO::PARAM_STR);
	$notnullitems->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	$notnullitems->execute();
	$notnullcount = $notnullitems->rowCount();
}
	//null items fetch
	$nullitems = $pdoread->prepare("SELECT NULL,'NORMAL' AS patient_type,:ipno AS ipno,M.tariff_name,M.tariff_code,M.tariff_category AS chargetype,:umrno AS umrno,:billno AS billno,:requestion AS reqno,`services_master`.`billing_head`,`services_master`.`service_group_code`,`services_master`.`service_category`,`services_master`.`service_sub_category`,M.ward_name,`services_master`.`service_code`,`services_master`.`services_name`,'','',IFNULL(M.itemcost,'check') AS cash_rate,'1' AS qty,M.itemcost AS rate,M.itemcost AS total,'PERCENTAGE' AS discounttype,0 AS discountper,0 AS discountvalue,M.itemcost AS afterrate,M.itemcost AS aftertotal,0,0,0,0,'CREDIT',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'',:ipaddress,CURRENT_TIMESTAMP,'No Update','No Update','Hold',`services_master`.`vaccutainer`,'',CURRENT_TIMESTAMP,:branch AS branch,M.display_code,M.display_name,M.sno AS serialno,'',0,M.billsercode,M.billsername,M.ward_charges from (SELECT * FROM (SELECT `tariff_master`.`tariff_name`,`tariff_master`.`display_name`,`tariff_master`.`display_code`,`tariff_master`.`sno`,F.service_code,F.service_name,`tariff_master`.`ward_charges` AS itemcost,F.tariff_category,F.tariff_code,F.ward_name,F.ward_charges,F.billsercode,F.billsername,F.serialno FROM (SELECT `package_list`.`service_code`,`package_list`.`service_name`,E.`service_code` AS billsercode,E.`service_name` AS billsername,E.ward_charges,E.`tariff_name`,E.`tariff_code`,E.`tariff_category`,E.ward_name,E.serialno FROM (SELECT `tariff_master`.`sno` AS serialno,`tariff_master`.`service_code`,`tariff_master`.`ward_name`,`tariff_master`.`service_name`,`tariff_master`.`ward_charges`,`tariff_master`.`tariff_category`,`tariff_master`.`tariff_name`,`tariff_master`.`tariff_code` FROM `tariff_master` WHERE `tariff_master`.`sno` = :cp_sno) AS E LEFT JOIN `package_list` ON E.service_code = `package_list`.`package_id` WHERE `package_list`.`status` = 'Active' AND `package_list`.`branch` = :branch) AS F LEFT JOIN `tariff_master` ON `tariff_master`.`tariff_code` = F.tariff_code AND `tariff_master`.`ward_name` = F.ward_name AND `tariff_master`.`service_code` = F.service_code) AS D) AS M LEFT JOIN `services_master` ON `services_master`.`service_code` = M.service_code WHERE M.sno IS NULL  AND `services_master`.`service_category` != 'CONSULTATION'");
	$nullitems->bindParam(':cp_sno', $cp_sno, PDO::PARAM_STR);
	$nullitems->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	$nullitems->bindParam(':requestion', $requestion, PDO::PARAM_STR);
	$nullitems->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
	$nullitems->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$nullitems->bindParam(':umrno', $check_ip_res['umrno'], PDO::PARAM_STR);
	$nullitems->bindParam(':billno', $check_ip_res['billno'], PDO::PARAM_STR);
	$nullitems->bindParam(':branch', $costcenter, PDO::PARAM_STR);
	$nullitems->execute();
	$nullcount = 0;
	while($nullitemsres = $nullitems->fetch(PDO::FETCH_ASSOC)){
		$requestionno = $pdoread -> prepare("SELECT Concat('IPRQ',DATE_FORMAT(CURRENT_DATE,'%y%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`requisition_no`),'00000'),Concat('IPRQ',DATE_FORMAT(CURRENT_DATE,'%y%m')),-1)+1),'6','0')) AS transid FROM `billing_history` WHERE `requisition_no` like'%IPRQ%'");
		$requestionno -> execute();
		$requestionres = $requestionno->fetch(PDO::FETCH_ASSOC);
		$requestion =  $requestionres['transid'];
		$balance = $pdo4->prepare("INSERT INTO `billing_history`(`sno`, `patient_type`, `ipno`, `tariff_category`, `tariff_code`, `charges_type`, `umr_no`, `billno`, `requisition_no`, `billinghead`, `servicegroupcode`, `category`, `subcategory`, `ward_type`, `servicecode`, `services`, `servicedoctor`, `hsn_sac`, `cash_rate`, `quantity`, `rate`, `total`, `discounttype`, `discount`, `discountvalue`, `afterrate`, `aftertotal`, `bill_included_package`, `bill_excluded_package`, `package_discount`, `pacakge_total`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `remarks`, `ipaddress`, `acknowledge_date`, `acknowledge_by`, `service_status`, `status`, `color_top`, `accepted_by`, `accepted_date`, `costcenter`, `displaycode`, `displayname`, `tariff_sno`, `service_type`, `is_cash`, `bill_servicecode`, `bill_servicename`, `bill_rate`) (SELECT NULL,'NORMAL' AS patient_type,:ipno AS ipno,`tariff_master`.`tariff_name`,`tariff_master`.`tariff_code`,`tariff_master`.`tariff_category` AS chargetype,:umrno AS umrno,:billno AS billno,:requestion AS reqno,:billing_head,:service_group_code,:service_category,:service_sub_category,:ward_name,:service_code,:services_name,'','',IFNULL(`tariff_master`.`ward_charges`,'0') AS cash_rate,'1' AS qty,IFNULL(`tariff_master`.`ward_charges`,'0') AS total,'PERCENTAGE' AS discounttype,0 AS discountper,0 AS discountvalue,IFNULL(`tariff_master`.`ward_charges`,'0') AS afterrate,IFNULL(`tariff_master`.`ward_charges`,'0') AS aftertotal,0,0,0,0,'CREDIT',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'',:ipaddress,CURRENT_TIMESTAMP,'No Update','No Update','Hold',:vaccutainer,'No Update',CURRENT_TIMESTAMP,:branch AS branch,`tariff_master`.`display_code`,`tariff_master`.`display_name`,`tariff_master`.`sno` AS serialno,'',0,:billsercode,:billsername,:ward_charges FROM `tariff_master` WHERE `tariff_code` LIKE :default_tariff AND `service_code` LIKE :service_code AND `ward_name` LIKE :ward_name LIMIT 1)");
		$balance->bindParam(':default_tariff', $check_ip_res['default_tariff'], PDO::PARAM_STR);
	$balance->bindParam(':service_code', $nullitemsres['service_code'], PDO::PARAM_STR);
	$balance->bindParam(':services_name', $nullitemsres['services_name'], PDO::PARAM_STR);
	$balance->bindParam(':ward_name', $nullitemsres['ward_name'], PDO::PARAM_STR);
	$balance->bindParam(':service_category', $nullitemsres['service_category'], PDO::PARAM_STR);
	$balance->bindParam(':service_sub_category', $nullitemsres['service_sub_category'], PDO::PARAM_STR);
	$balance->bindParam(':service_group_code', $nullitemsres['service_group_code'], PDO::PARAM_STR);
	$balance->bindParam(':vaccutainer', $nullitemsres['vaccutainer'], PDO::PARAM_STR);
	$balance->bindParam(':billing_head', $nullitemsres['billing_head'], PDO::PARAM_STR);
	$balance->bindParam(':billsercode', $nullitemsres['billsercode'], PDO::PARAM_STR);
	$balance->bindParam(':billsername', $nullitemsres['billsername'], PDO::PARAM_STR);
	$balance->bindParam(':ward_charges', $nullitemsres['ward_charges'], PDO::PARAM_STR);
	$balance->bindParam(':requestion', $requestion, PDO::PARAM_STR);
	$balance->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
	$balance->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
	$balance->bindParam(':umrno', $check_ip_res['umrno'], PDO::PARAM_STR);
	$balance->bindParam(':billno', $check_ip_res['billno'], PDO::PARAM_STR);
	$balance->bindParam(':branch', $costcenter, PDO::PARAM_STR);
	$balance->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
	$balance->execute();
	$nullcount = $balance->rowCount();
	}
	if($nullcount > 0 || $notnullcount > 0){
			$response['error']=false;
			$response['message']= "Service is added";
			$response['admissionno']= $admissionno;
		}else{
			$response['error']=true;
			$response['message']="Sorry! Please try again";
			$response['admissionno']= $admissionno;
		}

}
	}else{
			// Open BIlling
		//check if bill already exists
$check_item=$pdoread->prepare("SELECT `ipno` FROM `billing_history` WHERE `status`='Hold' AND `ipno`=:ipno AND  `servicecode`=:servicecode ");
$check_item->bindParam(':ipno', $admissionno, PDO::PARAM_STR);
$check_item->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$check_item -> execute();
if($check_item-> rowCount() > 0){
	$response['error']=true;
	$response['message']="Service Already Added to Bill";
}else{

//Generate Bill number Start
$admissioncheck = $pdoread -> prepare("SELECT `billno` AS billno,`registration`.`admission_type`,`registration`.`admissionno`,`registration`.`umrno`,`patient_category` FROM `registration`  WHERE `admissionno` = :admissionno");
$admissioncheck->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$admissioncheck -> execute();
$resultadmno = $admissioncheck->fetch(PDO::FETCH_ASSOC);
$billno = $resultadmno['billno'];
$admission_type = $resultadmno['admission_type'];
$umrno = $resultadmno['umrno'];
$patienttype = $resultadmno['patient_category'];
$requestionno = $pdoread -> prepare("SELECT Concat('IPRQ',DATE_FORMAT(CURRENT_DATE,'%y%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`requisition_no`),'00000'),Concat('IPRQ',DATE_FORMAT(CURRENT_DATE,'%y%m')),-1)+1),'6','0')) AS transid FROM `billing_history` WHERE `requisition_no` like'%IPRQ%'");
$requestionno -> execute();
$requestionres = $requestionno->fetch(PDO::FETCH_ASSOC);
$requestion =  $requestionres['transid'];
//Generate Bill number End
// Create Registration
$saleprice = $pdo4 -> prepare("INSERT IGNORE INTO `billing_history`(`sno`, `patient_type`, `ipno`, `tariff_category`, `tariff_code`, `charges_type`, `umr_no`, `billno`, `requisition_no`, `billinghead`, `servicegroupcode`, `category`, `subcategory`, `ward_type`, `servicecode`, `services`, `hsn_sac`,`cash_rate`,`quantity`, `rate`, `total`, `discounttype`, `discount`, `discountvalue`, `afterrate`, `aftertotal`, `bill_included_package`, `bill_excluded_package`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `remarks`, `ipaddress`, `acknowledge_date`, `acknowledge_by`, `service_status`, `status`, `color_top`,costcenter,`bill_servicecode`, `bill_servicename`, `bill_rate`) (SELECT NULL ,:admission_type,:admissionno,`tariff_master`.`tariff_name`,`tariff_master`.`tariff_code`,`tariff_master`.`tariff_category`,:umrno,:billno,:requisition_no,IFNULL((SELECT `billing_head` FROM `services_master` WHERE `service_code` = `tariff_master`.`service_code` LIMIT 1),'Investigation Charges') AS billing_head,IFNULL((SELECT `service_group_code` FROM `services_master` WHERE `service_code` = `tariff_master`.`service_code` LIMIT 1),'') AS service_group_code,IFNULL((SELECT `service_category` FROM `services_master` WHERE `service_code` = `tariff_master`.`service_code` LIMIT 1),'--') AS category,IFNULL((SELECT `service_sub_category` FROM `services_master` WHERE `service_code` = `tariff_master`.`service_code` LIMIT 1),'--') AS service_sub_category,`tariff_master`.`ward_name`,`tariff_master`.`service_code`,`tariff_master`.`service_name`,'',`tariff_master`.`ward_charges`,'1',`tariff_master`.`ward_charges`,`tariff_master`.`ward_charges`,:discounttype,(CASE WHEN :discounttype = 'PERCENTAGE' THEN :discount ELSE ROUND((:discount*100)/`tariff_master`.`ward_charges`,2) END),(CASE WHEN :discounttype = 'VALUE' THEN :discount ELSE ROUND((:discount/100)*`tariff_master`.`ward_charges`,2) END),(CASE WHEN :discounttype = 'PERCENTAGE' THEN ROUND(`tariff_master`.`ward_charges`*((100-:discount)/100),2) ELSE ROUND((`tariff_master`.`ward_charges`-:discount),2) END),(CASE WHEN :discounttype = 'PERCENTAGE' THEN ROUND(`tariff_master`.`ward_charges`*((100-:discount)/100),2) ELSE ROUND((`tariff_master`.`ward_charges`-:discount),2) END),0,0,'CREDIT',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'',:ipaddress,CURRENT_TIMESTAMP,'No Update','No Update','Hold',IFNULL((SELECT `vaccutainer` FROM `services_master` WHERE `service_code` = `tariff_master`.`service_code` LIMIT 1),'') AS color_top,:cost_center,`tariff_master`.`service_code`,`tariff_master`.`service_name`,`tariff_master`.`ward_charges` FROM `tariff_master` WHERE `sno` = :cp_sno LIMIT 1)");
$saleprice->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
$saleprice->bindParam(':billno', $billno, PDO::PARAM_STR);
$saleprice->bindParam(':admission_type', $admission_type, PDO::PARAM_STR);
$saleprice->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$saleprice->bindParam(':discount', $discount, PDO::PARAM_STR);
$saleprice->bindParam(':discounttype', $discatgeory, PDO::PARAM_STR);
$saleprice->bindParam(':cp_sno', $cp_sno, PDO::PARAM_STR);
$saleprice->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
$saleprice->bindParam(':requisition_no', $requestion, PDO::PARAM_STR);
$saleprice->bindParam(':cost_center', $costcenter, PDO::PARAM_STR);
$saleprice->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$saleprice -> execute();
$lastid = $pdo->lastInsertId();
if($saleprice -> rowCount() > 0){
	$track = $pdo4 -> prepare("INSERT IGNORE INTO `billing_history_track`(`sno`, `billing_id`, `bill_no`, `requisition_no`, `service_code`, `service_name`, `quantity`, `track_status`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `status`) VALUES (NULL,:lastid,:billno,'',:servicecode,:services,:quantity,'Bill Generated',:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Hold')");
$track->bindParam(':lastid', $lastid, PDO::PARAM_STR);
$track->bindParam(':billno', $billno, PDO::PARAM_STR);
$track->bindParam(':quantity', $quantity, PDO::PARAM_STR);
$track->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$track->bindParam(':services', $service, PDO::PARAM_STR);
$track->bindParam(':userid', $result['userid'] , PDO::PARAM_STR);
$track -> execute();
           http_response_code(200);
		$response['error']=false;
	$response['message']= "Service is added";
	$response['admissionno']= $admissionno;
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Sorry! Please try again";
	$response['admissionno']= $admissionno;
}
//
}
		//Open Billing
	}
}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Please choose proper investigation";
}

}else{
	http_response_code(503);
	$response['error']=true;
	$response['message']="Please Check Patient Status";
}
}else{
	http_response_code(400);
	$response['error']=true;
	$response['message']="Access Denied";
}
	//
}else{
	http_response_code(400);
	$response['error']=true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] =true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>