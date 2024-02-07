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
$doctorid = $data->doctorid;
$accesskey = $data->accesskey;
$branch = $data->branch;
$slottime1 = $data->slottime;
$selectdate = date_format(date_create($data->selectdate),"Y-m-d");
//$slottime = date_format(date_create($data->slottime),"H:i:00");
$response = array();
try{

if(!empty($slottime1) && !empty($doctorid) && !empty($selectdate) && !empty($accesskey) && !empty($branch)){
	if($slottime1=="Waiting List")
	{
			$slottime = $slottime1;
$validate = $pdoread -> prepare("SELECT `userid`,`cost_center` AS branch FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();
$result = $validate->fetch(PDO::FETCH_ASSOC);
	}else{
			$slottime = date_format(date_create($data->slottime),"H:i:00");
$validate = $pdoread -> prepare("SELECT `userid`,`cost_center` AS branch FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();
$result = $validate->fetch(PDO::FETCH_ASSOC);
		}

if($validate->rowCount()> 0){
    $check2 = $pdoread->prepare("SELECT * FROM `patient_details` WHERE (`date`= :selectdate AND `slot`= :slottime AND `location` = :branch AND `doctor_code` = :doctorid AND `slot_status` = 'booked') OR (`date`= :selectdate AND `slot`= :slottime AND `location` = :branch AND `doctor_code` = :doctorid AND `slot_status` = 'Hold')");
	              $check2->bindParam(':selectdate', $selectdate, PDO::PARAM_STR); 
		          $check2->bindParam(':slottime', $slottime, PDO::PARAM_STR); 
				  $check2->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
                  $check2->bindParam(':branch', $branch, PDO::PARAM_STR); 
				  $check2->execute();
				  //check2 executed//
    if($check2->rowcount()> 0){
		  http_response_code(503);
          $response['error'] = true;
		$response['message']="Sorry! This slot has been reserved";
    }else{
		
    $current_slot=date("H:i:s");
    $current_date=date("Y-m-d");
if(($selectdate > $current_date) || ($selectdate == $current_date && $slottime > $current_slot)){
		$apt = $pdoread -> prepare("SELECT IFNULL(MAX(`transid`),CONCAT('APT',DATE_FORMAT(CURRENT_DATE,'%y%m'),'00000')) AS transid FROM `patient_details` WHERE DATE_FORMAT(`created_time`,'%y%b') = DATE_FORMAT(CURRENT_TIMESTAMP,'%y%b')");
$apt -> execute();
$aptres = $apt->fetch(PDO::FETCH_ASSOC);
$transid =  $aptres['transid'];
$transid = ++$transid;
    $insert=$pdo4->prepare("INSERT INTO `patient_details`(`location`, `doctor_code`, `date`, `slot`, `slot_status`, `patient_name`, `patient_age`, `gender`, `patient_number`, `patient_mail`, `patient_alt_number`, `patient_lead_source`, `created_time`,`consultation_type`,`transid`) VALUES (:branch,:doctorid,:selectdate,:slottime,'Hold','No Update', '0','No Update','0','No Update','0','Call Center',CURRENT_TIMESTAMP,'Physical',:transid)");	
	              $insert->bindParam(':slottime', $slottime, PDO::PARAM_STR); 
	              $insert->bindParam(':transid', $transid, PDO::PARAM_STR); 
				  $insert->bindParam(':doctorid', $doctorid, PDO::PARAM_STR);
                  $insert->bindParam(':branch', $branch, PDO::PARAM_STR); 
				  $insert->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
				 
                  $insert->execute();
				     
if($insert->rowcount()>0){
	                     http_response_code(200);
					   $response['error'] = false;
						$response['message']="Slot is on hold 15 mins";
						$response['transid']=$transid;
				}else{
					http_response_code(503);
					 $response['error'] = true;
						$response['message']="Sorry! Try again";
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
							$response['message']="Access denied!";
					}  
}else{
	             http_response_code(400);
				$response['error'] = true;
				$response['message'] ="Sorry! Some details are missing";
					}
} 
catch(PDOException $e)
{
	 http_response_code(503);
	  $response['error'] = true;
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
	?>