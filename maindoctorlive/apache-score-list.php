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
$accesskey = trim($data->accesskey);
$ipno = trim($data->ipno);
$umrno = trim($data->umrno);

try {
    if (!empty($accesskey) && !empty($ipno) && !empty($umrno)) {
        
        $check = $pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);

        if ($check->rowCount() > 0) {
            $ninsert = $pdoread->prepare("SELECT  `apache_score`.`sno`,`createdfrom`, `ipno`, `umrno`, `unit_type`, `temp`, `temp_sod_low`, `temp_sod_high`, `sys_glu_low`, `sys_glu_high`, `dia_cre_low`, `dia_cre_high`, `heart_bun_low`, `heart_bun_high`, `resp_low`, `resp_high`, `urine`, `sea`, `sea_albumin`, `fi_bilirubin`, `ph_hct_low`, `ph_hct_high`, `po2`, `wbc_low`, `wbc_high`, `pco2`, `crf_type`, `crf_text`, `cancer_type`, `cancer_text`, `aids_type`, `aids_text`, `myeloma_type`, `myeloma_text`, `hepatic_type`, `hepatic_text`, `immuno_type`, `immuno_text`, `lymphoma_type`, `lymphoma_text`, `cirrhosis_type`, `cirrhosis_text`, `admitteedform`, `icu`, `post_operative`, `surgery`, `readmission`, `ventilated`, `apache`, `aps`, `logit`, `disease`, `mortality`, `los`, DATE_FORMAT(`apache_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `apache_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`apache_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `apache_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
            $ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
            $ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
            $ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $ninsert->execute();

            if ($ninsert->rowCount() > 0) {
                $response['error'] = false;
                $response['message'] = "Data Found";
				$response['createddt']="Created Dt.";
	            $response['createdby']="Created By.";
	            $response['createdfrom']="Created From";
	            $response['unittype']="Unit Type";
	            $response['temp']="Temperature / Sodium";
	            $response['systolic']="Systolic/Glucose";
	            $response['diastolic']="Diastolic /Creatinine";
	            $response['heartrate']="Heart Rate/BUN";
	            $response['respiratory']="Respiratory /Urine Output";
	            $response['altitude']="Altitude above sea level /Albumin";
	            $response['bilirubin']="Fio2 /Bilirubin";
	            $response['phhct']="PH /HCT";
	            $response['wbc']="PO2 - PCO2 /WBC";	
	            $response['hronichealth']="Chronic Health /Condition";
	            $response['admittedform']="Admitted form";
	            $response['pre']="Pre ICU LOS";
	            $response['postoperative']="Post-Operative";
	            $response['emergencysurgery']="Emergency Surgery";
	            $response['readmission']="Readmission";
	            $response['ventilated']="Ventilated";
	            $response['apachescore']="Apache IV Score";
	            $response['aps']="APS Score";
	            $response['logit']="Logit";
	            $response['apachedisease']="APACHE Disease";
	            $response['predicatedmortality']="Predicated Mortality Rate";
	            $response['predicatediculos']="Predicated ICU LOS";
	            $response['action']="Action";
        

                while ($results = $ninsert->fetch(PDO::FETCH_ASSOC)) {
                    $testarr = array();

        $testarr['sno'] = $results['sno'];
        $testarr['createdfrom'] = $results['createdfrom'];
        $testarr['ipno'] = $results['ipno'];
        $testarr['umrno'] = $results['umrno'];
        $testarr['unit_type'] = $results['unit_type'];
        $testarr['temp'] = $results['temp'];
        $testarr['temp_sod_low'] = $results['temp_sod_low'];
        $testarr['temp_sod_high'] = $results['temp_sod_high'];
        $testarr['sys_glu_low'] = $results['sys_glu_low'];
        $testarr['sys_glu_high'] = $results['sys_glu_high'];
        $testarr['dia_cre_low'] = $results['dia_cre_low'];
        $testarr['dia_cre_high'] = $results['dia_cre_high'];
        $testarr['heart_bun_low'] = $results['heart_bun_low'];
        $testarr['heart_bun_high'] = $results['heart_bun_high'];
        $testarr['resp_low'] = $results['resp_low'];
        $testarr['resp_high'] = $results['resp_high'];
        $testarr['urine'] = $results['urine'];
        $testarr['sea'] = $results['sea'];
        $testarr['sea_albumin'] = $results['sea_albumin'];
        $testarr['fi_bilirubin'] = $results['fi_bilirubin'];
        $testarr['ph_hct_low'] = $results['ph_hct_low'];
        $testarr['ph_hct_high'] = $results['ph_hct_high'];
        $testarr['po2'] = $results['po2'];
        $testarr['wbc_low'] = $results['wbc_low'];
        $testarr['wbc_high'] = $results['wbc_high'];
        $testarr['pco2'] = $results['pco2'];
        $testarr['admitteedform'] = $results['admitteedform'];
        $testarr['icu'] = $results['icu'];
        $testarr['post_operative'] = $results['post_operative'];
        $testarr['surgery'] = $results['surgery'];
        $testarr['readmission'] = $results['readmission'];
        $testarr['ventilated'] = $results['ventilated'];
        $testarr['apache'] = $results['apache'];
        $testarr['aps'] = $results['aps'];
        $testarr['logit'] = $results['logit'];
        $testarr['disease'] = $results['disease'];
        $testarr['mortality'] = $results['mortality'];
        $testarr['los'] = $results['los'];
        $testarr['createdon'] = $results['createdon'];
        $testarr['createdby'] = $results['createdby'];
        $testarr['details'] = '';
        if ($results['crf_type'] != "" && $results['crf_text'] != "") {
            $testarr['details'] .= 'CRF / HD (used for APS):<span class="text-primary">' . $results['crf_text'] . "</span></span></br>";
        }

        if ($results['cancer_type'] != "" && $results['cancer_text'] != "") {
            $testarr['details'] .= 'Metastatic Cancer: <span class="text-primary">' . $results['cancer_text'] . "</span></br>";
        }

        if ($results['aids_type'] != "" && $results['aids_text'] != "") {
            $testarr['details'] .= 'AIDS: <span class="text-primary">' . $results['aids_text'] . "</span></br>";
        }

        if ($results['myeloma_type'] != "" && $results['myeloma_text'] != "") {
            $testarr['details'] .= 'Leukemia/Multiple Myeloma: <span class="text-primary">' . $results['myeloma_text'] . "</span></br>";
        }

        if ($results['hepatic_type'] != "" && $results['hepatic_text'] != "") {
            $testarr['details'] .= 'Hepatic Failure: <span class="text-primary">' . $results['hepatic_text'] . "</span></br>";
        }

        if ($results['immuno_type'] != "" && $results['immuno_text'] != "") {
            $testarr['details'] .= 'Immunosuppressant: <span class="text-primary">' . $results['immuno_text'] . "</span></br>";
        }

        if ($results['lymphoma_type'] != "" && $results['lymphoma_text'] != "") {
            $testarr['details'] .= 'Lymphoma: <span class="text-primary">' . $results['lymphoma_text'] . "</span></br>";
        }

        if ($results['cirrhosis_type'] != "" && $results['cirrhosis_text'] != "") {
            $testarr['details'] .= 'Cirrhosis: <span class="text-primary">' . $results['cirrhosis_text']."</span>";
        }

        $response['apachescorelist'][] = $testarr;

        }
      
            } else {
                $response['error'] = true;
                $response['message'] = "Data Not Found";
            }
        } else {
            $response['error'] = true;
            $response['message'] = "Access denied!";
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Sorry! Some details are missing";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed";

}

echo json_encode($response);
$pdoread = null;
?>
