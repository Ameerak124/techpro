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
$createdfrom=trim($data->createdfrom);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);

try {
     if(!empty($accesskey) && !empty($createdfrom) && !empty($ipno) && !empty($umrno)){    

$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	
 $my_array = array("Respirational PaO2/FlO2","Coagulation Platelets(1000)","Liver Billirubin (mg/dL)","Cardiovascular Hypotension(MCG/KG/MIN)","CNS Glasgow Coma Score","Renal Createinine (mg/dL) or urine output (mL/d)");

//select into the table
$ninsert=$pdoread->prepare("SELECT  `sofa_score`.`sno`,`createdfrom`, `ipno`, `umrno`, CONCAT(`respirational`,'--', `respirational_score`) AS respirational, CONCAT(`coagulation`,'--', `coagulation_score`) AS coagulation, CONCAT(`liver`,'--', `liver_score`) AS liver , CONCAT(`cardiovascular`,'--', `cardiovascular_score`) AS cardiovascular, CONCAT(`glasgow`, '--',`glasgow_score`) AS glasgow, CONCAT(`renal`,'--', `renal_score`) AS renal, `score` AS total,  DATE_FORMAT(`sofa_score`.`createdon`,'%d-%b-%Y %h:%i:%p') as createdon, `user_logins`.`username` AS createdby FROM `sofa_score` LEFT JOIN `user_logins` ON `user_logins`.`userid`=`sofa_score`.`createdby` WHERE `createdfrom`=:createdfrom AND `ipno`=:ipno AND `umrno`=:umrno AND `estatus`='Active' AND `sofa_score`.`cost_center`=:cost_center ORDER BY createdon DESC");
$ninsert->bindParam(':createdfrom', $createdfrom, PDO::PARAM_STR);
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount()>0){
	http_response_code(200);
	$response['error']=false;
	$response['message']="Data Found";
	        $response['created_date']="Created Date";
	        $response['created_by']="Created By";
	        $response['created_from']="Created From";
	        $response['respirational']="Respirational";
			$response['coagulation_platelets']="Coagulation Platelets";
	        $response['liver_billirubin']="Liver Billirubin";
	        $response['cardio_vascular_hypotension']="Cardio vascular Hypotension";
	        $response['cns_glasgow_coma_score']="CNS Glasgow Coma Score";
	        $response['renal_creatine']="Renal Creatine";
	        $response['total_score']="Total Score";
	        $response['action']="Action";
	
	$response['note']="Risk(<10%) : (0-6) Risk(15-20%) : (7-9) Risk(40-50%) : (10-12) Risk(50-60%) : (13-14) Risk(>80%) : 15 Risk(>90%) : (15-24)";
	for($x = 0; $x < sizeof($my_array); $x++){	
	$response['Fields'][$x]['Title']=$my_array[$x];	
     }	
	
	
    while(  $results = $ninsert->fetch(PDO::FETCH_ASSOC)){
        $response['sofascorelist'][] = $results;
      }
            }else{
			http_response_code(503);
            $response['error']= true;
            $response['message']= "Data Not Found";
			$response['created_date']="Created Date";
	        $response['created_by']="Created By";
	        $response['created_from']="Created From";
	        $response['respirational']="Respirational";
			$response['coagulation_platelets']="Coagulation Platelets";
	        $response['liver_billirubin']="Liver Billirubin";
	        $response['cardio_vascular_hypotension']="Cardio vascular Hypotension";
	        $response['cns_glasgow_coma_score']="CNS Glasgow Coma Score";
	        $response['renal_creatine']="Renal Creatine";
	        $response['total_score']="Total Score";
	        $response['action']="Action";
			
			$response['note']="Risk(<10%) : (0-6) Risk(15-20%) : (7-9) Risk(40-50%) : (10-12) Risk(50-60%) : (13-14) Risk(>80%) : 15 Risk(>90%) : (15-24)";
	        for($x = 0; $x < sizeof($my_array); $x++){	
	        $response['Fields'][$x]['Title']=$my_array[$x];	
     }
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
	$response['message']= "Connection failed".$e->getmessage();
	
}

echo json_encode($response);
$pdoread = null;
?>