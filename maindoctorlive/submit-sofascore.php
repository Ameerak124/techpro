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
$createdfrom=trim($data->createdfrom);
$ipno=trim($data->ipno);
$umrno=trim($data->umrno);
/* $respirational=trim($data->respirational);
$coagulation=trim($data->coagulation);
$liver=trim($data->liver);  
$cardiovascular=trim($data->cardiovascular);
$glasgow=trim($data->glasgow);
$renal=trim($data->renal);
$respirational_score=trim($data->respirational_score);
$coagulation_score=trim($data->coagulation_score);
$liver_score=trim($data->liver_score);  
$cardiovascular_score=trim($data->cardiovascular_score);
$glasgow_score=trim($data->glasgow_score);
$renal_score=trim($data->renal_score);*/
$score=trim($data->score);
$sofascore_values_list= ($data->sofascore_values_list);
try {
 
 
    if(!empty($accesskey) && !empty($ipno)&& !empty($umrno) && !empty($createdfrom) && ($score) >= 0 && ($score) !=''){  
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){
		
		
$sql=$pdo4->prepare("INSERT IGNORE INTO `sofa_score`(`sno`, `createdfrom`,`ipno`, `umrno`,  `respirational`, `respirational_score`,`coagulation`,`coagulation_score`,`liver`,`liver_score`,`cardiovascular`,`cardiovascular_score`,`glasgow`,`glasgow_score`,`renal`, `renal_score`, `score`, `estatus`, `createdon`, `createdby`,`cost_center`, `del_remarks`) VALUES (NULL,:createdfrom,:ipno,:umrno, '', '','','','','','','','','','', '', :score, 'Active',CURRENT_TIMESTAMP,:userid,:costcenter,'') ");
$sql->bindParam(':createdfrom', $createdfrom, PDO::PARAM_STR);
$sql->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql->bindParam(':umrno', $umrno, PDO::PARAM_STR);
/* $sql->bindParam(':respirationals', $respirational, PDO::PARAM_STR);
$sql->bindParam(':respirational_score', $respirational_score, PDO::PARAM_STR);
$sql->bindParam(':coagulation_score', $coagulation_score, PDO::PARAM_STR);
$sql->bindParam(':coagulation', $coagulation, PDO::PARAM_STR);
$sql->bindParam(':liver_score', $liver_score, PDO::PARAM_STR);
$sql->bindParam(':liver', $liver, PDO::PARAM_STR);
$sql->bindParam(':cardiovascular', $cardiovascular, PDO::PARAM_STR);
$sql->bindParam(':cardiovascular_score', $cardiovascular_score, PDO::PARAM_STR);
$sql->bindParam(':glasgow_score', $glasgow_score, PDO::PARAM_STR);
$sql->bindParam(':glasgow', $glasgow, PDO::PARAM_STR);
$sql->bindParam(':renal_score', $renal_score, PDO::PARAM_STR);
$sql->bindParam(':renal', $renal, PDO::PARAM_STR); */
$sql->bindParam(':score', $score, PDO::PARAM_STR);
$sql->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$sql->bindParam(':costcenter', $result['cost_center'], PDO::PARAM_STR); 
$sql->execute();
if($sql->rowCount() > 0){
	
	
	foreach($sofascore_values_list as $row){
		
	$title=$row->title;
		
		if($title=='Respirational PaO2/FlO2'){

$sql1=$pdo4->prepare("UPDATE `sofa_score` SET `respirational`=:respirational,`respirational_score`=:respirational_score WHERE `ipno`=:ipno and `umrno`=:umrno and estatus='Active'");
$sql1->bindParam(':respirational', $row->selectedcountvalue, PDO::PARAM_STR);
$sql1->bindParam(':respirational_score', $row->selectedCount, PDO::PARAM_STR);
$sql1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$sql1->execute();
	}else if($title=='Coagulation Platelets(1000)'){
	
$sql1=$pdo4->prepare("UPDATE `sofa_score` SET `coagulation`=:coagulation,`coagulation_score`=:coagulation_score WHERE `ipno`=:ipno and `umrno`=:umrno and estatus='Active'");
$sql1->bindParam(':coagulation', $row->selectedcountvalue, PDO::PARAM_STR);
$sql1->bindParam(':coagulation_score', $row->selectedCount, PDO::PARAM_STR);	
$sql1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$sql1->execute();
}else if($title=='Liver Billirubin (mg/dL)'){

$sql1=$pdo4->prepare("UPDATE `sofa_score` SET `liver`=:liver,`liver_score`=:liver_score WHERE `ipno`=:ipno and `umrno`=:umrno and estatus='Active'");
$sql1->bindParam(':liver', $row->selectedcountvalue, PDO::PARAM_STR);
$sql1->bindParam(':liver_score', $row->selectedCount, PDO::PARAM_STR);	
$sql1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$sql1->execute();
}else if($title=='Cardiovascular Hypotension(MCG/KG/MIN)'){

$sql1=$pdo4->prepare("UPDATE `sofa_score` SET `cardiovascular`=:cardiovascular,`cardiovascular_score`=:cardiovascular_score WHERE `ipno`=:ipno and `umrno`=:umrno and estatus='Active'");
$sql1->bindParam(':cardiovascular', $row->selectedcountvalue, PDO::PARAM_STR);
$sql1->bindParam(':cardiovascular_score', $row->selectedCount, PDO::PARAM_STR);	
$sql1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$sql1->execute();
}else if($title=='CNS Glasgow Coma Score'){

$sql1=$pdo4->prepare("UPDATE `sofa_score` SET `glasgow`=:glasgow,`glasgow_score`=:glasgow_score WHERE `ipno`=:ipno and `umrno`=:umrno and estatus='Active'");
$sql1->bindParam(':glasgow', $row->selectedcountvalue, PDO::PARAM_STR);
$sql1->bindParam(':glasgow_score', $row->selectedCount, PDO::PARAM_STR);
$sql1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$sql1->execute();
}else if($title=='Renal Createinine (mg/dL) or urine output (mL/d)'){

$sql1=$pdo4->prepare("UPDATE `sofa_score` SET `renal`=:renal,`renal_score`=:renal_score WHERE `ipno`=:ipno and `umrno`=:umrno and estatus='Active'");
$sql1->bindParam(':renal', $row->selectedcountvalue, PDO::PARAM_STR);
$sql1->bindParam(':renal_score', $row->selectedCount, PDO::PARAM_STR);
$sql1->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$sql1->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$sql1->execute();
}
	}
	http_response_code(200);
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
	http_response_code(503);
    $response['error']=true;
    $response['message']="Data Not Inserted";
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
	$response['message']= "Connection failed: " . $e->getMessage();
	
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>