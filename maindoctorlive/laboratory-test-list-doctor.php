<?php 
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
//include "whatsapp.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$accesskey=trim($data->accesskey);
$umrno=trim($data->umrno);
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

if($check_billno->rowCount()>0){

	while($get_rid=$check_billno->fetch(PDO::FETCH_ASSOC)){

		$level = $pdoread->prepare("SELECT @a:=@a+1 serial_number,E.service_code,E.service_name,E.parameters,E.requisition,E.barcode FROM (SELECT @a:= 0) AS a,(SELECT `service_code`,`service_name`,`barcode`,COUNT(`parametercode`) AS parameters,TO_BASE64(`requisition`) AS requisition  FROM `lab_worklist` WHERE `bill_no` = :bill_no AND `bill_status` = 'B' AND `status` = 'A' GROUP BY `service_code` ORDER BY `service_name` ASC) AS E");
		$level ->bindParam(':bill_no',$get_rid['bill_no'],PDO::PARAM_STR);
		$level->execute();
		if($level->rowCount() > 0){

			$levelget=$level->fetchAll(PDO::FETCH_ASSOC);
				$response['error']=false;
    $response['message']="Data found";
				$response['laboratorylist'] = $levelget;
			}
	
		
	

		
	}
/* 	$get=$check_billno->fetchAll(PDO::FETCH_ASSOC); */

   /*  $response['laboratorylist']=$get; */
	
}else{
	 http_response_code(503);
	$response['error'] = true;
	$response['message']= "No records found";

}
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Access denied!";
}
}else {	
    http_response_code(400);
    $response['error'] = true;
	$response['message']= "Sorry! some details are missing ";
}

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
}
echo json_encode($response);
$pdoread = null;
?>