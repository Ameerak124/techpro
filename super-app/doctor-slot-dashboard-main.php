<?php
header("Content-Type: application/json; charset=UTF-8");
try {
//data credentials
include "pdo-db-new.php";
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$fromdate = date('Y-m-d', strtotime($data->fromdate));
$todate = date('Y-m-d', strtotime($data->todate));



$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);

if(!empty($accesskey)&&!empty($fromdate)&&!empty($todate) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  
	$week_days_qry= $pdo->prepare("SELECT 
		SUM(CASE WHEN WEEKDAY(dates.date) = 0 THEN 1 ELSE 0 END) AS Mondays,
		SUM(CASE WHEN WEEKDAY(dates.date) = 1 THEN 1 ELSE 0 END) AS Tuesdays,
		SUM(CASE WHEN WEEKDAY(dates.date) = 2 THEN 1 ELSE 0 END) AS Wednesdays,
		SUM(CASE WHEN WEEKDAY(dates.date) = 3 THEN 1 ELSE 0 END) AS Thursdays,
		SUM(CASE WHEN WEEKDAY(dates.date) = 4 THEN 1 ELSE 0 END) AS Fridays,
		SUM(CASE WHEN WEEKDAY(dates.date) = 5 THEN 1 ELSE 0 END) AS Saturdays,
		SUM(CASE WHEN WEEKDAY(dates.date) = 6 THEN 1 ELSE 0 END) AS Sundays
FROM 
	(
		SELECT 
			:fromdate + INTERVAL (a.a + (10 * b.a) + (100 * c.a)) DAY AS date
		FROM 
			(SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
			CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
			CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
	) AS dates
WHERE 
	dates.date BETWEEN :fromdate AND :todate;");
	$week_days_qry->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
	$week_days_qry->bindParam(':todate', $todate, PDO::PARAM_STR);
	$week_days_qry -> execute();
	$week_days = $week_days_qry->fetch(PDO::FETCH_ASSOC);
	$doctor_slots_query=$pdoread->prepare("SELECT SUM(Monday_slots+Tuesday_slots+Wednesday_slots+Thursday_slots+Friday_slots+Saturday_slots+Sunday_slots) as total_slots,SUM(booked) as booked,(SELECT count(*) FROM `doctor_master` WHERE 
    doctor_master.status = 'Active' AND doctor_master.doctor_type='Revenue' AND doctor_master.location=location_cd ) as total_doctors, SUM(Monday_slots+Tuesday_slots+Wednesday_slots+Thursday_slots+Friday_slots+Saturday_slots+Sunday_slots)-SUM(booked) as available,location_cd,location_name FROM (SELECT  
    doctor_master.doctor_code,
    doctor_timings.location_cd,
    (
        SELECT COUNT(slot) AS booked
        FROM patient_details
        WHERE 
            doctor_code = doctor_master.doctor_code
            AND slot_status = 'Booked'
            AND location = doctor_master.location
            AND DATE(date) BETWEEN :fromdate AND :todate
    ) AS booked,
	(SELECT `display_name` FROM `branch_master` WHERE `cost_center` = doctor_timings.location_cd) as location_name,
    SUM(CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )) as total_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Monday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Mondays) AS Monday_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Tuesday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Tuesdays) AS Tuesday_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Wednesday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Wednesdays) AS Wednesday_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Thursday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Thursdays) AS Thursday_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Friday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Fridays) AS Friday_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Saturday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Saturdays) AS Saturday_slots,
    SUM(CASE WHEN doctor_timings.day_name = 'Sunday' THEN 
            CEIL(
                TIME_TO_SEC(TIMEDIFF(doctor_timings.to_time, doctor_timings.from_time)) / 
                (900)
            )
        ELSE 0 END)*(:Sundays) AS Sunday_slots
FROM 
doctor_master
LEFT JOIN 
doctor_timings ON doctor_master.doctor_code = doctor_timings.doctor_code AND doctor_master.location = doctor_timings.location_cd
WHERE 
    doctor_master.status = 'Active' 
    AND doctor_timings.status = 'Active'
    
AND doctor_master.doctor_type='Revenue'
GROUP BY 
    doctor_master.doctor_code,
    doctor_master.location
HAVING total_slots IS NOT NULL) as E GROUP BY E.location_cd ORDER BY E.booked DESC  ;");
	$doctor_slots_query->bindParam(':Mondays', $week_days['Mondays'], PDO::PARAM_STR);
	$doctor_slots_query->bindParam(':Tuesdays', $week_days['Tuesdays'], PDO::PARAM_STR);
	$doctor_slots_query->bindParam(':Wednesdays', $week_days['Wednesdays'], PDO::PARAM_STR);
	$doctor_slots_query->bindParam(':Thursdays', $week_days['Thursdays'], PDO::PARAM_STR);
	$doctor_slots_query->bindParam(':Fridays', $week_days['Fridays'], PDO::PARAM_STR);
	$doctor_slots_query->bindParam(':Saturdays', $week_days['Saturdays'], PDO::PARAM_STR);
	$doctor_slots_query->bindParam(':Sundays', $week_days['Sundays'], PDO::PARAM_STR);
    $doctor_slots_query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $doctor_slots_query->bindParam(':todate', $todate, PDO::PARAM_STR);
	$doctor_slots_query -> execute();
	$doctor_slots = $doctor_slots_query->fetchAll(PDO::FETCH_ASSOC);
	 http_response_code(200);
    $response['error']= false;
    $response['message']="Data found";
    $response['data'] = $doctor_slots;
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
	$response['message']= "Connection failed";
	$errorlog =$pdo4->prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
	$errorlog->bindParam(':userid', $username, PDO::PARAM_STR);
	$errorlog->bindValue(':errmessage', "{$e->getMessage()}, At Line No :- {$e->getLine()}", PDO::PARAM_STR);
	$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
	$errorlog -> execute();
}
echo json_encode($response);
$pdoread= null;
$pdo4= null;
?>