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
$doccode = trim($data->doccode);
$accesskey = trim($data->accesskey);
$date = date('Y-m-d', strtotime($data->date));
$costenter=$data->costcenter;
$list=array();
try {
if(!empty($accesskey)&& !empty($doccode)&& !empty($costenter)){

$check = $pdoread -> prepare("SELECT `userid`,`cost_center`,dayname(:date) as dayname,date_format(:date,'%d-%b-%Y') AS selectdate FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status` ='Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->bindParam(':date', $date, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
//access verified//
if($check -> rowCount() > 0){

	
	 http_response_code(200);
	$response['error'] = false;
	$response['message'] = "Data found";
	$response['doctorid'] = $doccode;
	
	$get_availability_data = $pdoread->prepare("SELECT `fdate` FROM `doctor_availability` WHERE `DoctorCode`=:doctorid AND `estatus`='Active' AND :selectdate IN (`fdate`)");
                $get_availability_data->bindParam(':doctorid', $doccode, PDO::PARAM_STR);
                $get_availability_data->bindParam(':selectdate', $date, PDO::PARAM_STR);
                $get_availability_data->execute();
                if ($get_availability_data->rowCount()== 0) {
	//$dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	$dayNames = array($result['dayname']);
foreach ($dayNames as $dayName) {
	$response2 = array();
$list = $pdoread -> prepare("SELECT DISTINCT `day_name`,`status` FROM `doctor_timings` WHERE `doctor_code` = :doccode AND `location_cd` = :costenter AND `status` not in ('Inactive','Removed') AND `day_name`=:dayName and selecteddate=:date");
$list->bindParam(':doccode', $doccode, PDO::PARAM_STR);
$list->bindParam(':date', $date, PDO::PARAM_STR);
$list->bindParam(':costenter', $costenter, PDO::PARAM_STR);
$list->bindParam(':dayName', $dayName, PDO::PARAM_STR);
$list -> execute();
if($list->rowCount()>0){
	
	$slot = $pdoread->prepare("SELECT `slotgap` FROM `doctor_timings` WHERE `location_cd` = :costenter AND `doctor_code` = :doccode AND `status` = 'Active'  AND `day_name` = :day_name and selecteddate=:date");
	$slot->bindParam(':doccode', $doccode, PDO::PARAM_STR);
	$slot->bindParam(':date', $date, PDO::PARAM_STR);
	$slot->bindParam(':costenter', $costenter, PDO::PARAM_STR);
	$slot->bindParam(':day_name', $result['dayname'], PDO::PARAM_STR);
	$slot -> execute();
	if($slot->rowCount()>0){
	$slotres = $slot->fetch(PDO::FETCH_ASSOC);
	$response['slotgap'] = $slotres['slotgap'];
	$slotgap=$slotres['slotgap'];
	}else{
		
		$slotgap = "0";
	}
	$i = 0;
	while($listres = $list->fetch(PDO::FETCH_ASSOC)){
		
		
		if($listres['status'] == 'Active'){
			
			$msql = $pdoread -> prepare("SELECT DATE_FORMAT(`from_time`,'%h:%i %p') AS fromdate,DATE_FORMAT(`to_time`,'%h:%i %p') AS to_time,slotgap FROM `doctor_timings` WHERE `doctor_code` = :doccode AND `location_cd` = :costenter AND `day_name` = :day_name and selecteddate=:date AND `status` not in ('Inactive','Removed')");
$msql->bindParam(':doccode', $doccode, PDO::PARAM_STR);
$msql->bindParam(':date', $date, PDO::PARAM_STR);
$msql->bindParam(':costenter', $costenter, PDO::PARAM_STR);
$msql->bindParam(':day_name', $listres['day_name'], PDO::PARAM_STR);
$msql -> execute();
 
while($msqlres = $msql->fetch(PDO::FETCH_ASSOC)){
	 
	$temp1=[
	"fromdate"=>$msqlres['fromdate'],
	"fromdatetitle"=>'From',
	"to_time"=>$msqlres['to_time'],
	"todatetitle"=>'To',
	"addhour"=>'Add Hour',
	
	
	];
		array_push($response2,$temp1);
	
}
			
		}else{
			
			$temp1=[
	"fromdate"=>"",
	"fromdatetitle"=>'From',
	"to_time"=>"",
	"todatetitle"=>'To',
	"addhour"=>'Add Hour',
	
	
	];
		array_push($response2,$temp1);
			
		}
		$i++;
		
		$temp=[
		"dayname"=>$result['selectdate'].' - '. $listres['day_name'],
		"available"=>$listres['status'],
		"slotgap"=>$slotgap,
		"slotgaptitle"=>"Each Slot Duration",
		"value"=>$response2,
		
		];
		
		array_push($response1,$temp);
		
		
	}
}else{

  $temp1=[
	"fromdate"=>"",
	"fromdatetitle"=>'From',
	"to_time"=>"",
	"todatetitle"=>'To',
	"addhour"=>'Add Hour',
	
	
	];
	array_push($response2,$temp1);
	
//var_dump($temp1);
	$temp=[
		"dayname"=> $result['selectdate'].' - '.$dayName,
		//"daynametitle"=>"dayname",
		"available"=>"Inactive",
		//"availabletitle"=>"available",
		"slotgap"=>"",
		"slotgaptitle"=>"Each Slot Duration",
		"value"=>$response2,
		
		];
		
		array_push($response1,$temp);
	

}

}
}else{

  $temp1=[
	"fromdate"=>"",
	"fromdatetitle"=>'From',
	"to_time"=>"",
	"todatetitle"=>'To',
	"addhour"=>'Add Hour',
	
	
	];
	array_push($response2,$temp1);
	
//var_dump($temp1);
	$temp=[
		"dayname"=> $result['selectdate'].' - '.$result['dayname'],
		"available"=>"Inactive",
		"slotgap"=>"",
		"slotgaptitle"=>"Each Slot Duration",
		"value"=>$response2,
		
		];
		
		array_push($response1,$temp);
	

}

$response['loopdata']=$response1;

					
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
} catch(PDOException $e) {
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>