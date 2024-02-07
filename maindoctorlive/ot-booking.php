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
$otcode=$data->otcode;
$ipno=$data->ipno;
$duration=$data->duration;
$starttime=$data->starttime;
$selectdate=date_format(date_create($data->selectdate),"Y-m-d");
$accesskey=$data->accesskey; 
// input data//
$response=array();
try {
if(!empty($otcode)&& !empty($ipno)&& !empty($accesskey)&& !empty($selectdate)&& !empty($starttime)&& !empty($duration)){

$check=$pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active' ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check->rowCount() >0){
	$create=$pdoread->prepare("SELECT IFNULL(MAX(`booking_code`),CONCAT('OTB',DATE_FORMAT(CURRENT_TIMESTAMP,'%y%m'),'00000')) AS otno FROM `ot_booking` WHERE `booking_code` LIKE '%OTB%'");
	$create->execute();
	$grn=$create->fetch(PDO::FETCH_ASSOC);
	$booking=$grn['otno'];
	$booking_code=++$booking;	
//Access key verified sucessfully"//
$query0=$pdoread->prepare("SELECT  `admissionno` ,`patientname`,`patientage`,`patientgender`,`procedure_surgery`,`consultantname` AS doctorname, `registration`.`department` AS dept   FROM `registration` LEFT JOIN `ot_booking` ON  `registration`.`admissionno`=`ipno` WHERE `registration`.`admissionno`=:ipno");
 $query0->bindParam(':ipno', $ipno, PDO::PARAM_STR);
 $query0->execute();
 //query0 executed//
 if($query0->rowCount()>0) {
 $result1=$query0->fetch(PDO::FETCH_ASSOC);
//query1 executed//
$myArray=explode(',',$starttime);
if($myArray[0] != ''){
$check1 = $pdoread -> prepare("SELECT E.mydate,DATE_FORMAT(E.mydate,'%h:%i %p') AS dispalytime,if(v.start_time != '','',DATE_FORMAT(E.mydate,'%H')) AS slottime,SUM((CASE WHEN if(v.start_time != '','Booked','Vacant') = 'Booked' THEN 1 ELSE 0 END)) AS slotstatus,SUM(CASE WHEN if(E.mydate = CONCAT(:selectdate,' ',DATE_FORMAT(:exp1,'%H:00:00')) OR E.mydate = CONCAT(:selectdate,' ',DATE_FORMAT(:exp2,'%H:00:00')) OR E.mydate = CONCAT(:selectdate,' ',DATE_FORMAT(:exp3,'%H:00:00')),'Hold','Vacant') = 'Hold' THEN 1 ELSE 0 END) AS bookstatus FROM (SELECT DATE(:selectdate) + interval (seq * 60) Minute as mydate FROM seq_0_to_23) AS E LEFT JOIN (SELECT `otcode`,`start_time`,`end_time` FROM `ot_booking` WHERE `otcode` = :otcode AND DATE(`start_time`) = :selectdate) AS v ON E.mydate = v.start_time WHERE E.mydate = CONCAT(:selectdate,' ',DATE_FORMAT(:exp1,'%H:00:00')) OR E.mydate = CONCAT(:selectdate,' ',DATE_FORMAT(:exp2,'%H:00:00')) OR E.mydate = CONCAT(:selectdate,' ',DATE_FORMAT(:exp3,'%H:00:00')) ORDER BY E.mydate ASC");
$check1->bindValue(':exp1', $myArray[0], PDO::PARAM_STR);
$check1->bindValue(':exp2', $myArray[1], PDO::PARAM_STR);
$check1->bindValue(':exp3', $myArray[2], PDO::PARAM_STR);
$check1->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
$check1->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$check1 -> execute();
if($check1 -> rowCount() > 0){
	$row1 = $check1->fetch(PDO::FETCH_ASSOC);
		if($row1['slotstatus'] > 0 && $row1['bookstatus'] > 0){
			$response['error']=true;
		 $response['message']='Sorry! someone has booking those slots';
		}else if($row1['slotstatus'] < 1 && $row1['bookstatus'] > 0){
//not mistake
foreach($myArray as $value)
	{
	$query1=$pdoread->prepare("SELECT E.mydate,DATE_FORMAT(E.mydate,'%h:%i %p') AS dispalytime,if(v.start_time != '','Booked','Vacant') AS slotstatus  FROM (SELECT DATE(:selectdate) + interval (seq * 60) Minute as mydate FROM seq_0_to_23) AS E LEFT JOIN (SELECT `otcode`,`start_time`,`end_time` FROM `ot_booking` WHERE `otcode` =:otcode AND DATE((`start_time`) = :selectdate)) AS v ON E.mydate = v.start_time ORDER BY E.mydate ASC");
$query1->bindParam(':selectdate', $value, PDO::PARAM_STR);
$query1->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$query1->execute();
$OT1=$con->prepare("SELECT `branch`,`otcode`,`otname`,`otnumber` FROM `ot_master` WHERE `otcode` = :otcode");
$OT1->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$OT1->execute();
$RES1=$OT1->fetch(PDO::FETCH_ASSOC);
//query1 executed//

if($query1->rowCount()>0) {
while($result2=$query1->fetch(PDO::FETCH_ASSOC)) {
//
	if($result2['mydate'] ==$value && $result2['slotstatus']=='Vacant'){
 $list=$pdo4->prepare ("INSERT INTO `ot_booking`(`sno`, `booking_code`,`branch`,`booking_type`, `otcode`, `otname`, `ottheatre`, `ipno`, `patient_name`, `gender`, `doctor_name`, `department`, `surgery`, `duration`, `start_time`, `start_by`, `end_time`, `end_by`, `created_on`, `created_by`, `updated_on`, `updated_by`, `status`,`remarks`) VALUES
(NULL,:booking_code,:otbranch,'General',:otcode,:otroom,:ottheatre,:ipno,:ptname,:ptgender,:doctor,:department,:surgery,:duration,:starttime,:userid,DATE_ADD(:starttime, INTERVAL 1 HOUR),:userid,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,'Booked',' ') ");
                     $list->bindParam(':otbranch', $result['branch'], PDO::PARAM_STR);
                     $list->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
					 $list->bindParam(':department', $result1['dept'], PDO::PARAM_STR);
	                 $list->bindParam(':otroom', $RES1['otname'], PDO::PARAM_STR);
	                 $list->bindParam(':ottheatre', $RES1['otnumber'], PDO::PARAM_STR);
		             $list->bindParam(':ptname', $result1['patientname'], PDO::PARAM_STR);
			         $list->bindParam(':ptgender', $result1['patientgender'], PDO::PARAM_STR);
			         $list->bindParam(':doctor', $result1['doctorname'], PDO::PARAM_STR);
				     $list->bindParam(':surgery', $result1['procedure_surgery'], PDO::PARAM_STR);
				     $list->bindParam(':booking_code', $booking_code, PDO::PARAM_STR);
				     $list->bindParam(':duration', $duration, PDO::PARAM_STR);
				     $list->bindParam(':starttime', $value, PDO::PARAM_STR);
					 $list->bindParam(':ipno', $ipno, PDO::PARAM_STR);
					 $list->bindParam(':otcode', $otcode, PDO::PARAM_STR);
				     
				 //Insertion of the parameters into DB//
 $list->execute();
} 

}
}
}
}else{
	      http_response_code(503);
		  $response['error']= true;
		 $response['message']='No slots available for booking';
		}
		}else{
		http_response_code(503);
	     $response['error']=true;
		 $response['message']='Please select proper details';
}
if($list->rowCount() > 0){
	http_response_code(200);
	$response['error']=false;
	$response['message']='Your slot is booked';
}else{
	 http_response_code(503);
	 $response['error']=true;
	$response['message']='Sorry! please try again';
}
 }else{
	 http_response_code(503);
	 $response['error']=true;
		 $response['message']='Sorry! someone has booking those slots';
 }
}else{
	    http_response_code(503);
	$response['error']=true;
	$response['message']='You have entered incorrect admission number';
        //query0//
		}
}  else{
	      http_response_code(400);
			$response['error']=true;
	$response['message']='Access denied!';
        //check//
		}
	
      
  }else{
	      http_response_code(400);
	      $response['error']=true;
	 $response['message']='Some Details are Missing';
	   //check//
   }

}
 catch(PDOException $e) {
	 http_response_code(200);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>

