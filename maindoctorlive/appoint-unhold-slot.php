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
$doctorid = $data->doctorid;
$accesskey = $data->accesskey;
$selectdate = date_format(date_create($data->selectdate), "Y-m-d");
$slottime = date_format(date_create($data->slottime), "H:i:00");
$response = array();
try {
	
	if (!empty($slottime) && !empty($doctorid) && !empty($selectdate) && !empty($accesskey)) {
		$validate = $pdoread->prepare("SELECT `userid`,`cost_center` AS branch FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$validate->execute();
		$result = $validate->fetch(PDO::FETCH_ASSOC);
		if ($validate->rowCount() > 0) {
			//Doctor Details
			$doctor = $pdoread->prepare("SELECT `fullname`,`department` FROM `doctor_details` WHERE `sno` = :doctorid");
			$doctor->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
			$doctor->execute();
			$doctorres = $doctor->fetch(PDO::FETCH_ASSOC);
			$check2 = $pdo4->prepare("UPDATE `patient_details` SET `slot_status` = 'Unhold' WHERE `date`= :selectdate AND `slot`= :slottime AND `location` = :branch AND `doctor_code` = :doctorid AND `slot_status` = 'Hold'");
			$check2->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
			$check2->bindParam(':slottime', $slottime, PDO::PARAM_STR);
			$check2->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
			$check2->bindParam(':branch', $result['branch'], PDO::PARAM_STR);
			$check2->execute();
			//check2 executed//
			if ($check2->rowcount() > 0) {
				http_response_code(200);
				$response['error'] = false;
				$response['message'] = "Slot is unholded";
			} else {
				http_response_code(503);
				$response['error'] = true;
				$response['message'] = "Sorry! please try again";
			}
		} else {
			http_response_code(400);
			$response['error'] = true;
			$response['message'] = "Access Denied!";
		}
	} else {
		http_response_code(400);
		$response['error'] = true;
		$response['message'] = "Sorry! Some details are missing";
	}
} catch (PDOException $e) {
	die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>