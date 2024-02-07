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
$patientname = trim($data->patientname);
$patientgender = trim($data->patientgender);
$umr_no = trim($data->umr_no);
$selectdate = date_format(date_create($data->selectdate), "Y-m-d");
$accesskey = trim($data->accesskey);
$duration=trim($data->duration);
$starttime =($data->starttime);
$otcode=trim($data->otcode);
$mobile_no=trim($data->mobile_no);
$ot_technician_name=trim($data->ot_technician_name);
$drug_sensitivity=trim($data->drug_sensitivity);
$ot_status=trim($data->ot_status);
$remarks=trim($data->remarks);
$dob = trim($data->dob);
// input data//
if($umr_no==''){
	$umr_no='OTHERS';
}
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
$response = array();
try {
	// !empty($otcode) && !empty($ipno) && && !empty($selectdate) && !empty($starttime) && !empty($duration)
	if (!empty($accesskey)&& !empty($otcode) && !empty($selectdate) && !empty($starttime) && !empty($duration)  ) {
		
		$check = $pdoread->prepare("SELECT `userid`, `cost_center` AS branch FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active' ");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check->execute();
		$result = $check->fetch(PDO::FETCH_ASSOC);
		if ($check->rowCount() > 0) {
         //check if patient details exists already
	 $check_patient=$pdoread->prepare("SELECT `booking_code` FROM `ot_booking` WHERE `status`='Booked' AND `patient_name`=:patient_name AND `mobile_no`=:mobile_no AND `otcode`=:otcode AND `branch`=:branch ");
		 $check_patient->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
		 $check_patient->bindParam(':otcode', $otcode, PDO::PARAM_STR);
		 $check_patient->bindParam(':mobile_no', $mobile_no, PDO::PARAM_STR);
		 $check_patient->bindParam(':patient_name', $patientname, PDO::PARAM_STR);
		 $check_patient->execute();
         if ($check_patient->rowCount() == 0) {	

			$create = $pdoread->prepare("SELECT IFNULL(MAX(`booking_code`),CONCAT('OTB',DATE_FORMAT(CURRENT_TIMESTAMP,'%y%m'),'00000')) AS otno FROM `ot_booking` WHERE `booking_code` LIKE '%OTB%'");
			$create->execute();
			$grn = $create->fetch(PDO::FETCH_ASSOC);
			$booking = $grn['otno'];
			$booking_code = ++$booking;
			
	$myArray = explode(',', $starttime);

		foreach ($myArray as $value) {
$query1 = $pdoread->prepare("SELECT E.mydate,DATE_FORMAT(E.mydate,'%h:%i %p') AS dispalytime,if(v.start_time != '','Booked','Vacant') AS slotstatus  FROM (SELECT DATE(:selectdate) + interval (seq * 60) Minute as mydate FROM seq_0_to_23) AS E LEFT JOIN (SELECT `otcode`,`start_time`,`end_time` FROM `ot_booking` WHERE `otcode` =:otcode AND DATE((`start_time`) = :selectdate)) AS v ON E.mydate = v.start_time ORDER BY E.mydate ASC");
$query1->bindParam(':selectdate', $value, PDO::PARAM_STR);
$query1->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$query1->execute();
$OT1 = $pdoread->prepare("SELECT `branch`,`otcode`,`otname`,`otnumber` FROM `ot_master` WHERE `otcode` = :otcode");
$OT1->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$OT1->execute();
$RES1 = $OT1->fetch(PDO::FETCH_ASSOC);
						


if ($query1->rowCount() > 0) {
while ($result2 = $query1->fetch(PDO::FETCH_ASSOC)) {
							
	if ($result2['mydate'] == $value && $result2['slotstatus'] =='Vacant') {									

$list=$pdo4->prepare ("INSERT INTO `ot_booking`(`sno`, `booking_code`, `booking_type`, `otcode`, `otname`, `ottheatre`, `umr_no`, `ipno`, `patient_name`, `gender`, `dob`, `mobile_no`, `duration`, `start_time`, `start_by`, `end_time`, `end_by`, `created_on`, `created_by`, `updated_on`, `updated_by`, `branch`, `remarks`, `ot_technician_name`, `drug_sensitivity`, `status`, `ot_status`, `surgery_status`) VALUES (NULL,:booking_code,'General',:otcode,:otroom,:ottheatre,:umr_no,'OTHERS',:ptname,:ptgender,:dob,:mobile_no,:duration,:starttime,:userid,DATE_ADD(:starttime, INTERVAL 1 HOUR),:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:otbranch,:remarks,:ot_technician_name,:drug_sensitivity,'Booked',:ot_status,'Pending') ");

$list->bindParam(':otbranch', $RES1['branch'], PDO::PARAM_STR);
$list->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$list->bindParam(':otroom', $RES1['otname'], PDO::PARAM_STR);
$list->bindParam(':ottheatre', $RES1['otnumber'], PDO::PARAM_STR);
$list->bindParam(':ptname', $patientname, PDO::PARAM_STR);
$list->bindParam(':ptgender', $patientgender, PDO::PARAM_STR);
$list->bindParam(':booking_code', $booking_code, PDO::PARAM_STR);
$list->bindParam(':duration', $duration, PDO::PARAM_STR);
$list->bindParam(':starttime', $value, PDO::PARAM_STR);
$list->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
$list->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$list->bindParam(':mobile_no', $mobile_no, PDO::PARAM_STR);
$list->bindParam(':dob', $dob, PDO::PARAM_STR);
$list->bindParam(':ot_technician_name', $ot_technician_name, PDO::PARAM_STR);
$list->bindParam(':drug_sensitivity', $drug_sensitivity, PDO::PARAM_STR);
$list->bindParam(':ot_status', $ot_status, PDO::PARAM_STR);
$list->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$list->execute();
				   
}

	}
		}
			}
				if ($list->rowCount() > 0) {
					http_response_code(200);
					$response['error'] = false;
					$response['message'] = 'Your slot is booked';
                    $response['booking_code']=$booking_code;
				} else {
					http_response_code(503);
					$response['error'] = true;
					$response['message'] = 'Sorry! please try again';
				}
		
		 }else{
			 http_response_code(503);
			$response['error'] = true;	
			$response['message'] ="OT Booked Previously For".$patientname;
		 } 
		} else {
			http_response_code(400);
			$response['error'] = true;
			$response['message'] = 'Access Denied!';
		
		}


	} else {
		http_response_code(400);
		$response['error'] = true;
		$response['message'] = 'Some Details are Missing';
	
	}

} catch (PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>