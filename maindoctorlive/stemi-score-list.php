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
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);

try {
     if(!empty($accesskey)&& !empty($ipno) && !empty($umrno)){    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){

//select into the table
$ninsert=$pdoread->prepare("SELECT  `stemi_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`treatment`,'--', `treatment_score`) AS treatment, CONCAT(`anterior`,'--', `anterior_score`) AS anterior, CONCAT(`weights`,'--', `weights_score`) AS weights , CONCAT(`killip`,'--', `killip_score`) AS killip, CONCAT(`heart`, '--',`heart_score`) AS heart, CONCAT(`systolic`,'--', `systolic_score`) AS systolic,CONCAT(`diabetes`,'--', `diabetes_score`) AS diabetes,CONCAT(`age`,'--', `age_score`) AS age, `score` AS total,  DATE_FORMAT(`stemi_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `stemi_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`stemi_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `stemi_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount()>0){
	$response['error']=false;
	$response['message']="Data Found";
	$response['createddt']="Created Dt.";
	            $response['createdby']="Created By.";
	            $response['createdfrom']="Created From";
	            $response['timetotreatment']="Time to treatment >4 hours";
	            $response['anterior']="Anterior ST Elevtion or LBBB";
	            $response['weight']="Weight > 67kg (147.7 lbs)";
	            $response['killip']="Killip Class II-IV";
	            $response['heartrate']="Heart rate >100";
	            $response['systolicbp']="Systolic BP > 100 mmHg";
	            $response['diabetes']="Diabetes , Hypertension or Angina";
	            $response['age']="Age";
	            $response['totalscore']="Total Score";
	            $response['action']="Action";
	
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['stemiscorelist'][] = $results;
      }
            }else{
            $response['error']= true;
            $response['message']= "Data Not Found";
            } 
                            
}else {	
    $response['error'] = true;
	$response['message']= "Access denied!";
}

}else {	
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