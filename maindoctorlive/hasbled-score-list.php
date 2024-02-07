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
$ninsert=$pdoread->prepare("SELECT  `hasbled_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`alcohol`,'--', `alcohol_score`) AS alcohol, CONCAT(`medication`,'--', `medication_score`) AS medication, CONCAT(`age`,'--', `age_score`) AS age , CONCAT(`labile`,'--', `labile_score`) AS labile, CONCAT(`bleeding`, '--',`bleeding_score`) AS bleeding, CONCAT(`stroke`,'--', `stroke_score`) AS stroke,CONCAT(`liver`,'--', `liver_score`) AS liver,CONCAT(`renal`,'--', `renal_score`) AS renal,CONCAT(`hypertension`,'--', `hypertension_score`) AS hypertension, `score` AS total,  DATE_FORMAT(`hasbled_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `hasbled_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`hasbled_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `hasbled_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
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
	            $response['alcohol']="Alcohol use";
	            $response['medicationusage']="Medication usage predisposing to bleeding";
	            $response['age']="Age>=65";
	            $response['labile']="Labile INR";
	            $response['predisposing']="Prior major bleeding or predisposing to bleeding";
	            $response['strokehistory']="Stroke history";
	            $response['liverdisease']="Liver disease";
	            $response['renaldisease']="Renal disease";
	            $response['hypertension']="Hypertension";
	            $response['totalscore']="Total Score";
	            $response['action']="Action";
	
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['hasbledscorelist'][] = $results;
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