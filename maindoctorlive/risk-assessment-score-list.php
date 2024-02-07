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
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
try {
    if(!empty($accesskey)&& !empty($ipno) && !empty($umrno)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//insert into the table
$ninsert=$pdoread->prepare("SELECT `risk_assessment_score`.`sno`,`createdfrom`, `ipno`, `umrno`,  `assessment_type`, CONCAT(`hfall`, '--',`hfall_score`) AS hfall, CONCAT(`diagnosis`,'--', `diagnosis_score`) AS diagnosis, CONCAT(`ambulatory`, '--',`ambulatory_score`) AS ambulatory, CONCAT(`acess`, '--',`acess_score`) AS acess, CONCAT(`gait`, '--',`gait_score`) AS gait, CONCAT(`mental`,'--' ,`mental_score`) AS mental, (CASE WHEN `score` BETWEEN  0 AND 24 THEN CONCAT(`score`,' ','(Low Risk (0-24)- Always One Attended With Patient)') WHEN `score` BETWEEN  25 AND 44 THEN CONCAT(`score`,' ','(Medium Risk/High Risk (25-44) - Accompany One Nurse And Attendant With Patient For Ambulation If Needed)') WHEN `score` >=45 THEN CONCAT(`score`,' ','(High Risk (45 and Above) - Not Allow The Patient To Move From The Bed)') ELSE '' END)AS total,  DATE_FORMAT(`risk_assessment_score`.`createdon`, '%d-%b-%Y %h:%i:%p') AS createdon, `user_logins`.`username` AS createdby FROM `risk_assessment_score` LEFT JOIN  `user_logins` ON `user_logins`.`userid`=`risk_assessment_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `risk_assessment_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount()>0){
    http_response_code(200);
	$response['error']=false;
	$response['message']="Data Found";
	$response['createddt']="Created Dt.";
	$response['createdby']="Created By.";
	$response['createdfrom']="Created From";
	$response['historyoffall']="History Of Fall";
	$response['secondarydiagnosis']="Secondary Diagnosis";
	$response['ambulatoryaid']="Ambulatory Aid";
	$response['ivorivacess']="IV or IV Acess";
	$response['gait']="Gait";
	$response['mentalstatus']="Mental Status";
	$response['totalscore']="Total Score";
	$response['delete']="Delete";
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['riskassessmentlist'][] = $results;
      }
            }else{
                http_response_code(503);
            $response['error']= true;
            $response['message']= "Data Not Found";
            }                            
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {	
    http_response_code(503);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdoread = null;
?>