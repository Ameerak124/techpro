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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ipnumber = $data->ipnumber;
try{
if(!empty($accesskey) && !empty($ipnumber)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  
	$bed_track_que=$pdoread->prepare("SELECT
    `admissionno` as ipnumber,
    `service_code`,
    `service_name` as 'ward_name',
    DATE_FORMAT(`createdon`, '%Y-%b-%d %h:%i %p') as 'from_date',
    (CASE WHEN `bed_status`='ON_BED' THEN '--' ELSE DATE_FORMAT(`transferedon`, '%Y-%b-%d %h:%i %p') END) as 'to_date',
    (CASE WHEN `bed_status`='ON_BED' THEN '--' ELSE
        CONCAT(
            CASE WHEN FLOOR(TIMESTAMPDIFF(SECOND, `createdon`, `transferedon`) / (24 * 3600)) > 0
                 THEN CONCAT(FLOOR(TIMESTAMPDIFF(SECOND, `createdon`, `transferedon`) / (24 * 3600)), ' days ')
                 ELSE ''
            END,
            FLOOR((TIMESTAMPDIFF(SECOND, `createdon`, `transferedon`) % (24 * 3600)) / 3600), ' hours'
        )
    END) AS los,
    `remarks` as remarks,
    (CASE WHEN `remarks` = 'Admission' THEN 'Admitted' ELSE `bed_status` END) as 'bed_status',
    `createdby`,
    `transferedby` as 'shifted_by',
    `reference`
FROM
    `bed_transfer`
WHERE
    `admissionno` = :ipnumber AND `status` = 'Visible';

");
	$bed_track_que->bindParam(':ipnumber', $ipnumber, PDO::PARAM_STR);
	$bed_track_que->execute();
	if($bed_track_que->rowCount() > 0){
		http_response_code(200);
		$response['error'] = false;
		$response['message']= "Data found";
		$response['bedtransferlist'] = $bed_track_que->fetchAll(PDO::FETCH_ASSOC);
	}
	else{
		http_response_code(503);
		$response['error'] = true;
		$response['message']= "Data not found";
	}
}
else
{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied!";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(PDOException $e) {
	http_response_code(200);
	$response['error'] = true;
	$response['message']= "Connection failed";
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread= null;
?>