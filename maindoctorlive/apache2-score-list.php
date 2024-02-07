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
$ninsert=$pdoread->prepare("SELECT  `apache2_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`age`,'--', `age_score`) AS age,  CONCAT(`history`,'--', `history_score`) AS history ,CONCAT(`rectal`,'--', `rectal_score`) AS rectal, CONCAT(`mean`,'--', `mean_score`) AS mean, CONCAT(`heart`,'--', `heart_score`) AS heart, CONCAT(`respiratory`, '--',`respiratory_score`) AS respiratory,CONCAT(`oxygenation`,'--', `oxygenation_score`) AS oxygenation,CONCAT(`arterial`,'--', `arterial_score`) AS arterial,CONCAT(`sodium`,'--', `sodium_score`) AS sodium,CONCAT(`potassium`,'--', `potassium_score`) AS potassium,CONCAT(`creatinine`,'--', `creatinine_score`) AS creatinine,CONCAT(`hematocrit`,'--', `hematocrit_score`) AS hematocrit,CONCAT(`whiteblood`,'--', `whiteblood_score`) AS whiteblood,CONCAT(`glasgow`,'--', `glasgow_score`) AS glasgow, `score` AS total, DATE_FORMAT(`apache2_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `apache2_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`apache2_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `apache2_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
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
	$response['severeorgan']="History of severe organ insufficiency or immunocompromised";
	$response['rectaltemperature']="Rectal temperature";
	$response['meanarterialpressure']="Mean arterial pressure";
	$response['heartrate']="Heart rate";
	$response['respiratoryrate']="Respiratory rate";
	$response['oxygenation']="Oxygenation";
	$response['arterialph']="Arterial pH";
	$response['serumsodium']="Serum sodium";
	$response['serumpotassium']="Serum potassium";
	$response['serumcreatinine']="Serum creatinine";
	$response['hematocrit']="Hematocrit%";
	$response['whitebloodcount']="White blood count";
	$response['gcs']="GCS";
	$response['total']="Total";
	$response['action']="Action";
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['apache2scorelist'][] = $results;
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