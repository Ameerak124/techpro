<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$invoice_no = trim($data->invoice_no);
$doctorcode = trim($data->doctorcode);
$category=trim($data->category);
$selectdate = date('Y-m-d', strtotime($data->selectdate));
$response=array();
$response1=array();
 $location=''; 
	  $doctor_code=''; 
	  $umrno=''; 
	  $url=''; 
	  $date=''; 
	  $slot=''; 
	  $slotperiod=$array_of_time.' - '.$nexttime;
	  $requisition_no=''; 
	  $bill_no=''; 
	  $transid=''; 
	  $patient_name=''; 
	  $patient_age=''; 
	  $invoice_no=''; 
	  $fees=''; 
	  $gender=''; 
	  $patient_number=''; 
	  $patient_mail=''; 
	  $vip_status=''; 
	  $age=''; 
	  $paymentstatus=''; 
	  $paymentcolour=''; 
	  $timecolour=''; 
	  $costcolour=''; 
	  $slotstatus='Unbooked'; 
	  $slotstatustitle='Slot Available on'; 
	  $timer='15:00';
	  $slotcolour='#07de6b';
	  $pendingslot='NO';
try {

if(!empty($accesskey)&& !empty($doctorcode) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center`,concat(:baseurl,'/images/add.png') as addcolour,concat(:baseurl,'/images/video-pause-button.png') as pauseclour,accesskey FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
$addcolour=$result['addcolour'];
$pauseclour=$result['pauseclour'];
$accesskey_web=$result['accesskey'];

 $get_availability_data = $pdoread->prepare("SELECT `fdate` FROM `doctor_availability` WHERE `DoctorCode`=:doctorid AND `estatus`='Active' AND :selectdate IN (`fdate`)");
                $get_availability_data->bindParam(':doctorid', $doctorcode, PDO::PARAM_STR);
                $get_availability_data->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
                $get_availability_data->execute();
                if ($get_availability_data->rowCount() > 0) {
					http_response_code(503);
                    $response['error'] = true;
                    $response['message'] = "Doctor Unavailable On Selected Date";
                } else {


$check_getslot = $pdoread -> prepare("SELECT IFNULL(min(from_time),'NO') AS fdate,IFNULL(max(to_time),'NO') AS tdate, IFNULL(slotgap,0) AS slotgap FROM `doctor_timings` WHERE `doctor_code`=:doctorcode AND `status`='Active'  AND location_cd=:cost_center AND day_name=DAYNAME(:selectdate) and selecteddate=:selectdate order by sno desc limit 1");
$check_getslot->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
$check_getslot->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$check_getslot->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
$check_getslot -> execute();
$result_slot = $check_getslot->fetch(PDO::FETCH_ASSOC);	
if($check_getslot -> rowCount() > 0 && $result_slot['fdate']!='NO' && $result_slot['tdate']!='NO'){

 $response1 = array ();
 $date = new DateTime('now', new DateTimeZone('Asia/Kolkata'));
 
 $currentdatetime    = strtotime ($date->format('d-m-Y H:i:s'));
 
 $fdate_time=$selectdate.' '.$result_slot['fdate'];
 $tdate_time=$selectdate.' '.$result_slot['tdate'];
 
 
 $start_time    = strtotime ($fdate_time);
 $end_time      = strtotime ($tdate_time);
if($result_slot['slotgap']!='0'){
$fifteen_mins  = $result_slot['slotgap'] * 60;
}else{
	
$fifteen_mins  = 15 * 60;	
}
while ($start_time <= $end_time){

   $array_of_time= date ("h:i A", $start_time);
   $array_of_time1= date ("H:i:00", $start_time);
   
$check_getslot_tt = $pdoread -> prepare("SELECT  `slotgap` FROM `doctor_timings` WHERE `doctor_code`=:doctorcode AND `status`='Active'  AND location_cd=:cost_center AND day_name=DAYNAME(:selectdate) AND :slots between `from_time` AND `to_time` and selecteddate=:selectdate order by sno desc limit 1");
$check_getslot_tt->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
$check_getslot_tt->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$check_getslot_tt->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
$check_getslot_tt->bindParam(':slots', $array_of_time1, PDO::PARAM_STR);
$check_getslot_tt -> execute();
if($check_getslot_tt -> rowCount() > 0){
   
   
   

   
 /*   $getitem = $pdo -> prepare("SELECT location, doctor_code,umrno, IF(`consultation_type`='Physical' ,'',Concat('https://meet.daylo.com/r/',MD5(`bill_no`))) As url ,'' as videoicon,DATE_FORMAT(`date`,'%d-%b-%Y') AS date, DATE_FORMAT(slot,'%h:%i %p')AS slot,concat(DATE_FORMAT(slot,'%h:%i %p'),' - ',DATE_FORMAT(DATE_ADD(slot, INTERVAL :slotgap MINUTE),'%h:%i %p')) AS slotperiod, requisition_no, bill_no,transid,patient_name,CONCAT('Age : ',TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs - ' ,`gender`)AS patient_age,(CASE WHEN invoice_no='OTHERS' THEN ''ELSE  invoice_no END) AS invoice_no,if(amount = '0','',CONCAT('₹',`amount`,' /-')) AS fees,gender, patient_number, patient_mail, vip_status ,CONCAT(TIMESTAMPDIFF(YEAR, patient_age, CURDATE()))AS age ,IF(`amount`='0','Not Paid','Paid')  as paymentstatus, IF(`amount`='0','#a51e23','#236525') as paymentcolour, '#2CBA32' as timecolour,  '#2CBA32' as costcolour,slot_status,if(slot_status ='hold','#eb9f34',if(slot_status='booked' && amount = '0','#a51e23','#2CBA32')) as slotcolour FROM patient_details inner join patient_details on patient_logins.mobile_no=patient_details.patient_number WHERE  slot_status not in ('Cancelled','Unhold') and doctor_code=:doctorcode AND location=:branch AND date=:selectdate  AND slot = :slot"); */
   $getitem = $pdoread -> prepare("SELECT location, doctor_code,umrno, IF(`consultation_type`='Physical' ,'',Concat('https://techpro.medicoveronline.com/doctor-e-consultation-mobile.html?Accesskey=',:docaccess,'&Bookingid=',patient_details.transid,'&regnumber=',IFNULL(patient_logins.userid,''))) As url ,'' as videoicon,DATE_FORMAT(`date`,'%d-%b-%Y') AS date, DATE_FORMAT(slot,'%h:%i %p')AS slot,concat(DATE_FORMAT(slot,'%h:%i %p'),' - ',DATE_FORMAT(DATE_ADD(slot, INTERVAL :slotgap MINUTE),'%h:%i %p')) AS slotperiod, requisition_no, bill_no,transid,patient_details.patient_name,CONCAT('Age : ',TIMESTAMPDIFF(YEAR, patient_details.`patient_age`, CURDATE()),'Yrs - ' ,`gender`)AS patient_age,(CASE WHEN invoice_no='OTHERS' THEN ''ELSE  invoice_no END) AS invoice_no,if(amount = '0','',CONCAT('₹',`amount`,' /-')) AS fees,gender, patient_number, patient_mail, vip_status ,CONCAT(TIMESTAMPDIFF(YEAR, patient_details.patient_age, CURDATE()))AS age ,IF(`amount`='0','Not Paid','Paid')  as paymentstatus, IF(`amount`='0','#a51e23','#236525') as paymentcolour, '#2CBA32' as timecolour,  '#2CBA32' as costcolour,slot_status,if(slot_status ='hold','#eb9f34',if(slot_status='booked' && amount = '0','#a51e23','#2CBA32')) as slotcolour FROM patient_details LEFT join patient_logins on patient_logins.mobile_no=patient_details.patient_number WHERE  slot_status not in ('Cancelled','Unhold','Rescheduled') and doctor_code=:doctorcode AND location=:branch AND date=:selectdate  AND slot = :slot");
/*    $getitem = $pdo -> prepare("SELECT location, doctor_code,umrno,  'https://techpro.medicoveronline.com/queue-management-new.html' as url,DATE_FORMAT(`date`,'%d-%b-%Y') AS date, DATE_FORMAT(slot,'%h:%i %p')AS slot,concat(DATE_FORMAT(slot,'%h:%i %p'),' - ',DATE_FORMAT(DATE_ADD(slot, INTERVAL :slotgap MINUTE),'%h:%i %p')) AS slotperiod, requisition_no, bill_no,transid,patient_name,CONCAT('Age : ',TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs - ' ,`gender`)AS patient_age,(CASE WHEN invoice_no='OTHERS' THEN ''ELSE  invoice_no END) AS invoice_no,if(amount = '0','',CONCAT('₹',`amount`,' /-')) AS fees,gender, patient_number, patient_mail, vip_status ,CONCAT(TIMESTAMPDIFF(YEAR, patient_age, CURDATE()))AS age ,IF(`amount`='0','Not Paid','Paid')  as paymentstatus, IF(`amount`='0','#a51e23','#236525') as paymentcolour, '#2CBA32' as timecolour,  '#2CBA32' as costcolour,slot_status,if(slot_status ='hold','#eb9f34','#a51e23') as slotcolour FROM patient_details WHERE  slot_status not in ('Cancelled','Unhold') and doctor_code=:doctorcode AND location=:branch AND date=:selectdate  AND slot = :slot"); */
		$getitem -> bindParam(':slotgap', $result_slot['slotgap'], PDO::PARAM_STR);
		$getitem -> bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
		$getitem -> bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
		$getitem -> bindParam(':slot', $array_of_time1, PDO::PARAM_STR);
		$getitem -> bindParam(':docaccess', $accesskey_web, PDO::PARAM_STR);
		$getitem -> bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
		$getitem -> execute();
  
   if($getitem->rowCount()>0 ){
	 
	 
   $appdata = $getitem->fetch(PDO::FETCH_ASSOC);	
  if($appdata['slot_status'] =='booked'){ 

   $location=$appdata['location'];
   $doctor_code=$appdata['doctor_code'];
   $umrno=$appdata['umrno'];
   $url=$appdata['url'];
   $date=$appdata['date'];
   $slot=$appdata['slot'];
   $slotperiod=$appdata['slotperiod'];
   $requisition_no=$appdata['requisition_no'];
   $bill_no=$appdata['bill_no'];
   $transid=$appdata['transid'];
   $patient_name=$appdata['patient_name'];
   $patient_age=$appdata['patient_age'];
   $invoice_no=$appdata['invoice_no'];
   $fees=$appdata['fees'];
   $gender=$appdata['gender'];
   $patient_number=$appdata['patient_number'];
   $patient_mail=$appdata['patient_mail'];
   $vip_status=$appdata['vip_status'];
   $age=$appdata['age'];
   $paymentstatus=$appdata['paymentstatus'];
   $paymentcolour=$appdata['paymentcolour'];
   $timecolour=$appdata['paymentcolour'];
   $costcolour=$appdata['costcolour'];
   $slotstatus=$appdata['slot_status'];
   $slotstatustitle='';
   $timer='';
   $slotcolour=$appdata['slotcolour'];
   $videoicon=$appdata['videoicon'];
   
  }else{
	  
      $location=''; 
	  $doctor_code=''; 
	  $umrno=''; 
	  $url=''; 
	  $date=''; 
	  $slot=''; 
	  $slotperiod='';
	  $requisition_no=''; 
	  $bill_no=''; 
	  $transid=''; 
	  $patient_name=''; 
	  $patient_age=''; 
	  $invoice_no=''; 
	  $fees=''; 
	  $gender=''; 
	  $patient_number=''; 
	  $patient_mail=''; 
	  $vip_status=''; 
	  $age=''; 
	  $paymentstatus=''; 
	  $paymentcolour=''; 
	  $timecolour=''; 
	  $costcolour=''; 
	  $slotstatus=$appdata['slot_status']; 
	  $slotstatustitle='Slot is on hold'; 
	  $timer='15:00';
	  $slotcolour=$appdata['slotcolour'];
	  $videoicon='';
  }	  
   
   }else{
	  $pendingslot='Yes';
	 $nexttime= date ("h:i A", ($start_time+$fifteen_mins));
	  $location=''; 
	  $doctor_code=''; 
	  $umrno=''; 
	  $url=''; 
	  $date=''; 
	  $slot=''; 
	  $slotperiod=$array_of_time.' - '.$nexttime;
	  $requisition_no=''; 
	  $bill_no=''; 
	  $transid=''; 
	  $patient_name=''; 
	  $patient_age=''; 
	  $invoice_no=''; 
	  $fees=''; 
	  $gender=''; 
	  $patient_number=''; 
	  $patient_mail=''; 
	  $vip_status=''; 
	  $age=''; 
	  $paymentstatus=''; 
	  $paymentcolour=''; 
	  $timecolour=''; 
	  $costcolour=''; 
	  $slotstatus='Unbooked'; 
	  $slotstatustitle='Available Slot'; 
	  $timer='15:00';
	  $slotcolour='#07de6b';
	   $videoicon='';
	   
	   $nexttime="";
   }
   $validatedate= strtotime (date('Y-m-d'));
 $selectdatess= strtotime ($selectdate);
 
   if($selectdatess<$validatedate){
	   $pendingslot='Yes';
   if($slotstatus=='booked'){
   $temp=[
   "time"=>$array_of_time,
   "location"=> $location,
   "doctor_code"=>$doctor_code,
   "umrno"=>$umrno,
   "url"=>$url,
   "date"=>$date,
   "slot"=>$slot,
   "slotperiod"=>$slotperiod,
   "requisition_no"=>$requisition_no,
   "bill_no"=>$bill_no,
   "transid"=>$transid,
   "patient_name"=>$patient_name,
   "patient_age"=>$patient_age,
   "invoice_no"=>$invoice_no,
   "fees"=>$fees,
   "gender"=>$gender,
   "patient_number"=>$patient_number,
   "patient_mail"=>$patient_mail,
   "vip_status"=>$vip_status,
   "age"=>$age,
   "paymentstatus"=>$paymentstatus,
   "paymentcolour"=>$paymentcolour,
   "timecolour"=>$timecolour,
   "costcolour"=>$costcolour,
   "slotstatus"=>$slotstatus,
   "slotstatustitle"=>$slotstatustitle,
   "timer"=>$timer,
   "millifuture"=>'900000',
   "countdown"=>'1000',
   "ioscountdown"=>'900',
   "slotcolour"=>$slotcolour,
    "pauseicon"=>$pauseclour,
   "addicon"=>$addcolour,
   "videoicon"=>$videoicon,
   ];
   array_push($response1,$temp);
   }
   }else{
	 
	if($currentdatetime<=$start_time){

   $temp=[
   "time"=>$array_of_time,
   "location"=> $location,
   "doctor_code"=>$doctor_code,
   "umrno"=>$umrno,
   "url"=>$url,
   "date"=>$date,
   "slot"=>$slot,
   "slotperiod"=>$slotperiod,
   "requisition_no"=>$requisition_no,
   "bill_no"=>$bill_no,
   "transid"=>$transid,
   "patient_name"=>$patient_name,
   "patient_age"=>$patient_age,
   "invoice_no"=>$invoice_no,
   "fees"=>$fees,
   "gender"=>$gender,
   "patient_number"=>$patient_number,
   "patient_mail"=>$patient_mail,
   "vip_status"=>$vip_status,
   "age"=>$age,
   "paymentstatus"=>$paymentstatus,
   "paymentcolour"=>$paymentcolour,
   "timecolour"=>$timecolour,
   "costcolour"=>$costcolour,
   "slotstatus"=>$slotstatus,
   "slotstatustitle"=>$slotstatustitle,
   "timer"=>$timer,
   "millifuture"=>'900000',
   "countdown"=>'1000',
   "ioscountdown"=>'900',
   "slotcolour"=>$slotcolour,
    "pauseicon"=>$pauseclour,
   "addicon"=>$addcolour,
    "videoicon"=>$videoicon,
   ];
   array_push($response1,$temp);
	}else{
		$pendingslot='NO';
	if($slotstatus=='booked'){
		
   $temp=[
   "time"=>$array_of_time,
   "location"=> $location,
   "doctor_code"=>$doctor_code,
   "umrno"=>$umrno,
   "url"=>$url,
   "date"=>$date,
   "slot"=>$slot,
   "slotperiod"=>$slotperiod,
   "requisition_no"=>$requisition_no,
   "bill_no"=>$bill_no,
   "transid"=>$transid,
   "patient_name"=>$patient_name,
   "patient_age"=>$patient_age,
   "invoice_no"=>$invoice_no,
   "fees"=>$fees,
   "gender"=>$gender,
   "patient_number"=>$patient_number,
   "patient_mail"=>$patient_mail,
   "vip_status"=>$vip_status,
   "age"=>$age,
   "paymentstatus"=>$paymentstatus,
   "paymentcolour"=>$paymentcolour,
   "timecolour"=>$timecolour,
   "costcolour"=>$costcolour,
   "slotstatus"=>$slotstatus,
   "slotstatustitle"=>$slotstatustitle,
   "timer"=>$timer,
   "millifuture"=>'900000',
   "countdown"=>'1000',
   "ioscountdown"=>'900',
   "slotcolour"=>$slotcolour,
    "pauseicon"=>$pauseclour,
   "addicon"=>$addcolour,
    "videoicon"=>$videoicon,
   ];
   array_push($response1,$temp);
   }
	}
   }
  
}
 $start_time += $fifteen_mins;
}
/* if($pendingslot=='NO'){
   $temp=[
   "time"=>"Waiting List",
   "location"=> $location,
   "doctor_code"=>$doctor_code,
   "umrno"=>$umrno,
   "url"=>$url,
   "date"=>$date,
   "slot"=>$slot,
   "slotperiod"=>$slotperiod,
   "requisition_no"=>$requisition_no,
   "bill_no"=>$bill_no,
   "transid"=>$transid,
   "patient_name"=>$patient_name,
   "patient_age"=>$patient_age,
   "invoice_no"=>$invoice_no,
   "fees"=>$fees,
   "gender"=>$gender,
   "patient_number"=>$patient_number,
   "patient_mail"=>$patient_mail,
   "vip_status"=>$vip_status,
   "age"=>$age,
   "paymentstatus"=>$paymentstatus,
   "paymentcolour"=>$paymentcolour,
   "timecolour"=>$timecolour,
   "costcolour"=>$costcolour,
   "slotstatus"=>$slotstatus,
   "slotstatustitle"=>$slotstatustitle,
   "timer"=>$timer,
   "millifuture"=>'900000',
   "countdown"=>'1000',
   "ioscountdown"=>'900',
   "slotcolour"=>$slotcolour,
   "pauseaddicon"=>$pauseclour,
   "addicon"=>$addcolour,
   ];
   array_push($response1,$temp);
} */

http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
$response['appointmentlist'] = $response1;

	
/* 		}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Please update slotgap";
} */
   
     		
	
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="No slots";
}
	
					
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