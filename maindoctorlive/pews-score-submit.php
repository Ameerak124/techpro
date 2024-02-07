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
$behaviour=trim($data->behaviour);
$cardiovascular=trim($data->cardiovascular);
$respiratory=trim($data->respiratory);  
$behaviour_score=trim($data->behaviour_score);
$cardiovascular_score=trim($data->cardiovascular_score);
$respiratory_score=trim($data->respiratory_score);  
$score=trim($data->score);
 
try {
    
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && ($score) >= 0 && ($score) !=''){ 
	
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
    
                  //check if patient discharged or not
                  $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
                  $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
                  $validate -> execute();
                  $validates = $validate->fetch(PDO::FETCH_ASSOC);
                  if($validate -> rowCount() > 0){
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `pews_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `behaviour`, `behaviour_score`, `cardiovascular`, `cardiovascular_score`, `respiratory`, `respiratory_score`, `score`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`,`del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :behaviour, :behaviour_score, :cardiovascular, :cardiovascular_score, :respiratory, :respiratory_score, :score, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':behaviour', $behaviour, PDO::PARAM_STR);
$ninsert->bindParam(':behaviour_score', $behaviour_score, PDO::PARAM_STR);
$ninsert->bindParam(':cardiovascular_score', $cardiovascular_score, PDO::PARAM_STR);
$ninsert->bindParam(':cardiovascular', $cardiovascular, PDO::PARAM_STR);
$ninsert->bindParam(':respiratory_score', $respiratory_score, PDO::PARAM_STR);
$ninsert->bindParam(':respiratory', $respiratory, PDO::PARAM_STR);
$ninsert->bindParam(':score', $score, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
}else{
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
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
	// http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>