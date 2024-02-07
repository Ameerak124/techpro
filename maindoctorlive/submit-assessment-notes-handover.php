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
$accesskey=trim($data->accesskey);
$findings = $data->findings;
$clinical_examination = $data->clinical_examination;
$plan_care = $data->plan_care;
$medication_modification = $data->medication_modifications;
$diet = $data->diet;
$procedure_details = $data->procedure_details;
$others = $data->others;
$allergies_medication_type = $data->allergies_medication_type;
$allergies_medication = $data->allergies_medication;
$food_type = $data->food_type;
$food = $data->food;
$food_others = $data->food_others;
$progress_notes = $data->progress_notes;
$vitals = $data->vitals;
$assessment_diet = $data->assessment_diet;
$labs = $data->labs;
$critical_values = $data->critical_values;
$pending_orders = $data->pending_orders;
$assessment_clinical_examination = $data->assessment_clinical_examination;
$assessment_others = $data->assessment_others;
$recommendation_plan_care = $data->recommendation_plan_care;
$handover_constructions = $data->handover_constructions;
$recommendations_others = $data->recommendations_others;
$handover_by = $data->handover_by;
$handover_to = $data->handover_to;
$doc_uid = trim($data->doc_uid);
$umrno = trim($data->umrno);
$ipno = trim($data->ipno);
try {
	if(!empty($accesskey)&& !empty($umrno)&& !empty($ipno)&& !empty($doc_uid)&& !empty($handover_to)&& !empty($handover_by)&& !empty($recommendations_others)&& !empty($handover_constructions)&& !empty($recommendation_plan_care)&& !empty($assessment_others)&& !empty($assessment_clinical_examination)&& !empty($pending_orders)&& !empty($critical_values)&& !empty($labs)&& !empty($assessment_diet)&& !empty($vitals)&& !empty($progress_notes)&& !empty($food_others)&& !empty($food)&& !empty($food_type)&& !empty($allergies_medication)&& !empty($allergies_medication_type)&& !empty($others)&& !empty($procedure_details)&& !empty($diet)&& !empty($plan_care)&& !empty($clinical_examination)&& !empty($findings)){
		$check = $pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
		$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
		$check->execute();
		$result = $check->fetch(PDO::FETCH_ASSOC);
		if ($check->rowCount() > 0) {

			 //check if patient discharged or not
			 $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
			 $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
			 $validate -> execute();
			 $validates = $validate->fetch(PDO::FETCH_ASSOC);
			 if($validate -> rowCount() > 0){
			//check if details exist 
			$check_details = $pdoread->prepare("SELECT  `umrno` FROM  `doctor_assessment_notes_handover` WHERE `status`='Active' AND `cost_center`=:branch AND `ipno`=:ipno AND `umrno`=:umrno AND `doc_uid`=:doc_uid ");
			$check_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
			$check_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
			$check_details->bindParam(':umrno', $umrno, PDO::PARAM_STR);
			$check_details->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
			$check_details->execute();

			if ($check_details->rowCount() > 0) {

				//if data exists update the data on umr
				$update_details = $pdo4->prepare("UPDATE `doctor_assessment_notes_handover` SET `ipno`=:ipno,`umrno`=:umrno,`doc_uid`=:doc_uid,`findings`=:findings,`clinical_examination`=:clinical_examination,`plan_care`=:plan_care,`medication`=:medication_modification, `diet`=:diet,`procedure_details`=:procedure_details,`others`=:others,`allergies_medication_type`=:allergies_medication_type,`allergies_medication`=:allergies_medication,`food_type`=:food_type,`food`=:food,`food_others`=:food_others,`progress_notes`=:progress_notes,`vitals`=:vitals,`assessment_diet`=:assessment_diet,`labs`=:labs,`critical_values`=:critical_values,`pending_orders`=:pending_orders,`assessment_clinical_examination`=:assessment_clinical_examination,`assessment_others`=:assessment_others,`recommendation_plan_care`=:recommendation_plan_care,`handover_constructions`=:handover_constructions,`recommendations_others`=:recommendations_others,`handover_by`=:handover_by,`handover_to`=:handover_to,`modified_on`=CURRENT_TIMESTAMP,`modified_by`=:userid WHERE `ipno`=:ipno AND `umrno`=:umrno AND `doc_uid`=:doc_uid AND `cost_center`=:branch AND `status`='Active'");
				$update_details->bindParam(':ipno', $ipno, PDO::PARAM_STR);
				$update_details->bindParam(':umrno', $umrno, PDO::PARAM_STR);
				$update_details->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
				$update_details->bindParam(':findings', $findings, PDO::PARAM_STR);
				$update_details->bindParam(':clinical_examination', $clinical_examination, PDO::PARAM_STR);
				$update_details->bindParam(':plan_care', $plan_care, PDO::PARAM_STR);
				$update_details->bindParam(':medication_modification', $medication_modification, PDO::PARAM_STR);
				$update_details->bindParam(':diet', $diet, PDO::PARAM_STR);
				$update_details->bindParam(':procedure_details', $procedure_details, PDO::PARAM_STR);
				$update_details->bindParam(':others', $others, PDO::PARAM_STR);
				$update_details->bindParam(':allergies_medication_type', $allergies_medication_type, PDO::PARAM_STR);
				$update_details->bindParam(':allergies_medication', $allergies_medication, PDO::PARAM_STR);
				$update_details->bindParam(':food_type', $food_type, PDO::PARAM_STR);
				$update_details->bindParam(':food', $food, PDO::PARAM_STR);
				$update_details->bindParam(':food_others', $food_others, PDO::PARAM_STR);
				$update_details->bindParam(':progress_notes', $progress_notes, PDO::PARAM_STR);
				$update_details->bindParam(':vitals', $vitals, PDO::PARAM_STR);
				$update_details->bindParam(':assessment_diet', $assessment_diet, PDO::PARAM_STR);
				$update_details->bindParam(':labs', $labs, PDO::PARAM_STR);
				$update_details->bindParam(':critical_values', $critical_values, PDO::PARAM_STR);
				$update_details->bindParam(':pending_orders', $pending_orders, PDO::PARAM_STR);
				$update_details->bindParam(':assessment_clinical_examination', $assessment_clinical_examination, PDO::PARAM_STR);
				$update_details->bindParam(':assessment_others', $assessment_others, PDO::PARAM_STR);
				$update_details->bindParam(':recommendation_plan_care', $recommendation_plan_care, PDO::PARAM_STR);
				$update_details->bindParam(':handover_constructions', $handover_constructions, PDO::PARAM_STR);
				$update_details->bindParam(':recommendations_others', $recommendations_others, PDO::PARAM_STR);
				$update_details->bindParam(':handover_by', $handover_by, PDO::PARAM_STR);
				$update_details->bindParam(':handover_to', $handover_to, PDO::PARAM_STR);
				$update_details->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
				$update_details->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
				$update_details->execute();
				if ($update_details->rowCount() > 0) {
					$response['error'] = false;
					$response['message'] = "Data Updated Sucessfully";
				} else {
					$response['error'] = true;
					$response['message'] = "Please Try Again";
				}
				//if data is not there go on inserting
			} else {

				//insertion of data start
				$adddata = $pdo4->prepare("INSERT IGNORE INTO `doctor_assessment_notes_handover`(`sno`, `ipno`, `umrno`, `doc_uid`, `findings`, `clinical_examination`, `plan_care`, `medication`,`diet`, `procedure_details`, `others`, `allergies_medication_type`, `allergies_medication`, `food_type`, `food`, `food_others`, `progress_notes`, `vitals`, `assessment_diet`, `labs`, `critical_values`, `pending_orders`, `assessment_clinical_examination`, `assessment_others`, `recommendation_plan_care`, `handover_constructions`, `recommendations_others`, `handover_by`, `handover_to`, `created_on`, `created_by`, `modified_on`, `modified_by`, `status`, `cost_center`) VALUES (NULL,:ipno,:umrno,:doc_uid,:findings,:clinical_examination,:plan_care,:medication,:diet,:procedure_details,:others,:allergies_medication_type,:allergies_medication,:food_type,:food,:food_others,:progress_notes,:vitals,:assessment_diet,:labs,:critical_values,:pending_orders,:assessment_clinical_examination,:assessment_others,:recommendation_plan_care,:handover_constructions,:recommendations_others,:handover_by,:handover_to,CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,'Active',:branch)");
				$adddata->bindParam(':ipno', $ipno, PDO::PARAM_STR);
				$adddata->bindParam(':umrno', $umrno, PDO::PARAM_STR);
				$adddata->bindParam(':doc_uid', $doc_uid, PDO::PARAM_STR);
				$adddata->bindParam(':findings', $findings, PDO::PARAM_STR);
				$adddata->bindParam(':clinical_examination', $clinical_examination, PDO::PARAM_STR);
				$adddata->bindParam(':plan_care', $plan_care, PDO::PARAM_STR);
				$adddata->bindParam(':medication', $medication_modification, PDO::PARAM_STR);
				$adddata->bindParam(':diet', $diet, PDO::PARAM_STR);
				$adddata->bindParam(':procedure_details', $procedure_details, PDO::PARAM_STR);
				$adddata->bindParam(':others', $others, PDO::PARAM_STR);
				$adddata->bindParam(':allergies_medication_type', $allergies_medication_type, PDO::PARAM_STR);
				$adddata->bindParam(':allergies_medication', $allergies_medication, PDO::PARAM_STR);
				$adddata->bindParam(':food_type', $food_type, PDO::PARAM_STR);
				$adddata->bindParam(':food', $food, PDO::PARAM_STR);
				$adddata->bindParam(':food_others', $food_others, PDO::PARAM_STR);
				$adddata->bindParam(':progress_notes', $progress_notes, PDO::PARAM_STR);
				$adddata->bindParam(':vitals', $vitals, PDO::PARAM_STR);
				$adddata->bindParam(':assessment_diet', $assessment_diet, PDO::PARAM_STR);
				$adddata->bindParam(':labs', $labs, PDO::PARAM_STR);
				$adddata->bindParam(':critical_values', $critical_values, PDO::PARAM_STR);
				$adddata->bindParam(':pending_orders', $pending_orders, PDO::PARAM_STR);
				$adddata->bindParam(':assessment_clinical_examination', $assessment_clinical_examination, PDO::PARAM_STR);
				$adddata->bindParam(':assessment_others', $assessment_others, PDO::PARAM_STR);
				$adddata->bindParam(':recommendation_plan_care', $recommendation_plan_care, PDO::PARAM_STR);
				$adddata->bindParam(':handover_constructions', $handover_constructions, PDO::PARAM_STR);
				$adddata->bindParam(':recommendations_others', $recommendations_others, PDO::PARAM_STR);
				$adddata->bindParam(':handover_by', $handover_by, PDO::PARAM_STR);
				$adddata->bindParam(':handover_to', $handover_to, PDO::PARAM_STR);
				$adddata->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
				$adddata->bindParam(':branch', $result['cost_center'], PDO::PARAM_STR);
				$adddata->execute();
				if ($adddata->rowCount() > 0) {
					$response['error'] = false;
					$response['message'] = "Data Inserted Sucessfully";
				} else {
					$response['error'] = true;
					$response['message'] = "Please Try Again";
				}
			}
		}else{
			$response['error'] = true;
			  $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
		}
		} else {
			$response['error'] = true;
			$response['message'] = "Access denied";
		}
	} else {
		$response['error'] = true;
		$response['message'] = "Sorry! some details are missing ";
	}
} catch (PDOException $e) {
	http_response_code(503);
	$response['error'] = true;

}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>