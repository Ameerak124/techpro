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
$docid = trim($data->docid);
$patient_id = trim($data->patient_id);
$service = ($data->service);
$service_id = trim($data->service_id);
$instruction = trim($data->instruction);
$cat = trim($data->cat);
$tname = trim($data->tname);
if ($service_id == '') {
	$service_id = 'OTHERS';
}


try {
	if (!empty($accesskey) && !empty($docid) && !empty($patient_id)) {
		
		$check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check->execute();
		$result = $check->fetch(PDO::FETCH_ASSOC);
		if ($check->rowCount() > 0) {
			// $rest=$con->prepare("SELECT `service_id` FROM `doctor_service_suggestion` WHERE `service_id`=:service_id AND `status`='Active' AND `patient_id`=:patient_id ");
			// $rest->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
			// $rest->bindParam(':service_id', $service_id, PDO::PARAM_STR);
			// $rest->execute();
			// if($rest->rowCount()>0){
			//     $response['error']=true;
			//     $response['message']="Details Already! Exists";
			// }else{

			if ($cat == '' && !empty($service) && !empty($service_id)){
				$values = $pdo4->prepare("INSERT IGNORE INTO `doctor_service_suggestion`(`sno`, `doc_id`, `patient_id`, `service`,`service_id`, `created_by`, `created_on`, `modified_by`, `modified_on`, `status`, `cost_center`, `ip`,`instruction`) VALUES (NULL,:docid,:patient_id,:service,:service_id,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:branch,:ip,:instruction)");
				$values->bindParam(':docid', $docid, PDO::PARAM_STR);
				$values->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
				$values->bindParam(':service', $service, PDO::PARAM_STR);
				$values->bindParam(':service_id', $service_id, PDO::PARAM_STR);
				$values->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
				$values->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
				$values->bindParam(':ip', $ipaddress, PDO::PARAM_STR);
				$values->bindParam(':instruction', $instruction, PDO::PARAM_STR);
				$values->execute();
				if ($values->rowCount() > 0) {
					http_response_code(200);
					$response['error'] = false;
					$response['message'] = "Data Added Successfully";
				} else {
					http_response_code(503);
					$response['error'] = true;
					$response['message'] = "Sorry! Please try Again";
				}
			} else if ($cat == 'HISTORY') {
				//get previous values
				$get_previous = $pdoread->prepare(" SELECT @a:=@a+1 AS sno,`sno` AS id,`service_id`,`service`,'' AS service_group_code FROM (SELECT @a:=0) AS a,`doctor_service_suggestion` WHERE `status`='Active' AND `patient_id`=:patient_id AND  `doc_id`=:docid AND `status`='Active' GROUP BY `service_id` ");
				$get_previous->bindParam(':docid', $docid, PDO::PARAM_STR);
				$get_previous->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
				$get_previous->execute();
				if ($get_previous->rowCount() > 0) {
					while ($res = $get_previous->fetch(PDO::FETCH_ASSOC)) {
						$values = $pdo4->prepare("INSERT IGNORE INTO `doctor_service_suggestion`(`sno`, `doc_id`, `patient_id`, `service`,`service_id`, `created_by`, `created_on`, `modified_by`, `modified_on`, `status`, `cost_center`, `ip`,`instruction`) VALUES (NULL,:docid,:patient_id,:service,:service_id,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:branch,:ip,:instruction)");
						$values->bindParam(':docid', $docid, PDO::PARAM_STR);
						$values->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
						$values->bindParam(':service', $res['service'], PDO::PARAM_STR);
						$values->bindParam(':service_id', $res['service_id'], PDO::PARAM_STR);
						$values->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
						$values->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
						$values->bindParam(':ip', $ipaddress, PDO::PARAM_STR);
						$values->bindParam(':instruction', $instruction, PDO::PARAM_STR);
						$values->execute();
					}
					if ($values->rowCount() > 0) {
						http_response_code(200);
						$response['error'] = false;
						$response['message'] = "Data Added Successfully";
					} else {
						http_response_code(503);
						$response['error'] = true;
						$response['message'] = "Sorry! Please try Again";
					}
				} else {
					http_response_code(503);
					$response['error'] = true;
					$response['message'] = "No Previous History Found";
				}
			} else if ($cat == 'TEMPLATE') {
				//get previous values
				$get_previous = $pdoread->prepare(" SELECT `service_code`,`service_name`,`service_cost`,`instruction` FROM `service_templates` WHERE `status`='Active' AND `template_name`=:tname AND `req_no`=:req_no ");
				$get_previous->bindParam(':req_no', $patient_id, PDO::PARAM_STR);
				$get_previous->bindParam(':tname', $tname, PDO::PARAM_STR);
				$get_previous->execute();
				if ($get_previous->rowCount() > 0) {
					while ($res = $get_previous->fetch(PDO::FETCH_ASSOC)) {
						$values = $pdo4->prepare("INSERT IGNORE INTO `doctor_service_suggestion`(`sno`, `doc_id`, `patient_id`, `service`,`service_id`, `created_by`, `created_on`, `modified_by`, `modified_on`, `status`, `cost_center`, `ip`,`instruction`) VALUES (NULL,:docid,:patient_id,:service,:service_id,:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,'Active',:branch,:ip,:instruction)");
						$values->bindParam(':docid', $docid, PDO::PARAM_STR);
						$values->bindParam(':patient_id', $patient_id, PDO::PARAM_STR);
						$values->bindParam(':service', $res['service_name'], PDO::PARAM_STR);
						$values->bindParam(':service_id', $res['service_code'], PDO::PARAM_STR);
						$values->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
						$values->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
						$values->bindParam(':ip', $ipaddress, PDO::PARAM_STR);
						$values->bindParam(':instruction', $res['instruction'], PDO::PARAM_STR);
						$values->execute();
					}
					if ($values->rowCount() > 0) {
						http_response_code(200);
						$response['error'] = false;
						$response['message'] = "Data Added Successfully";
					} else {
						http_response_code(503);
						$response['error'] = true;
						$response['message'] = "Sorry! Please try Again";
					}
				} else {
					http_response_code(503);
					$response['error'] = true;
					$response['message'] = "No Templates Added";
				}
			} else {
				http_response_code(503);
				$response["error"] = true;
				$response["message"] = "Please Try Again";
			}
			// }
		} else {
			http_response_code(400);
			$response['error'] = true;
			$response['message'] = "Access denied!";
		}
	} else {
		http_response_code(400);
		$response['error'] = true;
		$response['message'] = "Sorry! some details are missing ";
	}
} catch (PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message'] = "Connection failed";
	
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>