<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
//data credentials
include 'pdo-db.php';
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$fromdate = date('Y-m-d', strtotime($data->fromdate));
$todate = date('Y-m-d', strtotime($data->todate));

$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

if(!empty($accesskey)&&!empty($fromdate)&&!empty($todate) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username`,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  
	$role = $result['role'];  
	if($role=='Medical Head' || $role=='Center Head'){
	$doctor_slots_query=$pdoread->prepare("SELECT location_name,count_doc,location_cd, location_name, total_doctors,total_slots,booked,(total_slots-booked) as available,UPPER(state) as state FROM  (SELECT `display_name` as location_name,`cost_center` as location_cd,`state`,IFNULL((SELECT COUNT(*) FROM `doctor_master` WHERE `status` = 'Active' AND doctor_type='Revenue' AND `location`=`cost_center`),(SELECT COUNT(*) FROM `doctor_master` WHERE `status` = 'Active' AND doctor_type='Revenue' AND `branch_access`=`cost_center`)) as total_doctors ,IFNULL(
        (SELECT 
             SUM(ROUND(TIME_TO_SEC(TIMEDIFF(to_time, from_time)) / 
            ((CASE WHEN slotgap = 0 THEN 15 ELSE slotgap END )*60), 0))
         FROM doctor_timings
         WHERE selecteddate BETWEEN :fromdate AND :todate AND status='Active' AND location_cd = cost_center 
         GROUP BY `location_cd`), 0
    ) AS total_slots,
(
        SELECT COUNT(slot) AS booked
        FROM patient_details
        WHERE 
           
             slot_status = 'Booked'
    		AND location = cost_center
        
            AND DATE(date) BETWEEN :fromdate AND :todate
    ) AS booked,
    (SELECT 
         COUNT( DISTINCT doctor_timings.doctor_code)
         FROM doctor_timings
         INNER JOIN doctor_master ON doctor_timings.doctor_code=doctor_master.doctor_uid
         WHERE selecteddate BETWEEN :fromdate AND :todate AND doctor_timings.status='Active' AND doctor_master.status='Active' AND doctor_master.doctor_type='Revenue' AND location_cd = `cost_center`) as count_doc FROM `branch_master` WHERE `status`='Active' AND cost_center=:cost_center) as E");
	
    $doctor_slots_query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $doctor_slots_query->bindParam(':todate', $todate, PDO::PARAM_STR);
    $doctor_slots_query->bindParam(':cost_center', $cost_center, PDO::PARAM_STR);
	$doctor_slots_query -> execute();
}else{
$doctor_slots_query=$pdoread->prepare("SELECT location_name,count_doc,location_cd, location_name, total_doctors,total_slots,booked,(total_slots-booked) as available,UPPER(state) as state FROM  (SELECT `display_name` as location_name,`cost_center` as location_cd,`state`,IFNULL((SELECT COUNT(*) FROM `doctor_master` WHERE `status` = 'Active' AND doctor_type='Revenue' AND `location`=`cost_center`),(SELECT COUNT(*) FROM `doctor_master` WHERE `status` = 'Active' AND doctor_type='Revenue' AND `branch_access`=`cost_center`)) as total_doctors ,IFNULL(
        (SELECT 
             SUM(ROUND(TIME_TO_SEC(TIMEDIFF(to_time, from_time)) / 
            ((CASE WHEN slotgap = 0 THEN 15 ELSE slotgap END )*60), 0))
         FROM doctor_timings
         WHERE selecteddate BETWEEN :fromdate AND :todate AND status='Active' AND location_cd = cost_center 
         GROUP BY `location_cd`), 0
    ) AS total_slots,
(
        SELECT COUNT(slot) AS booked
        FROM patient_details
        WHERE 
           
             slot_status = 'Booked'
    		AND location = cost_center
        
            AND DATE(date) BETWEEN :fromdate AND :todate
    ) AS booked,
    (SELECT 
         COUNT( DISTINCT doctor_timings.doctor_code)
         FROM doctor_timings
         INNER JOIN doctor_master ON doctor_timings.doctor_code=doctor_master.doctor_uid
         WHERE selecteddate BETWEEN :fromdate AND :todate AND doctor_timings.status='Active' AND doctor_master.status='Active' AND doctor_master.doctor_type='Revenue' AND location_cd = `cost_center`) as count_doc FROM `branch_master` WHERE `status`='Active') as E;");
	
    $doctor_slots_query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $doctor_slots_query->bindParam(':todate', $todate, PDO::PARAM_STR);
	$doctor_slots_query -> execute();
}
	$doctor_slots = $doctor_slots_query->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    $response['error']= false;
    $response['message']="Data found";
	$response['unitname']="Unit\nName";
    $response['totaldoctors']="Total\nDoctors";
    $response['totalslots']="Total\nslots";
    $response['bookedslots']="Booked\nslots";
    $response['avlslots']="Available\nslots";
   $response['doctorsdashbordcounts'] = $doctor_slots;
    // $response['days_count']=$week_days;
    //  Doctor Slots
    $doctor_specific_query=$pdoread->prepare("
    SELECT `doctor_uid` as doctor_code,full_name as doctor_name,`location` as location_cd ,IFNULL((SELECT display_name FROM branch_master WHERE cost_center =`location`),(SELECT display_name FROM branch_master WHERE cost_center =`branch_access`)) as location_name,UPPER(IFNULL((SELECT `state` FROM branch_master WHERE cost_center =`location`),(SELECT `state` FROM branch_master WHERE cost_center =`branch_access`))) as state,`department` as speciality,total_slots,booked,(total_slots-booked) as available FROM (SELECT 
    `doctor_uid`,
    CONCAT('Dr. ', `doctor_name`) AS full_name,
    `location`,
    `department`,
    `branch_access`,
    IFNULL(
        (SELECT 
            SUM(ROUND(TIME_TO_SEC(TIMEDIFF(to_time, from_time)) / 
            ((CASE WHEN slotgap = 0 THEN 15 ELSE slotgap END )*60), 0))
         FROM doctor_timings
         WHERE selecteddate BETWEEN :fromdate AND :todate AND status='Active' AND doctor_code = doctor_uid
         GROUP BY doctor_code), 0
    ) AS total_slots,
    (
        SELECT COUNT(slot) AS booked
        FROM patient_details
        WHERE 
            doctor_code = doctor_master.doctor_code
            AND slot_status = 'Booked'
            AND location = doctor_master.location
            AND DATE(date) BETWEEN :fromdate AND :todate
    ) AS booked
FROM doctor_master
WHERE `status` = 'Active' AND doctor_type='Revenue' ) as E;
");
    
    $doctor_specific_query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $doctor_specific_query->bindParam(':todate', $todate, PDO::PARAM_STR);
    $doctor_specific_query->execute();
    if($doctor_specific_query->rowCount()>0){
        $response['doctors_list']=$doctor_specific_query->fetchAll(PDO::FETCH_ASSOC);
    }else{
        $response['doctors_list']= '';
       
    }
$patient_specific_qry=$pdoread->prepare("SELECT  `location`, (SELECT `display_name` FROM `branch_master`  WHERE `cost_center`=`location`) as location_name, `doctor_code`,(SELECT CONCAT('Dr. ',`doctor_name`) FROM `doctor_master` WHERE `doctor_uid`=  `patient_details`.`doctor_code` ) as doctor_name,(SELECT `department` FROM `doctor_master` WHERE `doctor_uid`=  `patient_details`.`doctor_code` ) as department, `date`, `slot`, `slot_status`, `umrno`, `invoice_no`, `bill_no`, `requisition_no`, `patient_name`, `patient_age`, `gender`, `patient_number`, `patient_mail`, `patient_alt_number`, `remarks`, `consultation_type`, `transid`, `consult_status`, `patient_lead_source`,`visit_type`,`reschedule_to` FROM `patient_details` WHERE slot_status IN ('Booked','Rescheduled') AND (DATE(date) BETWEEN :fromdate AND :todate);");
$patient_specific_qry->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$patient_specific_qry->bindParam(':todate', $todate, PDO::PARAM_STR);
$patient_specific_qry->execute();
if($patient_specific_qry->rowCount()>0){
    $response['patient_list']=$patient_specific_qry->fetchAll(PDO::FETCH_ASSOC);
}else{
    $response['patient_list']= '';
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
	$response['error'] = true;
	$response['message']= "Connection failed";
	$errorlog =$pdo->prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
	$errorlog->bindParam(':userid', $username, PDO::PARAM_STR);
	$errorlog->bindValue(':errmessage', "{$e->getMessage()}, At Line No :- {$e->getLine()}", PDO::PARAM_STR);
	$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
	$errorlog -> execute();
}
echo json_encode($response);
$pdoread = null;
?>