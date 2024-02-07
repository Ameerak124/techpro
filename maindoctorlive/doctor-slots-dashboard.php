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
$branch = trim($data->branch);
$fdate = date('Y-m-d', strtotime($data->fdate));
$tdate = date('Y-m-d', strtotime($data->tdate));

if(!empty($accesskey)&&!empty($fdate)&&!empty($tdate) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  
	
	
    //  Doctor Slots
    $doctor_specific_query=$pdoread->prepare("
    SELECT `doctor_uid` as doctorcode,full_name as doctorname,`location` as location ,IFNULL((SELECT display_name FROM branch_master WHERE cost_center =`location`),(SELECT display_name FROM branch_master WHERE cost_center =`branch_access`)) as locationname,UPPER(IFNULL((SELECT `state` FROM branch_master WHERE cost_center =`location`),(SELECT `state` FROM branch_master WHERE cost_center =`branch_access`))) as state,`department` as speciality,totalcount,bookcount,(totalcount-bookcount) as avlcount FROM (SELECT 
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
         WHERE selecteddate BETWEEN :fdate AND :tdate AND status='Active' AND doctor_code = doctor_uid
         GROUP BY doctor_code), 0
    ) AS totalcount,
    (
        SELECT COUNT(slot) AS bookcount
        FROM patient_details
        WHERE 
            doctor_code = doctor_master.doctor_code
            AND slot_status = 'Booked'
            AND location = doctor_master.location
            AND DATE(date) BETWEEN :fdate AND :tdate
    ) AS bookcount
FROM doctor_master
WHERE `status` = 'Active' AND doctor_type='Revenue' AND doctor_master.location=:location_cd ) as E;
");
    
    $doctor_specific_query->bindParam(':fdate', $fdate, PDO::PARAM_STR);
    $doctor_specific_query->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$doctor_specific_query->bindParam(':location_cd', $branch, PDO::PARAM_STR);
    $doctor_specific_query->execute();
    if($doctor_specific_query->rowCount()>0){
		http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	$response['drname']="Doctor Name";
	$response['totalslots']="Total Slots";
	$response['bookedslots']="Booked Slots";
	$response['avlslots']="Available Slots";
        $response['appointmentslotslist']=$doctor_specific_query->fetchAll(PDO::FETCH_ASSOC);
    }else{
		http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	$response['drname']="Doctor Name";
	$response['totalslots']="Total Slots";
	$response['bookedslots']="Booked Slots";
	$response['avlslots']="Available Slots";
        $response['appointmentslotslist']= '';
       
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
}
echo json_encode($response);
$pdoread = null;
?>