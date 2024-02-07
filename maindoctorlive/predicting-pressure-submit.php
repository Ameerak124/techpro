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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$sensory=trim($data->sensory);
$sensory_score=trim($data->sensory_score);
$moisture=trim($data->moisture);
$moisture_score=trim($data->moisture_score);
$activity=trim($data->activity);
$activity_score=trim($data->activity_score);
$mobility=trim($data->mobility);
$mobility_score=trim($data->mobility_score);
$nutrition=trim($data->nutrition);
$nutrition_score=trim($data->nutrition_score);
$friction=trim($data->friction);
$friction_score=trim($data->friction_score);
$score=trim($data->score);
$doc_name=trim($data->doc_name);
$remarks=trim($data->remarks);
try {
     if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !='' ){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	   //check if patient discharged or not
	      //check if patient discharged or not
   $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
   $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
   $validate -> execute();
   $validates = $validate->fetch(PDO::FETCH_ASSOC);
   if($validate -> rowCount() > 0){
	   $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
	   $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
	   $validate -> execute();
	   $validates = $validate->fetch(PDO::FETCH_ASSOC);
	   if($validate -> rowCount() > 0){

//insert into the table
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `predicting_pressure_score`(`sno`, `createdfrom`,`ipno`, `umrno`,`sensory`, `sensory_score`, `moisture`, `moisture_score`, `activity`, `activity_score`, `mobility`, `mobility_score`, `nutrition`, `nutrition_score`, `friction`, `friction_score`, `score`,`doc_name`,`remarks`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL, 'Score Assessment',:ipno,:umrno,:sensory,:sensory_score,  :moisture, :moisture_score, :activity, :activity_score, :mobility, :mobility_score, :nutrition, :nutrition_score, :friction, :friction_score, :score, :doc_name, :remarks, 'Active', CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP, :userid, :cost_center,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':sensory', $sensory, PDO::PARAM_STR);
$ninsert->bindParam(':sensory_score', $sensory_score, PDO::PARAM_STR);
$ninsert->bindParam(':moisture', $moisture, PDO::PARAM_STR);
$ninsert->bindParam(':moisture_score', $moisture_score, PDO::PARAM_STR);
$ninsert->bindParam(':activity', $activity, PDO::PARAM_STR);
$ninsert->bindParam(':activity_score', $activity_score, PDO::PARAM_STR);
$ninsert->bindParam(':mobility', $mobility, PDO::PARAM_STR);
$ninsert->bindParam(':mobility_score', $mobility_score, PDO::PARAM_STR);
$ninsert->bindParam(':nutrition', $nutrition, PDO::PARAM_STR);
$ninsert->bindParam(':nutrition_score', $nutrition_score, PDO::PARAM_STR);
$ninsert->bindParam(':friction', $friction, PDO::PARAM_STR);
$ninsert->bindParam(':friction_score', $friction_score, PDO::PARAM_STR);
$ninsert->bindParam(':doc_name', $doc_name, PDO::PARAM_STR);
$ninsert->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Inserted Successfully";
            }else{
			http_response_code(503);
            $response['error']= true;
            $response['message']= "Data Not Inserted";
            } 
		}else{
			$response['error'] = true;
			  $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
		  }
		}else{
			$response['error'] = true;
			  $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
		  }
                            
}else {
	http_response_code(400);	
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
	http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>