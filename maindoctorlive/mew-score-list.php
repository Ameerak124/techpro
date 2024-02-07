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
$ninsert=$pdoread->prepare("SELECT  `mew_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`tempu`,'--', `tempu_score`) AS tempu, CONCAT(`systolic`,'--', `systolic_score`) AS systolic, CONCAT(`heart`,'--', `heart_score`) AS heart , CONCAT(`resp`,'--', `resp_score`) AS resp, CONCAT(`loc`, '--',`loc_score`) AS loc, CONCAT(`spo`,'--', `spo_score`) AS spo, `score` AS total, `display_name`, CONCAT(`color`,'color: black !important;') as color, DATE_FORMAT(`mew_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `mew_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`mew_score`.`createdby` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `mew_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
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
	            $response['temp']="Temperature";
	            $response['systolic']="Systolic BP";
	            $response['heartrate']="Heart Rate";
	            $response['resp']="Resp Rate";
	            $response['loc']="L.O.C(AVPU)";
	            $response['spo2']="SP02";
	            $response['totalscore']="Total Score";
	            $response['remarks']="Remarks";	
	            $response['action']="Action";
	
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['mewscorelist'][] = $results;
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
$pdo4 = null;
$pdoread = null;
?>