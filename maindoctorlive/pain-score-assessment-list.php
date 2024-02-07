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
//select into the table
$ninsert=$pdoread->prepare("SELECT  `pain_scores_table`.`sno`,`admission_num` as ipno,`assessment_type` as createdform,  CONCAT(`pain_rate`,'/', `pain`) AS 'Pain Score/Pain',CONCAT(`pain_loc`,'/', `pain_char`) AS 'Pain Location/Pain Character', `pain_duration`, `dec_pain`,`inc_pain`,`action_plan`,`intervention`,`interventiond`,  DATE_FORMAT(`pain_scores_table`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `pain_scores_table` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`pain_scores_table`.`createdby` WHERE `admission_num`=:ipno AND `umr_no`=:umrno  AND `pain_scores_table`.`status`='Active' ORDER BY createdon DESC");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount()>0){
    http_response_code(200);
	$response['error']=false;
	$response['message']="Data Found";
	$response['createddt']="Created Dt.";
	$response['createdby']="Created By.";
	$response['createdfrom']="Created From";
	$response['painscore/pain']="Pain Score/Pain";
	$response['painlocation/paincharacter']="Pain Location/Pain Character";
	$response['painduration']="Pain Duration";
	$response['decreasingpain']="Decreasing Pain";
	$response['increasingpain']="Increasing Pain";
	$response['intervention']="Intervention";
	$response['interventiontherapy']="Intervention Therapy";
	$response['action']="Action";
    while( $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['painscoreassessmentlist'][] = $results;
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
$pdoread = null;
?>