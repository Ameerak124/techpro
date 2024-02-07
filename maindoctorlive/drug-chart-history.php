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
$response1 = array();
$response2 = array();
$accesskey=trim($data->accesskey);
$ip_no=trim($data->ip_no);

try {
     if(!empty($accesskey)&& !empty($ip_no)){    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//get dates list 
$get_dates=$pdoread->prepare("SELECT MIN(`dosage_date`)AS start_date,MAX(`dosage_date`) AS end_date  FROM `drug_medication` WHERE `ip_no`=:ip_no AND date(dosage_date)<= CURRENT_DATE  LIMIT 1 ");
$get_dates->bindParam(':ip_no', $ip_no, PDO::PARAM_STR);
$get_dates->execute();
$dates_resp=$get_dates->FETCH(PDO::FETCH_ASSOC);
//dates end
$f_date=$dates_resp['start_date'];
$t_date=$dates_resp['end_date'];
$dates_list=$pdoread->prepare(" SELECT date(`dosage_date`)AS selected_date,DATE_FORMAT(`dosage_date`,'%d-%b-%Y')AS display_date FROM `drug_medication` WHERE `ip_no`=:ip_no AND `status`='Active' GROUP BY `dosage_date` ORDER BY dosage_date DESC");
$dates_list->bindParam(':ip_no', $ip_no, PDO::PARAM_STR);
$dates_list->execute();
if($dates_list->rowCount()>0){
while($d_result=$dates_list->fetch(PDO::FETCH_ASSOC)){
//$dates_row[]=$d_result;
$get_medicines=$pdoread->prepare(" SELECT `drug_medication`.`medicine_code`,`drug_medication`.`medicine_name`,ifnull(CONCAT(`doctor_mediciation`.`frequency`,' (',`doctor_mediciation`.`dosage`,')'),'')
AS frequency FROM `drug_medication` LEFT JOIN `doctor_mediciation` ON `drug_medication`.`ip_no` = `doctor_mediciation`.`billno` AND `doctor_mediciation`.`medicine_code` = `drug_medication`.`medicine_code` AND `doctor_mediciation`.`vstatus` = 'Active' WHERE `status`='Active' AND `ip_no`= :ip_no AND DATE(`dosage_date`) = DATE(:ddate) GROUP BY `medicine_code`,medicine_name");
$get_medicines->bindParam(':ip_no', $ip_no, PDO::PARAM_STR);
 $get_medicines->bindParam(":ddate", $d_result['selected_date'], PDO::PARAM_STR);
$get_medicines->execute();
//$s = 0;
while($e_result=$get_medicines->fetch(PDO::FETCH_ASSOC)){



$getmedtime = $pdoread -> prepare("SELECT IFNULL(GROUP_CONCAT(DATE_FORMAT(`dosage_time`, '%H:%i:%p') SEPARATOR ', '),'--') AS medtime,COALESCE((SELECT ifnull(`frequency`,'') AS frequency FROM doctor_mediciation WHERE `medicine_code`=:mcode AND `billno`=:ipnumber LIMIT 1),'--') AS frequency,remarks  FROM `drug_medication` WHERE `ip_no` =:ipnumber AND `medicine_code` =:mcode AND DATE(`dosage_date`) = DATE(:ddate);");
    $getmedtime->bindParam(":mcode", $e_result['medicine_code'], PDO::PARAM_STR);
    $getmedtime->bindParam(":ddate", $d_result['selected_date'], PDO::PARAM_STR);
    $getmedtime->bindParam(":ipnumber", $ip_no, PDO::PARAM_STR);
    $getmedtime-> execute();
   

while($medtimedata = $getmedtime->fetch(PDO::FETCH_ASSOC)){

$temp1=[
"mcode"=>"Medicine Code",
"mname"=>"Medicine Name",
"frqncy"=>"Frequency",
"mtime"=>"Medicine Time",
"rmks"=>"Remarks",
"medicine_code"=>$e_result['medicine_code'],
"medicine_name"=>$e_result['medicine_name'],
"frequency"=>$e_result['frequency'],
"medtime"=>$medtimedata['medtime'],
"remarks"=>$medtimedata['remarks'],

];
array_push($response2,$temp1);
}

}



$temp=[
"selectdate"=>$d_result['selected_date'],
"displaydate"=>$d_result['display_date'],
"medlist"=>$response2,

];
array_push($response1,$temp);	
$response2=array();
}
http_response_code(200);
    $response['error'] = false;
	$response['message']= "Data found";
	$response['mcode']= "Medicine Code";
	$response['mname']= "Medicine Name";
	$response['frqncy']= "Frequency";
	$response['mtime']= "Medicine Time";
	$response['rmks']= "Remarks";
$response['druglist']= $response1;
}else{
http_response_code(503);
    $response['error'] = true;
	$response['message']= "No Data found";	
	
}
//got medicine names
}else {	
http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {	
http_response_code(503);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection Failed";
	
}
echo json_encode($response);
$pdoread = null;
?>