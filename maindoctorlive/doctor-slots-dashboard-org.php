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
$branch = trim($data->branch);
$fdate = date('Y-m-d', strtotime($data->fdate));
$tdate = date('Y-m-d', strtotime($data->tdate));
$response=array();
$response1=array();
$response2=array();
$response3=array();
$response4=array();
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

if(!empty($accesskey) ){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, username,`cost_center`,concat(:baseurl,'/images/add.png') as addcolour,concat(:baseurl,'/images/video-pause-button.png') as pauseclour ,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
$addcolour=$result['addcolour'];
$pauseclour=$result['pauseclour'];
$getbranchs = $pdoread->prepare("SELECT location,branch_master.display_name FROM `doctor_master` inner join branch_master on branch_master.cost_center=doctor_master.location WHERE doctor_master.status = 'Active' AND doctor_master.doctor_type='Revenue' group by `location`");
$getbranchs->execute();
 $bookcounttotalcount='0';
 $avlcounttotalcount='0';
while($resultbranch = $getbranchs->fetch(PDO::FETCH_ASSOC)){
	$branch=$resultbranch['location']; 
$branchname=$resultbranch['display_name']; 




$getdoctors = $pdoread->prepare("SELECT doctor_code as doctorcode,doctor_name,branch_master.display_name FROM `doctor_master` inner join branch_master on branch_master.cost_center=doctor_master.location WHERE doctor_master.status = 'Active' AND doctor_master.doctor_type='Revenue' AND doctor_master.location=:location_cd");
    $getdoctors->bindParam(':location_cd', $branch, PDO::PARAM_STR);
	
    $getdoctors->execute();
	
  while($resultdoctor = $getdoctors->fetch(PDO::FETCH_ASSOC)){
$doctorcode=$resultdoctor['doctorcode']; 
$doctorname=$resultdoctor['doctor_name']; 
$branchname=$resultdoctor['display_name']; 
$stmtdate=$pdoread->prepare("select * from 
(select adddate('1900-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date from
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
 (select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4) v
where selected_date between :fdate and :tdate order by selected_date");
 $stmtdate->bindParam(':fdate', $fdate, PDO::PARAM_STR);
 $stmtdate->bindParam(':tdate', $tdate, PDO::PARAM_STR);
 $stmtdate->execute();
 $bookcounttotal='0';
 $avlcounttotal='0';
while($resultdate = $stmtdate->fetch(PDO::FETCH_ASSOC)){
$selectdate=$resultdate['selected_date'];	

 $get_availability_data = $pdoread->prepare("SELECT `fdate` FROM `doctor_availability` WHERE `DoctorCode`=:doctorid AND `estatus`='Active' AND :selectdate IN (`fdate`)");
                $get_availability_data->bindParam(':doctorid', $doctorcode, PDO::PARAM_STR);
                $get_availability_data->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
                $get_availability_data->execute();
                if ($get_availability_data->rowCount() == 0) {
					




$check_getslot = $pdoread -> prepare("SELECT IFNULL(min(from_time),'NO') AS fdate,IFNULL(max(to_time),'NO') AS tdate, IFNULL(slotgap,0) AS slotgap FROM `doctor_timings` WHERE `doctor_code`=:doctorcode AND `status`='Active'  AND location_cd=:cost_center AND day_name=DAYNAME(:selectdate) order by sno desc limit 1");
$check_getslot->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
$check_getslot->bindParam(':cost_center', $branch, PDO::PARAM_STR);
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
$bookcount='0';
$avlcount='0';
while ($start_time <= $end_time){

   $array_of_time= date ("h:i A", $start_time);
   $array_of_time1= date ("H:i:00", $start_time);
   
$check_getslot_tt = $pdoread -> prepare("SELECT  `slotgap` FROM `doctor_timings` WHERE `doctor_code`=:doctorcode AND `status`='Active'  AND location_cd=:cost_center AND day_name=DAYNAME(:selectdate) AND :slots between `from_time` AND `to_time` order by sno desc limit 1");
$check_getslot_tt->bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
$check_getslot_tt->bindParam(':cost_center', $branch, PDO::PARAM_STR);
$check_getslot_tt->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
$check_getslot_tt->bindParam(':slots', $array_of_time1, PDO::PARAM_STR);
$check_getslot_tt -> execute();
if($check_getslot_tt -> rowCount() > 0){
   
   
   
   $getitem = $pdoread -> prepare("SELECT * FROM patient_details WHERE  slot_status not in ('Cancelled','Unhold') and doctor_code=:doctorcode AND location=:branch AND date=:selectdate  AND slot = :slot");
		//$getitem -> bindParam(':slotgap', $result_slot['slotgap'], PDO::PARAM_STR);
		$getitem -> bindParam(':doctorcode', $doctorcode, PDO::PARAM_STR);
		$getitem -> bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
		$getitem -> bindParam(':slot', $array_of_time1, PDO::PARAM_STR);
		$getitem -> bindParam(':branch', $branch, PDO::PARAM_STR);
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
   "slotstatus"=>$slotstatus,
   
   "bookcount"=>'1',
   "avlcount"=>'0',
   ];
     $bookcount=$bookcount + 1;
$avlcount=$avlcount + 0;
   array_push($response1,$temp);
   }
   }else{
	 
	if($currentdatetime<=$start_time){
if($slotstatus=='booked'){
	$count='1';
	$count1='0';
}else{
	$count='0';
	$count1='1';
}
   $temp=[
   "time"=>$array_of_time,
   "location"=> $location,
   
   "doctor_code"=>$doctor_code,
   "slotstatus"=>$slotstatus,
  "bookcount"=>$count,
  "avlcount"=>$count1,
   
   ];
 $bookcount=$bookcount + $count;
$avlcount=$avlcount + $count1;
   array_push($response1,$temp);
	}else{
		$pendingslot='NO';
	if($slotstatus=='booked'){
		
   $temp=[
   "time"=>$array_of_time,
   "location"=> $location,
   "doctor_code"=>$doctor_code,
   "slotstatus"=>$slotstatus,
   "bookcount"=>'1',
   "avlcount"=>'0',
   ];
   $bookcount=$bookcount + 1;
   $avlcount=$avlcount + 0;
   array_push($response1,$temp);
   }
	}
   }
 
}

 $start_time += $fifteen_mins;
}
 

					
}else{
	
	$bookcount='0';
   $avlcount='0';
	
}
/* $temp1=[
   "location"=> $branch,
   "locationname"=> $branchname,
   "doctorcode"=>$doctorcode,
   "doctorname"=>$doctorname,
   "bookcount"=>strval($bookcount),
   "avlcount"=>strval($avlcount),
   "totalcount"=>strval($avlcount + $bookcount),
   ]; */
   $bookcounttotal=$bookcount + $bookcounttotal;
   $avlcounttotal=$avlcount + $avlcounttotal;
   
	$bookcount=0;
   $avlcount=0;
   
	
 /*   array_push($response2,$temp1);	 */
}else{
	
	
	
}
}
/* $temp2=[
   "location"=> $branch,
   "locationname"=> $branchname,
   "doctorcode"=>$doctorcode,
   "doctorname"=>$doctorname,
   "bookcount"=>strval($bookcounttotal),
   "avlcount"=>strval($avlcounttotal),
   "totalcount"=>strval($avlcounttotal + $bookcounttotal),
   ]; */
    $bookcounttotalcount=$bookcounttotal + $bookcounttotalcount;
   $avlcounttotalcount=$avlcounttotal + $avlcounttotalcount;
  	$bookcounttotal=0;
   $avlcounttotal=0;
}

$temp3=[
   "location_cd"=> $branch,
   "location_name"=> $branchname,
   "total_doctors"=> '',
   "booked"=>strval($bookcounttotalcount),
   "available"=>strval( $avlcounttotalcount),
   "total_slots"=>strval( $avlcounttotalcount + $bookcounttotalcount),
   ];
   array_push($response4,$temp3);	
   
   $bookcounttotalcount=0;
   $avlcounttotalcount=0;
   
}



http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	$response['drname']="Doctor Name";
	$response['totalslots']="Total Slots";
	$response['bookedslots']="Booked Slots";
	$response['avlslots']="Available Slots";
//$response['doctorsdashbordcounts'] = $response4;
$response['doctorsdashbordcounts'] = $response4;
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