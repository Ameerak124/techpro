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
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$accesskey = $data->accesskey;
$searchterm = $data->searchterm;
	
$response = array();
try{

if(!empty($fdate) && !empty($tdate) && !empty($accesskey)){
$validate = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center`,UPPER(`role`) AS roles FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();

if($validate -> rowCount() > 0){
	$validres = $validate->fetch(PDO::FETCH_ASSOC);  
   if ($validres['roles'] == 'DOCTOR') {
				$stmt = $pdoread->prepare("SELECT COUNT(`umrno`) AS allcount,SUM(CASE WHEN `admissionstatus` = 'Admitted' THEN 1 ELSE 0 END) AS admissions,SUM(CASE WHEN `admissionstatus` = 'Initiated Discharge' THEN 1 ELSE 0 END) AS readytodischarge,SUM(CASE WHEN `admissionstatus` = 'Discharged' THEN 1 ELSE 0 END) AS discharged  FROM `registration` WHERE DATE(`admittedon`) BETWEEN :fdate AND :tdate AND `branch` = :branch AND `status`='Visible' AND `admissionstatus` not in ('Cancelled','Discharged') AND `consultantcode` = :userid");
				$stmt->bindParam(':fdate', $fdate, PDO::PARAM_STR);
				$stmt->bindParam(':tdate', $tdate, PDO::PARAM_STR);
				$stmt->bindParam(':branch', $validres['branch'], PDO::PARAM_STR);
				$stmt->bindParam(':userid', $validres['userid'], PDO::PARAM_STR);
			} else {
				$stmt = $pdoread->prepare("SELECT COUNT(`umrno`) AS allcount,SUM(CASE WHEN `admissionstatus` = 'Admitted' THEN 1 ELSE 0 END) AS admissions,SUM(CASE WHEN `admissionstatus` = 'Initiated Discharge' THEN 1 ELSE 0 END) AS readytodischarge,SUM(CASE WHEN `admissionstatus` = 'Discharged' THEN 1 ELSE 0 END) AS discharged  FROM `registration` WHERE DATE(`admittedon`) BETWEEN :fdate AND :tdate AND `branch` = :branch AND `status`='Visible' AND `admissionstatus` not in ('Cancelled','Discharged') ");
				$stmt->bindParam(':fdate', $fdate, PDO::PARAM_STR);
				$stmt->bindParam(':tdate', $tdate, PDO::PARAM_STR);
				$stmt->bindParam(':branch', $validres['branch'], PDO::PARAM_STR);
				
			}

			$stmt->execute();
			$records = $stmt->fetch(PDO::FETCH_ASSOC);
			$totalRecords = $records['allcount'];
	//$validres = $validate->fetch(PDO::FETCH_ASSOC);
    if ($validres['roles'] == 'DOCTOR') {
				$check2 = $pdoread->prepare("SELECT `umrno`,  `admissionno`,`admissionstatus`, date_format(`admittedon`,'%d-%b-%Y') AS admtno, date_format(`admittedon`,'%h:%i %p') AS admttime,(CASE WHEN `admissionstatus` = 'Discharged' THEN date_format(`dischargedon`,'%d-%b-%Y')  ELSE '--' END) AS DDATE,(CASE WHEN `admissionstatus` = 'Discharged' THEN date_format(`dischargedon`,'%h:%i %p') ELSE '--' END) AS DTIME,`patientname`, DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),`patientage`)), '%Y') + 0 AS patientage, `consultantname`, `department`, `admittedward` AS Admittedward,`roomno` AS roomno ,(CASE WHEN `vip_patient`='0' THEN 'd-none' ELSE 'label-danger' END)AS vip ,`vip_patient`,`contactno` ,`organization_name` AS sponsorname ,`patient_category`,`patientgender`,`title` ,`dis_edit_status` FROM `registration` WHERE DATE(`admittedon`) BETWEEN :fdate AND :tdate AND `cost_center`=:branch AND `status`='Visible' AND `admissionstatus` not in ('Cancelled','Discharged') AND `consultantcode` = :userid and (admissionno like :searchterm || patientname like :searchterm || umrno like :searchterm) ORDER BY `admittedon` DESC");
				$check2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
				$check2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
				$check2->bindParam(':branch', $validres['cost_center'], PDO::PARAM_STR);
				$check2->bindParam(':userid', $validres['userid'], PDO::PARAM_STR);
				$check2->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
			} else {
				$check2 = $pdoread->prepare("SELECT `umrno`,  `admissionno`,`admissionstatus`, date_format(`admittedon`,'%d-%b-%Y') AS admtno, date_format(`admittedon`,'%h:%i %p') AS admttime,(CASE WHEN `admissionstatus` = 'Discharged' THEN date_format(`dischargedon`,'%d-%b-%Y')  ELSE '--' END) AS DDATE,(CASE WHEN `admissionstatus` = 'Discharged' THEN date_format(`dischargedon`,'%h:%i %p') ELSE '--' END) AS DTIME,`patientname`,DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),`patientage`)), '%Y') + 0 AS patientage, `consultantname`, `department`, `admittedward` AS Admittedward,`roomno` AS roomno ,(CASE WHEN `vip_patient`='0' THEN 'd-none' ELSE 'label-danger' END)AS vip ,`vip_patient`,`contactno` ,`organization_name` AS sponsorname ,`patient_category`,`patientgender`,`title`,`dis_edit_status` FROM `registration` WHERE DATE(`admittedon`) BETWEEN :fdate AND :tdate AND `cost_center`=:branch AND `status`='Visible' AND `admissionstatus` not in ('Cancelled','Discharged') and (patientname like :searchterm || admissionno like :searchterm || umrno like :searchterm) ORDER BY `admittedon` DESC");
				$check2->bindParam(':fdate', $fdate, PDO::PARAM_STR);
				$check2->bindParam(':tdate', $tdate, PDO::PARAM_STR);
				$check2->bindParam(':branch', $validres['cost_center'], PDO::PARAM_STR);
				$check2->bindValue(':searchterm', "%{$searchterm}%", PDO::PARAM_STR);
			}
			$check2->execute();
			if ($check2->rowCount() > 0) {
				$response['error'] = false;
				$response['message'] = "Data found";
				$response['role'] = $validres['role'];
				$response['admissions'] = $records['admissions'];
				$response['role'] =$validres['roles'];
				$response['readytodischarge'] = $records['readytodischarge'];
				$response['discharged'] = $records['discharged'];
				$response['draw'] = intval($draw);
				$response['iTotalRecords'] = $totalRecords;
				$response['iTotalDisplayRecords'] = $totalRecords;

	while($check2list = $check2->fetch(PDO::FETCH_ASSOC)){
		
		
		$response['list'][] = [
					'umrno'=>$check2list['umrno'],
					'admissionno'=>$check2list['admissionno'],
					'admtno'=>$check2list['admtno'],
					'admttime'=>$check2list['admttime'],
					'patientname'=>$check2list['patientname'],
					'patientage'=>$check2list['patientage'],
					'consultantname'=>$check2list['consultantname'],
					'department'=>$check2list['department'],
					'DDATE'=>$check2list['DDATE'],
					'roomno'=>$check2list['roomno'],
					'patientgender'=>$check2list['patientgender'],
					'Admittedward'=>$check2list['Admittedward'],
					
				];
	}
}else{
	http_response_code(503);
      $response['error'] = true;
				$response['message']="No data found";
}
    	}else{
			http_response_code(400);
					     $response['error'] = true;
							$response['message']="Access Denied!";
					}  
}else{
	          http_response_code(400);
				$response['error'] = true;
				$response['message'] ="Sorry! Some details are missing";
					}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdoread = null;
	?>