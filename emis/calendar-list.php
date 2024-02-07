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
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$response = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
		$check1 = $pdoread -> prepare("SELECT CURRENT_DATE AS today,subdate(current_date, 1) AS yesterday,DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY) AS last7days,Date_format(CURRENT_DATE,'%Y-%m-01') AS startdate,Date_format(CURRENT_DATE,'%Y-%01-01') AS curentyear,DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y-%m-01') AS pstart,DATE_FORMAT(LAST_DAY(CURRENT_DATE - INTERVAL 1 MONTH), '%Y-%m-%d') As lastdate,DATE_FORMAT(CURRENT_DATE - INTERVAL 1 YEAR, '%Y-%m-01') AS pyearstart,DATE_FORMAT(LAST_DAY(CURRENT_DATE - INTERVAL 1 YEAR), '%Y-%m-%d') As lastyeardate,Date_format(date_add(CURRENT_DATE - INTERVAL 1 Year,interval 1 MONTH),'%Y-%m-01') AS startq1,Date_format(date_add(Last_day(CURRENT_DATE - INTERVAL 1 Year),interval 3 MONTH),'%Y-%m-%d') AS endq1,Date_format(date_add(CURRENT_DATE - INTERVAL 1 Year,interval 4 MONTH),'%Y-%m-01') AS startq2,Date_format(date_add(Last_day(CURRENT_DATE - INTERVAL 1 Year),interval 6 MONTH),'%Y-%m-%d') AS endq2,Date_format(date_add(CURRENT_DATE - INTERVAL 1 Year,interval 7 MONTH),'%Y-%m-01') AS startq3,Date_format(date_add(Last_day(CURRENT_DATE - INTERVAL 1 Year),interval 9 MONTH),'%Y-%m-%d') AS endq3,Date_format(date_sub(CURRENT_DATE - INTERVAL 0 Year,interval 2 MONTH),'%Y-%m-01') AS startq4,LAST_DAY(CURRENT_DATE) AS endq4");
		$check1 -> execute();
		 $result1 = $check1->fetch(PDO::FETCH_ASSOC);
		$my_array = array("Today","Yesterday","Last 7 Days","Month Till Today","Month To Yesterday","Year To Yesterday","Previouse Month","Previouse Year","Quarter 1","Quarter 2","Quarter 3","Quarter 4","Custom Dates");
		$my_array1 = array($result1['today'],$result1['yesterday'],$result1['last7days'],$result1['startdate'],$result1['startdate'],$result1['curentyear'],$result1['pstart'],$result1['pyearstart'],$result1['startq1'],$result1['startq2'],$result1['startq3'],$result1['startq4'],$fdate);
		$my_array2 = array($result1['today'],$result1['yesterday'],$result1['today'],$result1['today'],$result1['yesterday'],$result1['yesterday'],$result1['lastdate'],$result1['lastyeardate'],$result1['endq1'],$result1['endq2'],$result1['endq3'],$result1['endq4'],$tdate);
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data Found";
    for($x = 0; $x < sizeof($my_array); $x++){	
	$response['calendarlist'][$x]['type']=$my_array[$x];	
	$response['calendarlist'][$x]['fdate']=$my_array1[$x];	
	$response['calendarlist'][$x]['displayfdate']=date('d-M-Y',strtotime($my_array1[$x]));	
	$response['calendarlist'][$x]['tdate']=$my_array2[$x];	
	$response['calendarlist'][$x]['displaytdate']=date('d-M-Y',strtotime($my_array2[$x]));	
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