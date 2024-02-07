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
try {

if(!empty($accesskey)&& !empty($doctorcode) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center`,Date_format(CURRENT_TIMESTAMP,'%d-%m-%Y %H:%i') as currenttime FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);

$check_getslot = $pdoread -> prepare("SELECT Concat_ws(' ',:selectdate,`from_time`) AS fdate,Concat_ws(' ',:selectdate,`to_time`) AS tdate, `slotgap` FROM `doctor_timings` WHERE `doctor_code`=:doctorcode AND `status`='Active'  AND location_cd=:cost_center AND day_name=DAYNAME(:selectdate) order by sno desc limit 1");
$check_getslot->bindParam(':doctorcode', $result['userid'], PDO::PARAM_STR);
$check_getslot->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$check_getslot->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
$check_getslot -> execute();
if($check_getslot -> rowCount() > 0){
$result_slot = $check_getslot->fetch(PDO::FETCH_ASSOC);	
 $response1 = array ();
 $start_time    = strtotime ($result_slot['fdate']);
 $end_time      = strtotime ($result_slot['tdate']);
if($result_slot['slotgap']!='0'){
$fifteen_mins  = $result_slot['slotgap'] * 60;
}else{
	$fifteen_mins  = 15 * 60;	
}
while ($start_time <= $end_time){
   $array_of_time= date ("h:i A", $start_time);
   $array_of_time11= date ("H:i:00", $start_time);
    $array_of_time1= date ("d-m-Y h:i", $start_time);
    $array_of_time2=strtotime ($result['currenttime']);
    $array_of_time3=strtotime ($array_of_time1);
	
   $getitem = $pdoread -> prepare("SELECT location, doctor_code,umrno, Concat('https://meet.daylo.com/r/',MD5(`bill_no`)) As url ,DATE_FORMAT(`date`,'%d-%b-%Y') AS date, DATE_FORMAT(slot,'%h:%i %p')AS slot, requisition_no, bill_no,transid,patient_name,CONCAT('Age : ',TIMESTAMPDIFF(YEAR, `patient_age`, CURDATE()),'Yrs - ' ,`gender`)AS patient_age,(CASE WHEN invoice_no='OTHERS' THEN ''ELSE  invoice_no END) AS invoice_no,if(amount = '0','',CONCAT('₹',`amount`,' /-')) AS fees,gender, patient_number, patient_mail, vip_status ,CONCAT(TIMESTAMPDIFF(YEAR, patient_age, CURDATE()))AS age ,IF(`amount`='0','Unpaid','Paid')  as paymentstatus, IF(`amount`='0','#a51e23','#236525') as paymentcolour, '#2CBA32' as timecolour,  '#2CBA32' as costcolour,slot_status FROM patient_details WHERE slot_status='booked' AND doctor_code=:doctorcode AND location=:branch AND date=:selectdate  AND slot = :slot");
		$getitem -> bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
		$getitem -> bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
		$getitem -> bindParam(':slot', $array_of_time11, PDO::PARAM_STR);
		$getitem -> bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
		$getitem -> execute();
  
   if($getitem->rowCount()>0){
   $appdata = $getitem->fetch(PDO::FETCH_ASSOC);	
   
   $location=$appdata['location'];
   $doctor_code=$appdata['doctor_code'];
   $umrno=$appdata['umrno'];
   $url=$appdata['url'];
   $date=$appdata['date'];
   $slot=$appdata['slot'];
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
   
   
   }else{
	   
	  $location=''; 
	  $doctor_code=''; 
	  $umrno=''; 
	  $url=''; 
	  $date=''; 
	  $slot=''; 
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
	   
	   
   }
   if($array_of_time2<=$array_of_time3){
   /* echo $array_of_time2 .'...';
   echo $array_of_time3. '..'; */
   $temp=[
   "time"=>$array_of_time,
   "location"=> $location,
   "doctor_code"=>$doctor_code,
   "umrno"=>$umrno,
   "url"=>$url,
   "date"=>$date,
   "slot"=>$slot,
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
   ];
   array_push($response1,$temp);
   $start_time += $fifteen_mins;
   }else{
http_response_code(503);
	$response['error']= true;
	$response['message']="No slots are available";

   }	   
   
}
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
	http_response_code(503);
	$response['error']= true;
	$response['message']="No slots";
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