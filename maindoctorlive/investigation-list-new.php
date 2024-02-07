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
$accesskey = $data->accesskey;
$category = strtoupper($data->category);
//$subcategory = strtoupper($data->subcategory);
$ward = strtoupper($data->ward);
$searchterm = trim($data->searchterm);
$umrno = trim($data->umrno);

try {

if(!empty($accesskey) && !empty($category) && !empty($ward) && !empty($searchterm)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//Generate Admission number Start

if($category=="CONSULTATION"){
	//check on umr
	$findcat=$pdoread->prepare("SELECT  `umrno`, `billno`, `admissionno`, `admissionstatus`,`organization_name`, `organization_code` FROM `registration` WHERE `umrno`=:umrno AND `status`='Visible' GROUP BY `umrno` LIMIT 1 ");
	$findcat->bindParam(':umrno', $umrno, PDO::PARAM_STR);
	$findcat->execute();
   $cat=$findcat->fetch(PDO::FETCH_ASSOC);
   $ORGNAME=$cat['organization_name'];
   $ORGCODE=$cat['organization_code'];
   // if exists get organisation code and orgname
	$org=$pdoread->prepare("SELECT  `category`, `tariff_code`, `secondary_tariff`, `organization_code`, `organization_name`, `organization_address` FROM `sponsor_master` WHERE `status`='Active' AND `organization_code`=:organizationcode ");
	$org->bindParam(':organizationcode', $ORGCODE, PDO::PARAM_STR);
	$org->execute();
	$tariff=$org->fetch(PDO::FETCH_ASSOC);
	$TARIFF1=$tariff['tariff_code'];
	$TARIFF2=$tariff['secondary_tariff'];
	//if exists and condition satisfied get tariffcode based on org code from sponsor master
	$docdetails=$pdoread->prepare("SELECT `sno`, `department_name` AS category,'' AS hsn, `doc_code`, `doc_uid` AS servicecode, CONCAT(`title`, `doctor_name`) AS display, `ward_name`, `charge`AS price, `revisit_charge`, `emergency_charge`, `tarif_name`AS subcategory, `tariff_code`, `branch`, `created_on`, `created_by`, `modified_on`, `modified_by`, `status`, `ipaddress` FROM `consultation_master` WHERE (`status`='Active' AND `ward_name`=:wardname AND `doctor_name` LIKE :searchterm  AND `tariff_code`=:tariff_code)OR(`status`='Active' AND `ward_name`=:wardname AND `doctor_name` LIKE :searchterm  AND `tariff_code`=:tariff) ");
	$docdetails -> bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
	$docdetails->bindParam(':wardname', $ward, PDO::PARAM_STR);
	$docdetails->bindParam(':tariff_code', $TARIFF1, PDO::PARAM_STR);
	$docdetails->bindParam(':tariff', $TARIFF2, PDO::PARAM_STR);
	$docdetails -> execute();    
	if($docdetails->rowCount()>0){
		http_response_code(200);
		 $response['error']=false;
			$response['message']="Data found";
		while($res=$docdetails->fetch(PDO::FETCH_ASSOC)){
			$response['investigationnewlist'][]=$res;
		}
	}else{
		$docdetails=$pdoread->prepare("SELECT `sno`, `department_name` AS category,'' AS hsn, `doc_code`, `doc_uid` AS servicecode, CONCAT(`title`, `doctor_name`) AS display, `ward_name`, `charge`AS price, `revisit_charge`, `emergency_charge`, `tarif_name`AS subcategory, `tariff_code`, `branch`, `created_on`, `created_by`, `modified_on`, `modified_by`, `status`, `ipaddress` FROM `consultation_master` WHERE `status`='Active' AND `tarif_name`='GENERAL' AND `ward_name`=:wardname AND `doctor_name` LIKE :searchterm AND `branch`=:branch ");
		$docdetails -> bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
		$docdetails->bindParam(':wardname', $ward, PDO::PARAM_STR);
		$docdetails->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
		$docdetails -> execute();    
		if($docdetails->rowCount()>0){
			http_response_code(200);
			 $response['error']=false;
				$response['message']="Data found";
			while($res=$docdetails->fetch(PDO::FETCH_ASSOC)){
				$response['investigationnewlist'][]=$res;
			}
		
	}else{
		http_response_code(503);
		$response['error']=true;
		$response['message']="Sorry! No Data Found";
	}
}
	
	}else if ($category=="FOOD AND BEVERAGES"){
	 
//get details from fb_item_master

$foodd=$pdoread->prepare("SELECT  `itemcode`AS servicecode, `itemname`AS display, `hsn`, `packing`, `uom` AS subcategory, `manufacturer`, `category`AS category, `genericname`, `gst`, `mrp` AS price FROM `fb_item_master` WHERE `vstatus`='Available' AND `itemname` LIKE :searchterm ");
$foodd -> bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
$foodd->execute();
if($foodd->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	   $response['message']="Data found";
   while($res=$foodd->fetch(PDO::FETCH_ASSOC)){
	   $response['investigationnewlist'][]=$res;
   }
}else{
	http_response_code(503);
	$response['error']=true;
   $response['message']="Sorry! No Data Found";
}

	}else if ($category=="INVESTIGATION"){
		$gettariff=$pdoread->prepare("SELECT service.id,service.display,service.servicecode,service.price,`services_master`.`billing_head`,`services_master`.`service_sub_category` AS subcategory,service.category,'' AS hsn FROM (SELECT `tariff_master`.`service_code`,`tariff_master`.`sno`AS id,`display_name` AS display, `display_code` AS servicecode,`ward_charges` AS price,`sponsor_master`.`tariff_name` AS category FROM `sponsor_master` LEFT JOIN `tariff_master` ON `tariff_master`.`tariff_code` = `sponsor_master`.`tariff_code` WHERE `cost_center` LIKE :branch AND `category` LIKE 'GENERAL' AND `display_name` LIKE :searchterm AND `ward_name` = :admittedward GROUP BY service_code LIMIT 10) AS service LEFT JOIN `services_master` ON `services_master`.`service_code` = service.service_code AND `services_master`.`service_status` = 'Active'");
		$gettariff -> bindValue(":searchterm", "%{$searchterm}%", PDO::PARAM_STR);
		$gettariff->bindParam(":admittedward", $ward, PDO::PARAM_STR);
		$gettariff->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
		$gettariff->execute();
		if($gettariff->rowCount()>0){
			http_response_code(200);
			$response['error']=false;
			   $response['message']="Data found";
		   while($res=$gettariff->fetch(PDO::FETCH_ASSOC)){
			   $response['investigationnewlist'][]=$res;
		   }
		}else{
			http_response_code(503);
			$response['error']= true;
			$response['message']="No such investigations are found";
		}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Please Select Proper Category";
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