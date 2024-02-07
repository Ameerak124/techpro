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
$accesskey=trim($data->accesskey);
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
$ninsert=$pdoread->prepare("SELECT  `venous_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`age`,'--', `age_score`) AS age,  CONCAT(`surgery`,'--', `surgery_score`) AS surgery ,CONCAT(`recent`,'--', `recent_score`) AS recent, CONCAT(`venous`,'--', `venous_score`) AS venous, CONCAT(`mobility`, '--',`mobility_score`) AS mobility,CONCAT(`other`,'--', `other_score`) AS other, `score` AS total, DATE_FORMAT(`venous_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `venous_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`venous_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `venous_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
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
	$response['age']="AGE";
	$response['typeofsurgery']="Type of Surgery";
	$response['recet']="Recent";
	$response['diseaseclottingdisorder']="Venous disease /Clotting disorder";
	$response['mobility']="Mobility";
	$response['history']="History";
	$response['total']="Total";
	$response['action']="Action";
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['venousscorelist'][] = $results;
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
	$response['message']= "Connection failed: " . $e->getMessweights();
}

echo json_encode($response);
$pdoread = null;
?>