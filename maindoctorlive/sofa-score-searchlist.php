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
$response=array();
$data=json_decode(file_get_contents("php://input"));
$accesskey=trim($data->accesskey);
$createdfrom=trim($data->createdfrom);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$searchterm=$data->searchterm;
try {
if(!empty($accesskey) && !empty($createdfrom) && !empty($ipno) && !empty($umrno)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//select into the table
$ninsert=$pdoread->prepare("SELECT  `sofa_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`respirational`,'--', `respirational_score`) AS respirational, CONCAT(`coagulation`,'--', `coagulation_score`) AS coagulation, CONCAT(`liver`,'--', `liver_score`) AS liver , CONCAT(`cardiovascular`,'--', `cardiovascular_score`) AS cardiovascular, CONCAT(`glasgow`, '--',`glasgow_score`) AS glasgow, CONCAT(`renal`,'--', `renal_score`) AS renal, `score` AS total,  DATE_FORMAT(`sofa_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `sofa_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`sofa_score`.`createdby` WHERE `createdfrom`=:createdfrom AND `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `sofa_score`.`cost_center`=:cost_center and user_logins.username like :searchterm ORDER BY createdon DESC");
$ninsert->bindParam(':createdfrom',$createdfrom, PDO::PARAM_STR);
$ninsert->bindParam(':ipno',$ipno,PDO::PARAM_STR);
$ninsert->bindParam(':umrno',$umrno,PDO::PARAM_STR);
$ninsert->bindParam(':cost_center',$result['cost_center'],PDO::PARAM_STR);
$ninsert->bindValue(':searchterm',"%{$searchterm}%",PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Found";
     while($results = $ninsert->fetch(PDO::FETCH_ASSOC)){
           $response['sofascorelist'][]= $results;
      }
      }else{
		    http_response_code(503);
            $response['error']= true;
            $response['message']= "No Data Found";
            }                            
}else{	
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
	        $response['message']= "Connection failed".$e->getmessage();	
}
echo json_encode($response);
$pdoread = null;
?>