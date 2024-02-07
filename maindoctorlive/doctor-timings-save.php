<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$loopdata = array();
$from= array();
$to = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$date = date('Y-m-d', strtotime($data->date));
$doccode= $data->doctorid;
$docslotgap= $data->docslotgap;
$loopdata= ($data->loopdata);
try {
if(!empty($accesskey)&& !empty($doccode)){
$check = $pdoread -> prepare("SELECT `userid`,`cost_center`,dayname(:date) as dayname,username as empname FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status` ='Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->bindParam(':date', $date, PDO::PARAM_STR);
$check -> execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
$costcenter=$result['cost_center'];

		


//access verified//
if($check -> rowCount() > 0){
	
	$start = strtotime($data->date);
$monthyear = strtotime(date("F-Y")); 
$finish = strtotime("+2 months", strtotime(date("F-Y")));
if($start<=$finish && $start>=$monthyear){
	
	
$status = $pdo4 -> prepare("UPDATE `doctor_timings` SET `status`='Inactive',`modifiedon`=CURRENT_TIMESTAMP,`modifiedby`=:userid WHERE `doctor_code` = :doccode AND `day_name`=:dayname and selecteddate =:date");
$status->bindParam(':doccode', $doccode, PDO::PARAM_STR);
$status->bindParam(':date', $date, PDO::PARAM_STR);
$status->bindParam(':dayname', $result['dayname'], PDO::PARAM_STR);
$status->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$status -> execute();

foreach($loopdata as $row){

		if($row->available == "Active"){
			if($docslotgap>=5 && $docslotgap<59){
foreach($row->value as $value){ 
$from = explode(",",$value->fromdate);
$to = explode(",",$value->to_time); 
$num = count($from);
for($i = 0;$i<$num;$i++){
	
	$ftime=date('H:i',strtotime($from[$i]));
$ttime=date('H:i',strtotime($to[$i]));
if($ftime<=$ttime){
	$dayname =explode('- ',$row->dayname);
	
$query=$pdo4->prepare("INSERT INTO `doctor_timings`(`sno`, `doctor_code`, `location_cd`, `day_name`,`from_time`,`to_time`, `status`, `createdon`, `createdby`, `modifiedon`, `modifiedby`,slotgap,`selecteddate`) VALUES (NULL,:doccode,:doclocation,:dayname,TIME_FORMAT(:fromtime, '%H:%i:%s'),TIME_FORMAT(:totime, '%H:%i:%s'),:available,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:docslotgap,:date)");
$query->bindParam(':doccode', $doccode, PDO::PARAM_STR);
$query->bindParam(':date', $date, PDO::PARAM_STR);
$query->bindParam(':doclocation', $costcenter, PDO::PARAM_STR);
$query->bindParam(':dayname', $dayname[1], PDO::PARAM_STR);
$query->bindParam(':available', $row->available, PDO::PARAM_STR);
$query->bindParam(':fromtime',date_format(date_create($from[$i]),"H:i:00"), PDO::PARAM_STR);
$query->bindParam(':totime', date_format(date_create($to[$i]),"H:i:00"), PDO::PARAM_STR);
$query->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$query->bindParam(':docslotgap',$docslotgap, PDO::PARAM_STR);
$query->execute();


$update = $pdo4->prepare("UPDATE `doctor_availability` SET `estatus`='Inactive' WHERE `DoctorCode`=:doctorid AND `estatus`='Active' AND :selectdate IN (`fdate`)");
	$update->bindParam(':doctorid', $doccode, PDO::PARAM_STR);
	$update->bindParam(':selectdate', $date, PDO::PARAM_STR);
	$update -> execute();
	if($query->rowCount()>0){
	http_response_code(200);
	$response['error']= false;
$response['message']="Data Saved";
}else{
	http_response_code(503);
	$response['error']= true;
$response['message']="Sorry! Please try again";
	}
	}else{
http_response_code(503);
$response['error']= true;
$response['message']="Please select proper timings";
}
}
}
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="Please select proper slotgap";
}
	}else{
		
		foreach($row->value as $value){ 
$from = explode(",",$value->fromdate);
$to = explode(",",$value->to_time); 
$num = count($from);
for($i = 0;$i<$num;$i++){
	
	$dayname =explode('- ',$row->dayname);
		$query=$pdo4->prepare("INSERT INTO `doctor_timings`(`sno`, `doctor_code`, `location_cd`, `day_name`,`from_time`,`to_time`, `status`, `createdon`, `createdby`,slotgap,selecteddate) VALUES (NULL,:doccode,:doclocation,:dayname,'00:00:00','00:00:00',:available,CURRENT_TIMESTAMP,:userid,:docslotgap,:date)");
$query->bindParam(':doccode', $doccode, PDO::PARAM_STR);
$query->bindParam(':date', $date, PDO::PARAM_STR);
$query->bindParam(':doclocation', $costcenter, PDO::PARAM_STR);
$query->bindParam(':dayname', $dayname[1], PDO::PARAM_STR);
$query->bindParam(':available', $row->available, PDO::PARAM_STR);
$query->bindParam(':docslotgap', $docslotgap, PDO::PARAM_STR);
$query->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$query->execute();
if($query->rowCount()>0){
	http_response_code(200);
	$response['error']= false;
$response['message']="Data Saved";
}else{
	http_response_code(503);
	$response['error']= true;
$response['message']="Sorry! Please try again";
	}
/* $update = $pdo->prepare("UPDATE `doctor_availability` SET `estatus`='Active' WHERE `DoctorCode`=:doctorid AND `estatus`='Active' AND :selectdate IN (`fdate`)");
	$update->bindParam(':doctorid', $doccode, PDO::PARAM_STR);
	$update->bindParam(':selectdate', $date, PDO::PARAM_STR);
	$update -> execute(); */
}
	}
}

}
}else{
	            http_response_code(503);
				$response['error'] = true;
				$response['message'] = "You can schedule upto 2 months only";
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
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = $dayname[1];
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>