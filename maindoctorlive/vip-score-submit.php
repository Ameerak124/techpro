<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey=trim($data->accesskey);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
$types=trim($data->types);
$types_score=trim($data->types_score); 
try {
    if(!empty($accesskey) && !empty($ipno) && !empty($umrno) && !empty($types) ){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
            $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
            $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
            $validate -> execute();
            $validates = $validate->fetch(PDO::FETCH_ASSOC);
            if($validate -> rowCount() > 0){
       if($types_score == 0){
            $signs='No Signs of phlebitis';
            $observe='Observe Cannula';
       }else if($types_score == 1){
        $signs='Possible first Signs of phlebitis';
        $observe='Observe Cannula';
        }else if($types_score == 2){
        $signs='Early stage of phlebitis';
        $observe='Resite Cannula';
        }else if($types_score == 3){
            $signs='Medium stage of phlebitis';
            $observe='Resite Cannula consider treatment';
        }else if($types_score == 4){
            $signs='Advance stage of Phlebitis or start of thrombophlebitis';
            $observe='Resite canunula consider treatment';
        }else {
            $signs='Advance stage of thrombophlebitis';
            $observe='Initiate treatment resiste cannula';
        }   
    
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `vip_score`(`sno`, `createdfrom`, `ipno`, `umrno`,  `types`, `types_score`, `signs`, `observe`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`, `del_remarks`) VALUES (NULL,'Score Assessment',:ipno,:umrno, :types, :types_score, :signs, :observe, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':types', $types, PDO::PARAM_STR);
$ninsert->bindParam(':types_score', $types_score, PDO::PARAM_STR);
$ninsert->bindParam(':signs', $signs, PDO::PARAM_STR);
$ninsert->bindParam(':observe', $observe, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
	http_response_code(200);
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="Data Not Inserted";
}
}else{
	http_response_code(503);
    $response['error'] = true;
      $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
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
$pdo4 = null;
$pdoread = null;
?>