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
//include "whatsapp.php";
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ip = trim($data->ip);
$doc_id = trim($data->doc_id);
$consult = trim($data->consult);
$consult_date = date('Y-m-d', strtotime($data->consult_date));
$consult_time = trim($data->consult_time);
$doctor_name = trim($data->doctor_name);
$remarks = trim($data->remarks);
$appointment_priority = trim($data->appointment_priority);
$patient_clinical_problems = trim($data->patient_clinical_problems);
$reason_for_refferal = trim($data->reason_for_refferal);
$reffered_for = trim($data->reffered_for);

try {
 
  if (!empty($accesskey) && !empty($ip) && !empty($consult)) {
    //Check User Access Start
    $check = $pdoread->prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
    $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
    $check->execute();
    $result = $check->fetch(PDO::FETCH_ASSOC);
    if ($check->rowCount() > 0) {

      $generatebillno = $pdoread->prepare("SELECT IFNULL(MAX(`billno`),CONCAT('MOFB',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000')) AS billno  FROM `registration` WHERE DATE_FORMAT(`admittedon`,'%y%b') = DATE_FORMAT(CURRENT_TIMESTAMP,'%y%b')");
      $generatebillno->execute();
      if ($generatebillno->rowCount() > 0) {
        $resbillno = $generatebillno->fetch(PDO::FETCH_ASSOC);
        $billno =  $resbillno['billno'];
        $billnum = ++$billno;
      } else {
        $billnum = $billno;
      }
      $receipt = $pdoread->prepare("SELECT IFNULL(MAX(`receiptno`),CONCAT('MCR',DATE_FORMAT(CURRENT_DATE,'%y%m'),'0000000')) AS receiptno FROM `payment_history`");
      $receipt->execute();
      if ($receipt->rowCount() > 0) {
        $receiptres = $receipt->fetch(PDO::FETCH_ASSOC);
        $receiptno =  $receiptres['receiptno'];
        $receiptno = ++$receiptno;
      } else {
		  http_response_code(503);
        $response['error'] = true;
        $response['message'] = "receiptno not found";
      }
      $validate = $pdoread->prepare("SELECT E.consultantname,E.roomno,E.patientname,E.admissionno,E.umrno,E.organization_code,E.map_ward,`sponsor_master`.`tariff_code`,`sponsor_master`.`secondary_tariff`,`sponsor_master`.`default_tariff` FROM (SELECT `admissionno`,`umrno`,`organization_code`,`map_ward`,`patientname`,CONCAT(`registration`.`admittedward`,' / ',`registration`.`roomno`) AS roomno,`registration`.`consultantname` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible') AS E LEFT JOIN `sponsor_master` ON `sponsor_master`.`organization_code` = E.organization_code LIMIT 1");
      $validate->bindParam(':ip', $ip, PDO::PARAM_STR);
      $validate->execute();
      $validates = $validate->fetch(PDO::FETCH_ASSOC);
      if ($validate->rowCount() > 0) {
		  http_response_code(200);
        $response['error'] = false;
        $response['message'] = "Data Found";
        if ($consult == 'Consultant Visited') {
          if (!empty($consult_date) && !empty($consult_time) && !empty($doctor_name)) {

            //   $rates = $con -> prepare("SELECT charge from `consultation_master` left join `registration` on `consultation_master`.`ward_name` = `registration`.`map_ward` where  `registration`.`admissionno`  =:admissionnum AND `registration`.`status`= 'Visible' AND `admissionstatus` != 'Discharged' AND `consultation_master`.`status` != 'Inactive' LIMIT 1");
            $rates = $pdoread->prepare("SELECT E.charge FROM ((SELECT `charge` FROM `consultation_master` WHERE `doc_code` LIKE :doc_id AND `tariff_code` = :primaryy AND `ward_name` = :map_ward LIMIT 1) UNION ALL (SELECT `charge` FROM `consultation_master` WHERE `doc_code` LIKE :doc_id AND `tariff_code` = :secondaryy AND `ward_name` = :map_ward LIMIT 1) UNION ALL (SELECT `charge` FROM `consultation_master` WHERE `doc_code` LIKE :doc_id AND `tariff_code` = :defaultt AND `ward_name` = :map_ward LIMIT 1)) AS E LIMIT 1");
            $rates->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
            $rates->bindParam(':primaryy', $validates['tariff_code'], PDO::PARAM_STR);
            $rates->bindParam(':secondaryy', $validates['secondary_tariff'], PDO::PARAM_STR);
            $rates->bindParam(':defaultt', $validates['default_tariff'], PDO::PARAM_STR);
            $rates->bindParam(':map_ward', $validates['map_ward'], PDO::PARAM_STR);
            $rates->execute();
            if ($rates->rowCount() > 0) {
              $ratess = $rates->fetch(PDO::FETCH_ASSOC);
              $insert = $pdo4->prepare("INSERT IGNORE INTO `nursing_doctor_visit`(`sno`, `ip`, `sugg_by`,`ward`,`consult`, `consult_date`, `consult_time`, `doctor_name`, `doc_id`, `remarks`, `appointment_priority`, `patient_clinical_problems`, `reason_for_refferal`, `reffered_for`, `createdby`, `createdon`, `modifiedon`, `modifiedby`, `estatus`) VALUES(NULL, :ip, :userid,'ward',:consult, :consult_date, :consult_time, :doctor_name, :doc_id, :remarks, '', '', '', '', :userid,  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, :userid, 'Approved')");
              $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
              $insert->bindParam(':consult', $consult, PDO::PARAM_STR);
              $insert->bindParam(':consult_date', $consult_date, PDO::PARAM_STR);
              $insert->bindParam(':consult_time', $consult_time, PDO::PARAM_STR);
              $insert->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
              $insert->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
              $insert->bindParam(':remarks', $remarks, PDO::PARAM_STR);
              $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
              $insert->execute();
              if ($insert->rowCount() > 0) {
				  
                $billhis = $pdo4->prepare("INSERT IGNORE INTO `billing_history`(`sno`, `patient_type`, `ipno`, `tariff_category`, `tariff_code`, `charges_type`, `umr_no`, `billno`, `requisition_no`, `category`, `subcategory`, `servicecode`, `services`, `hsn_sac`, `quantity`, `rate`, `total`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `remarks`, `ipaddress`, `acknowledge_date`, `acknowledge_by`, `service_status`, `status`,`billinghead`,`ward_type`,`afterrate`,`aftertotal`,`discounttype`)  (select NULL,'No Update',:admissionnum, '', '', '', '', :billnum, :receiptno, 'CONSULTATION', `doctor_master`.`department`, `doctor_master`.`doctor_uid`, `doctor_master`.`doctor_name`,	'', '1', :charge, :charge, 'CREDIT', :userid,CURRENT_TIMESTAMP,  :userid, CURRENT_TIMESTAMP, '', '', '', '', '', 'Visible','Consultation Charges',:map_ward,:charge,:charge,'VALUE' from `nursing_doctor_visit` left join `doctor_master` on `doctor_master`.`doctor_uid` = `nursing_doctor_visit`.`doc_id` where  `nursing_doctor_visit`.`ip` = :admissionnum AND `nursing_doctor_visit`.`doc_id` = :doc_id AND `nursing_doctor_visit`.`sno` =(select max(`sno`) from `nursing_doctor_visit` where `nursing_doctor_visit`.`ip` = :admissionnum))");
                $billhis->bindParam(':admissionnum', $ip, PDO::PARAM_STR);
                $billhis->bindParam(':consult', $consult, PDO::PARAM_STR);
                $billhis->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
                $billhis->bindParam(':billnum', $billnum, PDO::PARAM_STR);
                $billhis->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
                $billhis->bindParam(':charge', $ratess['charge'], PDO::PARAM_STR);
                $billhis->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
                $billhis->bindParam(':map_ward', $validates['map_ward'], PDO::PARAM_STR);
                $billhis->execute();
                if ($billhis->rowCount() > 0) {
					http_response_code(200);
                  $response['error'] = false;
                  $response['message'] = "Data Saved";
                } else {
					http_response_code(503);
                  $response['error'] = true;
                  $response['message'] = "In Billing History Not Inserted";
                }
              } else {
				  http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Data Not Inserted";
              }
            } else {
				http_response_code(503);
              $response['error'] = true;
              $response['message'] = "Rates Not Found";
            }
          } else {
			  http_response_code(503);
            $response['error'] = true;
            $response['message'] = "Sorry! Date/Time/DoctorName Details Missing";
          }
        } else {
          if (!empty($appointment_priority) && !empty($patient_clinical_problems) && !empty($reason_for_refferal) && !empty($reffered_for)) {
            $insert = $pdo4->prepare("INSERT IGNORE INTO `nursing_doctor_visit`(`sno`, `ip`,`sugg_by`, `ward`,`consult`, `consult_date`, `consult_time`, `doctor_name`, `doc_id`, `remarks`, `appointment_priority`, `patient_clinical_problems`, `reason_for_refferal`, `reffered_for`, `createdby`, `createdon`, `modifiedon`, `modifiedby`, `estatus`) VALUES(NULL, :ip, :userid,'ward', :consult, '', '', :doctor_name, :doc_id, '', :appointment_priority, :patient_clinical_problems, :reason_for_refferal, :reffered_for, :userid,  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,:userid,  'Approved')");
            $insert->bindParam(':ip', $ip, PDO::PARAM_STR);
            $insert->bindParam(':consult', $consult, PDO::PARAM_STR);
            $insert->bindParam(':appointment_priority', $appointment_priority, PDO::PARAM_STR);
            $insert->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
            $insert->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
            $insert->bindParam(':patient_clinical_problems', $patient_clinical_problems, PDO::PARAM_STR);
            $insert->bindParam(':reason_for_refferal', $reason_for_refferal, PDO::PARAM_STR);
            $insert->bindParam(':reffered_for', $reffered_for, PDO::PARAM_STR);
            $insert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $insert->execute();
            if ($insert->rowCount() > 0) {
              // $rates = $con -> prepare("SELECT charge from `consultation_master` left join `registration` on `consultation_master`.`ward_name` = `registration`.`map_ward` where  `registration`.`admissionno`  =:admissionnum AND `registration`.`status`= 'visible' AND `admissionstatus` != 'Discharged' AND `consultation_master`.`status` != 'Inactive'");
              $rates = $pdoread->prepare("SELECT E.charge FROM ((SELECT `charge` FROM `consultation_master` WHERE `doc_code` LIKE :doc_id AND `tariff_code` = :primaryy AND `ward_name` = :map_ward LIMIT 1) UNION ALL (SELECT `charge` FROM `consultation_master` WHERE `doc_code` LIKE :doc_id AND `tariff_code` = :secondaryy AND `ward_name` = :map_ward LIMIT 1) UNION ALL (SELECT `charge` FROM `consultation_master` WHERE `doc_code` LIKE :doc_id AND `tariff_code` = :defaultt AND `ward_name` = :map_ward LIMIT 1)) AS E LIMIT 1");
              $rates->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
              $rates->bindParam(':primaryy', $validates['tariff_code'], PDO::PARAM_STR);
              $rates->bindParam(':secondaryy', $validates['secondary_tariff'], PDO::PARAM_STR);
              $rates->bindParam(':defaultt', $validates['default_tariff'], PDO::PARAM_STR);
              $rates->bindParam(':map_ward', $validates['map_ward'], PDO::PARAM_STR);
              $rates->execute();
              if ($rates->rowCount() > 0) {
                $ratess = $rates->fetch(PDO::FETCH_ASSOC);
				
                $billhis = $pdo4->prepare("INSERT IGNORE INTO `billing_history`(`sno`, `patient_type`, `ipno`, `tariff_category`, `tariff_code`, `charges_type`, `umr_no`, `billno`, `requisition_no`, `category`, `subcategory`, `servicecode`, `services`, `hsn_sac`, `quantity`, `rate`, `total`, `credit_debit`, `createdby`, `createdon`, `modifiedby`, `modifiedon`, `remarks`, `ipaddress`, `acknowledge_date`, `acknowledge_by`, `service_status`, `status`,`billinghead`,`ward_type`,`afterrate`,`aftertotal`,`discounttype`)  (select NULL,'No Update',:admissionnum, '', '', '', '', :billnum, :receiptno, 'CONSULTATION', `doctor_master`.`department`, `doctor_master`.`doctor_uid`, `doctor_master`.`doctor_name`,	'', '1', :charge, :charge, 'CREDIT', :userid,CURRENT_TIMESTAMP,  :userid, CURRENT_TIMESTAMP, '', '', '', '', '', 'Visible','Consultation Charges',:map_ward,:charge,:charge,'VALUE' from `nursing_doctor_visit` inner join `doctor_master` on `doctor_master`.`doctor_uid` = `nursing_doctor_visit`.`doc_id` where  `nursing_doctor_visit`.`ip` = :admissionnum AND `nursing_doctor_visit`.`doc_id` = :doc_id AND `nursing_doctor_visit`.`sno` =(select max(`sno`) from `nursing_doctor_visit` where `nursing_doctor_visit`.`ip` = :admissionnum))");
                $billhis->bindParam(':admissionnum', $ip, PDO::PARAM_STR);
                $billhis->bindParam(':consult', $consult, PDO::PARAM_STR);
                $billhis->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
                $billhis->bindParam(':billnum', $billnum, PDO::PARAM_STR);
                $billhis->bindParam(':receiptno', $receiptno, PDO::PARAM_STR);
                $billhis->bindParam(':charge', $ratess['charge'], PDO::PARAM_STR);
                $billhis->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
                $billhis->bindParam(':map_ward', $validates['map_ward'], PDO::PARAM_STR);
                $billhis->execute();
                if ($billhis->rowCount() > 0){
					http_response_code(200);
                  $response['error'] = false;
                  $response['message'] = "Data Saved";
                  $patientname = TRIM($validates['patientname']);
                  $roomno = TRIM($validates['roomno']);
                  $consultantname = TRIM($validates['consultantname']);
                  $mobile = $pdoread->prepare("SELECT IFNULL((SELECT `mobile` FROM `doctor_master` WHERE `doctor_uid` = :doc_id),'NA') AS mobileno,(SELECT `branch_master`.`branch_name` FROM `branch_master` WHERE `cost_center` = :cost_center) AS branch");
                  $mobile->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
                  $mobile->bindParam(':doc_id', $doc_id, PDO::PARAM_STR);
                  $mobile->execute();
                  $mobres = $mobile->fetch(PDO::FETCH_ASSOC);
                  if($mobres['mobileno'] != 'NA'){
                    $mobilemain = $mobres['mobileno'];
                    $branch = $mobres['branch'];
                    /* $bodytext = "*$appointment_priority* ,: *$ip* ,Pt.Name: *$patientname*, *$roomno*, $consultantname,for *$reffered_for* ,Hospitals, $branch";
                    $buttontext = "";
                    $mobileno = "917702919740";
                    $templateid = "cross_consultation";
                   whatsapp($bodytext,$buttontext,$mobilemain,$templateid);
                    whatsapp($bodytext,$buttontext,$mobileno,$templateid); */
                  }

                } else {
					http_response_code(503);
                  $response['error'] = true;
                  $response['message'] = "In Billing History Not Inserted";
                }
              } else {
				  http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Rates not found";
              }
            } else {
				http_response_code(503);
              $response['error'] = true;
              $response['message'] = "Data Not Inserted";
            }
          } else {
			  http_response_code(503);
            $response['error'] = true;
            $response['message'] = "Sorry! appointment_priority/patient_clinical_problems/reason_for_refferal/reffered_for Details Missing";
          }
        }
      } else {
		  http_response_code(503);
        $response['error'] = true;
        $response['message'] = "Please Check IP";
      }

      // insearting values into billing history 




      //Check User Access End
    } else {
		http_response_code(400);
      $response['error'] = true;
      $response['message'] = "Access denied";
    }
  } else {
	  http_response_code(400);
    $response['error'] = true;
    $response['message'] = "Sorry! some details are missing";
  }
  //Check empty Parameters End
} catch (PDOException $e) {
  http_response_code(503);
  $response['error'] = true;
  $response['message'] = "Connection failed" . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>