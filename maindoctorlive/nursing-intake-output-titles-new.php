<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$ip = trim($data->ip);
/* $fdate = date('Y-m-d', strtotime($data->fdate));
$tdate = date('Y-m-d', strtotime($data->tdate)); */
$response = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
 $get_count = $pdoread->prepare("SELECT IFNULL((SELECT ROUND(SUM(`amount`),2) FROM nursing_intake_output WHERE `estatus`='Active' AND `category`='In Take' AND `ip`=:ip),'0')AS in_take,IFNULL((SELECT ROUND(SUM(`amount`),2) FROM nursing_intake_output WHERE `estatus`='Active' AND `category`='Output' AND `ip`=:ip ),'0')AS Output, IFNULL((SELECT ROUND(SUM(`amount`),2) FROM nursing_intake_output WHERE `estatus`='Active' AND `category`='Kilo calories' AND `ip`=:ip),'0')AS Kilo FROM `nursing_intake_output` WHERE `ip`=:ip AND DATE(`date`)=CURRENT_DATE");/* BETWEEN :fdate AND :tdate */ 
                $get_count->bindParam(':ip', $ip, PDO::PARAM_STR);
               /*  $get_count->bindParam(':fdate', $fdate, PDO::PARAM_STR);
                $get_count->bindParam(':tdate', $tdate, PDO::PARAM_STR); */
                $get_count->execute();
				if($get_count -> rowCount() > 0){
                $list_res = $get_count->fetch(PDO::FETCH_ASSOC);
               /*  $response['sublist'][] = $list_res; */
			  
    //start
	http_response_code(200);
	$response = array(
    "error" => false,
    "message" => "Data Found",
			 "intakeoutputlist" => array(
                array("title" => "Intake Cumulative Total", "value" => $list_res['in_take']),
                array("title" => "Output Cumulative Total", "value" => $list_res['Output']),
                array("title" => "Balance", "value" => strval(number_format(($list_res['in_take']-$list_res['Output']),2))),
                array("title" => "Kilo Calories Cumulative Total", "value" => $list_res['Kilo'])
               
            ),
   
    //end	
);
				}else{
					http_response_code(503);
			  $response['error']= true;
	          $response['message']="No Data Found";
				}
				
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
   }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
}
 
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
     
     //"Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>