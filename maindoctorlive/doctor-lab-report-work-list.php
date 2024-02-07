<?php 
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
include "whatsapp.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$response1 = array();
$accesskey=trim($data->accesskey);
$umrno=trim($data->umrno);
$ipaddress=$_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
$mybrowser = get_browser(null, true);
try {
     if(!empty($accesskey)&& !empty($umrno)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
	

	
	
//get the total line of items count
$check_billno=$pdoread->prepare("SELECT `bill_no`,`patient_category`,`patient_name`,`gender`,`age`,DATE_FORMAT(`bill_date`,'%d-%b-%Y') AS billdate,`referral_doctorname` AS prescribedby,`umrno`,`servicecategory` FROM `lab_worklist` WHERE `umrno` = :umrno AND `bill_status` = 'B' AND `status` = 'A' GROUP BY `bill_no` ORDER BY `bill_date` DESC");
$check_billno ->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$check_billno->execute();
$sno = 0;
if($check_billno->rowCount()>0){
while($get_rid=$check_billno->fetch(PDO::FETCH_ASSOC)){
			
		$level = $pdoread->prepare("SELECT @a:=@a+1 serial_number,E.service_code,E.service_name,E.parameters,E.requisition,E.barcode FROM (SELECT @a:= 0) AS a,(SELECT `service_code`,`service_name`,`barcode`,COUNT(`parametercode`) AS parameters,TO_BASE64(`requisition`) AS requisition  FROM `lab_worklist` WHERE `bill_no` = :bill_no AND `bill_status` = 'B' AND `status` = 'A' GROUP BY `service_code` ORDER BY `service_name` ASC) AS E");
		$level ->bindValue(':bill_no',$get_rid['bill_no'],PDO::PARAM_STR);
		$level->execute();
		if($level->rowCount() > 0){
	while($levelget=$level->fetch(PDO::FETCH_ASSOC)){
			
		$reglist = $pdoread -> prepare("SELECT * FROM (SELECT `parametercode`, (CASE WHEN `parametername`='' THEN `service_name` ELSE `parametername` END) AS parametername, `result`, `service_name`,`gender` FROM `sample_report_final` WHERE `barcode`= :barcode AND `service_code`= :servicecode) AS E WHERE E.parametername NOT IN ('NOTE','') AND  E.result != ''");
$reglist->bindParam(':barcode',$levelget['barcode'], PDO::PARAM_STR);
$reglist->bindParam(':servicecode', $levelget['servicecode'], PDO::PARAM_STR);
$reglist -> execute();
if($reglist-> rowCount() > 0){
while($regres1 = $reglist->fetch(PDO::FETCH_ASSOC)){
    //$response['service_name'] = $regres1['service_name'];	

       if($regres1['service_name']=='CBP(COMPLETE BLOOD PICTURE)'){
		$reglist1=$pdoread->prepare("SELECT if(:gender='Male',`mranges`,`franges`) AS NORMALRANGE, `method` FROM `cbp_ranges` WHERE `parametercode`=:parametercode ");
		$reglist1->bindParam(':parametercode', $regres1['parametercode'], PDO::PARAM_STR);
		$reglist1->bindParam(':gender', $regres1['gender'], PDO::PARAM_STR);
		 
	}else{
	$reglist1=$pdoread->prepare("SELECT lab_param.NORMALRANGE, lab_param.`method` FROM `lab_param`  
    where lab_param.PARAMCD=:parametercode ");
	$reglist1->bindParam(':parametercode', $regres1['parametercode'], PDO::PARAM_STR);	 
	}
	 $reglist1-> execute();
	if($reglist1-> rowCount() > 0){
	 $rowss = $reglist1->fetch(PDO::FETCH_ASSOC);
	 $ranges=$rowss['NORMALRANGE'];
	 $method=$rowss['method'];
	}else{
	 $ranges="";
	 $method="";
	}
}
}
		
    $response['error']= false;
	$response['message']="data found";
	$response['laboratorylist'][$sno]['patient_category'] = $get_rid['patient_category'];
	$response['laboratorylist'][$sno]['billno'] = $get_rid['bill_no'];
	$response['laboratorylist'][$sno]['billdate'] = $get_rid['billdate'];
	$response['laboratorylist'][$sno]['umrno'] = $get_rid['umrno'];
	$response['laboratorylist'][$sno]['prescribedby'] = $get_rid['prescribedby'];
	$response['laboratorylist'][$sno]['servicecategory'] = $get_rid['servicecategory'];
    $response['list'][$sno]['level1'][] = $levelget;
    

	}}}
	$sno++;
	
}else{
	$response['error']= true;
	$response['message']="No data found";
}	


}else {	
    $response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
}
}else {	
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	$errorlog = $pdo -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
$errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
$errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
$errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
$errorlog -> execute();
}
echo json_encode($response);
$pdoread = null;
?>