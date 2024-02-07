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
$accesskey = trim($data->accesskey);
$did = trim($data->did);
$ipno = trim($data->ipno);
$umrno = trim($data->umrno);
$dates = trim($data->dates);
$timeszone = trim($data->timeszone);
$bp =trim($data->bp);
$vp = trim($data->vp);
$uf = trim($data->uf);
$qb = trim($data->qb);
$heparin = trim($data->heparin);
$remarks = trim($data->remarks);
try{
    
    if(!empty($accesskey) && !empty($ipno) && !empty($umrno)){
        $check = $pdoread -> prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check -> execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if($check -> rowCount() > 0){

        
//insert query
$ninsert=$pdo4->prepare("INSERT IGNORE INTO `dialysis_record_sheet`(`sno`, `did`,`ipno`, `umrno`, `dates`, `timeszone`, `bp`, `vp`, `uf`, `qb`, `heparin`, `remarks`, `estatus`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `cost_center`) VALUES (NULL, :did,:ipno, :umrno, :dates, :timeszone, :bp, :vp, :uf, :qb, :heparin, :remarks, 'Active',CURRENT_TIMESTAMP,:userid,CURRENT_TIMESTAMP,:userid,:cost_center) ");
$ninsert->bindParam(':did', $did, PDO::PARAM_STR);
$ninsert->bindParam(':ipno', $ipno, PDO::PARAM_STR);
$ninsert->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$ninsert->bindParam(':dates', $dates, PDO::PARAM_STR);
$ninsert->bindParam(':timeszone', $timeszone, PDO::PARAM_STR);
$ninsert->bindParam(':bp', $bp, PDO::PARAM_STR);
$ninsert->bindParam(':vp', $vp, PDO::PARAM_STR);
$ninsert->bindParam(':uf', $uf, PDO::PARAM_STR);
$ninsert->bindParam(':qb', $qb, PDO::PARAM_STR);
$ninsert->bindParam(':heparin', $heparin, PDO::PARAM_STR);
$ninsert->bindParam(':remarks', $remarks, PDO::PARAM_STR);
$ninsert->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$ninsert->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
$ninsert->execute();
if($ninsert->rowCount() > 0){
    $response['error']=false;
    $response['message']="Data Inserted Successfully";
}else{
    $response['error']=true;
    $response['message']="Data Not Inserted";
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
	$response['messheart']= "Connection failed";
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>