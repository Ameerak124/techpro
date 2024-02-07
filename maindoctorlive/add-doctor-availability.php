<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey = trim($data->accesskey);
$fdate = date_format(date_create($data->fdate), "Y-m-d");
$tdate = date_format(date_create($data->tdate), "Y-m-d");
$doctorcode = strtoupper($data->doctorcode);
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {

	if (!empty($accesskey) && !empty($fdate) && !empty($doctorcode) && !empty($tdate)) {
		//Check access 
		$check = $pdoread->prepare("SELECT `userid`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check->execute();
		$result = $check->fetch(PDO::FETCH_ASSOC);
		$costcenter = $result['cost_center'];
		if ($check->rowCount() > 0) {
			
			if($tdate>=$fdate){
			$gen = $pdoread->prepare("SELECT IFNULL(MAX(`transid`),CONCAT('TR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000')) AS ocode  FROM `doctor_availability` WHERE `transid` LIKE '%TR%';");
			$gen->execute();
			$code = $gen->fetch(PDO::FETCH_ASSOC);
			$id = $code['ocode'];
			$ocd = ++$id;
			$crosscheck = $pdoread->prepare("SELECT * FROM `doctor_availability` WHERE `DoctorCode` = :doctorcode AND `fdate` BETWEEN :fdate AND :tdate AND `estatus` = 'Active'");
			$crosscheck->bindParam(':fdate', $fdate, PDO::PARAM_STR);
			$crosscheck->bindParam(':tdate', $tdate, PDO::PARAM_STR);
			$crosscheck->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
			$crosscheck->execute();
			if($crosscheck->rowCount() > 0){
				http_response_code(503);
				$response['error'] = true;
				$response['message'] = "Sorry! date already submitted";
			}else{

			while (strtotime($fdate) <= strtotime($tdate)) {

				//check ip status
				$check_ip = $pdo4->prepare("INSERT INTO `doctor_availability` (`sno`, `DoctorCode`, `fdate`, `tdate`, `transid`, `estatus`) VALUES (NULL, :doctorcode, :fdate, :tdate, :ocd, 'Active')");
				$check_ip->bindParam(':fdate', $fdate, PDO::PARAM_STR);
				$check_ip->bindParam(':tdate', $tdate, PDO::PARAM_STR);
				$check_ip->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
				$check_ip->bindParam(':ocd', $ocd, PDO::PARAM_STR);
				$check_ip->execute();
				if ($gen->rowCount() > 0) {
					http_response_code(200);
					$response['error'] = false;
					$response['message'] = "Data saved";
				} else {
					http_response_code(503);
					$response['error'] = true;
					$response['message'] = "Sorry! Please try again";
				}
				$fdate = date("Y-m-d", strtotime("+1 day", strtotime($fdate)));
			}
			
		}
			}else{
			http_response_code(503);
			$response['error'] = true;
			$response['message'] = "Please select proper dates";	
				
				
			}
		} else {
			http_response_code(400);
			$response['error'] = true;
			$response['message'] = "Access denied!";
		}
		//
	} else {
		http_response_code(400);
		$response['error'] = true;
		$response['message'] = "Sorry! some details are missing";
	}
} catch (PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message'] = "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>
