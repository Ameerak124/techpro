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
$ninsert=$pdoread->prepare("SELECT  `uanstemi_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`cardiac`,'--', `cardiac_score`) AS cardiac, CONCAT(`ekg`,'--', `ekg_score`) AS ekg, CONCAT(`angina`,'--', `angina_score`) AS angina , CONCAT(`asa`,'--', `asa_score`) AS asa, CONCAT(`cad`, '--',`cad_score`) AS cad,CONCAT(`cadrisk`, '--',`cadrisk_score`) AS cadrisk,CONCAT(`age`,'--', `age_score`) AS age, `score` AS total,  DATE_FORMAT(`uanstemi_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `uanstemi_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`uanstemi_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `uanstemi_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
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
	            $response['positivecardiac']="Positive cardiac marker";
	            $response['ekgst']="EKG ST chares >= 0.5mm";
	            $response['severeangina']="Severe angina (>=2 episodes in 24hrs)";
	            $response['asause']="ASA use in past 7 days";
	            $response['knowncad']="Known CAD (stenosis >=50%)";
	            $response['cadrisk']=">=3 CAD risk factors";
	            $response['age']="Age >65";
	            $response['totalscore']="Total Score";
	            $response['action']="Action";
	
	
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['uanstemiscorelist'][] = $results;
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