<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
date_default_timezone_set('Asia/Kolkata');
include "pdo-db.php";
include "whatsapp.php";
$data = json_decode(file_get_contents("php://input"));
$vip = (int) $data->vip;
$doctorid =($data->doctorid);
$branch = trim($data->branch);
$transid = trim($data->transid);
$accesskey = $data->accesskey;
$selectdate = date_format(date_create($data->selectdate),"Y-m-d");
$slottime = date_format(date_create($data->slottime),"H:i:00");
$umrno = strtoupper($data->umrno);
$patientname = strtoupper($data->patientname);
$patientage = date_format(date_create($data->patientage),"Y-m-d");
$gender = strtoupper($data->gender);
$mobile = (int)($data->mobile);
$alternative = (int)($data->alternative);
$emailid = trim(strtolower($data->emailid));
$amount = (int)($data->amount);
$visit_type =trim($data->visit_type);
$from_area = trim($data->from_area);
$referred_by =trim($data->referred_by);
$apt_type = trim($data->apt_type);
$billno =trim($data->billno);
$invoice_no =trim($data->invoice_no);
$visit_count =trim($data->visit_count);
$reqno =trim($data->reqno);
$umr =trim($data->umr);
//$patient_lead_source =trim($data->patient_lead_source);
$remarks = str_ireplace("'","",($data->remarks));
$visitreason = str_ireplace("'","",($data->visitreason));
$response = array();
$ipaddress = $_SERVER['REMOTE_ADDR'];
try{

if(empty($umr)){
	$umr='OTHERS';
	// $patient_lead_source='OTHERS';
}else{
	$umr=$data->umr;
	//$patient_lead_source=$data->patient_lead_source;
}
if(!empty($slottime) && !empty($doctorid) && !empty($selectdate) && !empty($accesskey) && !empty($patientname) && !empty($mobile) && !empty($gender) && !empty($transid) && !empty($reqno)&& !empty($invoice_no)&& !empty($umr) && !empty($branch) ){
$validate = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();
$result = $validate->fetch(PDO::FETCH_ASSOC);
if($validate->rowCount()> 0){
	//Doctor Details
$doctor = $pdoread -> prepare("SELECT CONCAT(`title`,' ',`doctor_name`) AS fullname,`department` FROM `doctor_master` WHERE `doctor_code` = :doctorid");
$doctor->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
$doctor -> execute();
$doctorres = $doctor->fetch(PDO::FETCH_ASSOC);
//check invoice added previously or not
$check_invoice=$pdoread->prepare("SELECT `requisition_no` FROM `patient_details` WHERE `requisition_no`=:invoice_no AND `slot_status`='booked'  AND `requisition_no` NOT IN ('OTHERS')  ");
// $check_invoice->bindParam(':transid', $transid, PDO::PARAM_STR);
$check_invoice->bindParam(':invoice_no', $reqno, PDO::PARAM_STR);
$check_invoice->execute();
if($check_invoice->rowCount() > 0){
	$response['error']=true;
	$response['message']='Slot Previously Booked For This Patient';
}else{
	
    $current_slot=date("H:i:s");
    $current_date=date("Y-m-d");
if(($selectdate > $current_date) || ($selectdate == $current_date && $slottime > $current_slot)){
    $check2 = $pdo4->prepare("UPDATE `patient_details` SET `slot_status` = 'booked',`patient_name` = :patientname,`patient_age` = :patientage,`gender` = :gender,`patient_number` = :mobile,`patient_alt_number` = :alternative,`createdby` = :userid,`created_time` = CURRENT_TIMESTAMP,`amount` = :amount,`patient_mail` = :emailid,`remarks` = :remarks,`invoice_no` = :umrno,`vip_status` = :vip,`reason_for_visit` = :visitreason,`visit_type` = :visit_type,`from_area` = :from_area,`referred_by` = :referred_by,`walkin_type` = :apt_type ,`bill_no`=:billno  
	,`requisition_no`=:reqno,`invoice_no`=:invoice_no , `umrno`=:umr , `visit_count`=:visit_count ,`patient_lead_source`=:patient_lead_source ,`modifiedon` =CURRENT_TIMESTAMP
	WHERE (`transid` = :transid AND `slot_status` ='Hold' AND `location`=:branch)OR (`transid` = :transid AND `slot_status` ='booked' AND `location`=:branch)");
				  $check2->bindParam(':transid', $transid, PDO::PARAM_STR);
				  $check2->bindParam(':patientname', $patientname, PDO::PARAM_STR);
				  $check2->bindParam(':patientage', $patientage, PDO::PARAM_STR);
				  $check2->bindParam(':gender', $gender, PDO::PARAM_STR);
				  $check2->bindParam(':mobile', $mobile, PDO::PARAM_STR);
				  $check2->bindParam(':alternative', $alternative, PDO::PARAM_STR);
				  $check2->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
				  $check2->bindParam(':amount', $amount, PDO::PARAM_STR);
				  $check2->bindParam(':emailid', $emailid, PDO::PARAM_STR);
				  $check2->bindParam(':remarks', $remarks, PDO::PARAM_STR);
				  $check2->bindParam(':umrno', $umrno, PDO::PARAM_STR);
				  $check2->bindParam(':vip', $vip, PDO::PARAM_STR);
				  $check2->bindParam(':visit_type', $visit_type, PDO::PARAM_STR);
				  $check2->bindParam(':apt_type', $apt_type, PDO::PARAM_STR);
				  $check2->bindParam(':from_area', $from_area, PDO::PARAM_STR);
				  $check2->bindParam(':referred_by', $referred_by, PDO::PARAM_STR);
				  $check2->bindParam(':visitreason', $visitreason, PDO::PARAM_STR);
				  $check2->bindParam(':billno', $billno, PDO::PARAM_STR);
				  $check2->bindParam(':reqno', $reqno, PDO::PARAM_STR);
				  $check2->bindParam(':umr', $umr, PDO::PARAM_STR);
				  $check2->bindParam(':invoice_no', $invoice_no, PDO::PARAM_STR);
				  $check2->bindParam(':visit_count', $visit_count, PDO::PARAM_STR);
				  $check2->bindParam(':branch', $branch, PDO::PARAM_STR);
				  $check2->bindParam(':patient_lead_source', $patient_lead_source, PDO::PARAM_STR);
				  $patient_lead_source='Doctor App';
				  $check2->execute();
				  //check2 executed//
    if($check2->rowcount()> 0){
		  http_response_code(200);
          $response['error'] = false;
		$response['message']="Your appointment is booked";
		$response['current_slot']=$current_slot;
		$response['input_slot']=$slottime;
	//$trans = $pdo -> prepare("SELECT IFNULL(MAX(`transid`),CONCAT('CTR',DATE_FORMAT(CURRENT_DATE,'%y%m%d'),'0000')) AS billno FROM `crm_patient_track` WHERE DATE_FORMAT(`createdon`,'%y%b') = DATE_FORMAT(CURRENT_TIMESTAMP,'%y%b')");
//$trans -> execute();
//$transidres = $trans->fetch(PDO::FETCH_ASSOC);
//$transid =  $transidres['billno'];
// //$transid = ++$transid;
$doctorname = ucwords($doctorres['fullname']);
$slotdate = date_format(date_create($selectdate),'d-M-Y');
$slottime = date_format(date_create($slottime),'h:i A');
$bodytext = "Sir/Madam,$doctorname,$slotdate,$slottime,$transid,Hospitals,Hyderabad";
$buttontext = "";
$mobileno = $mobile;
$templateid = "patient_appointment_booking";
whatsapp($bodytext,$buttontext,$mobileno,$templateid);
$message = 'Appointment booked for '.$doctorres['fullname'].' consultation '.date_format(date_create($selectdate),'d-M-Y').' '.date_format(date_create($slottime),'h:i:00 A').' at '.$result['cost_center'].' Branch';
		$insert = $pdo4->prepare("INSERT INTO `crm_patient_track` (`sno`, `transid`, `tstatus`, `category`, `patient_name`, `mobile_number`, `message`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `ip_address`, `dstatus`,`doctor_code`,`branch`) VALUES (NULL, :transid, 'Booked', 'Appointment', :patientname, :mobile, :message,:userid, CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP, :ipaddress, 'Active',:doctorid,:branch)");
				  $insert->bindParam(':transid', $transid, PDO::PARAM_STR);
				  $insert->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
				  $insert->bindParam(':patientname', $patientname, PDO::PARAM_STR);
				  $insert->bindParam(':message', $message , PDO::PARAM_STR);
				  $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
				  $insert->bindParam(':branch', $branch, PDO::PARAM_STR);
				  $insert->bindParam(':ipaddress', $ipaddress, PDO::PARAM_STR);
				  $insert->bindParam(':mobile', $mobile, PDO::PARAM_STR);
				  $insert->execute();
		
    }else{
		                    http_response_code(503);
                            $response['error'] = true;
							$response['message']="Please select proper slot";
							$response['transid']=$transid;
		}
	}else{
		http_response_code(503);
		$response['error']=true;
		$response['message']="Please Select Proper Slot time";
		$response['current_slot']=$current_slot;
		$response['input_slot']=$slottime;
		$response['current_date']=$current_date;
		$response['select_date']=$selectdate;
	}
	}
        
   	}else{
		                http_response_code(400);
					    $response['error'] = true;
						$response['message']="Access Denied!";
					}  
}else{
	        http_response_code(400);
			$response['error'] = true;
			$response['message'] ="Sorry! Some details are missing";
				}
}catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
	?>