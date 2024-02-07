<?php 
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
//include "whatsapp.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$response1 = array();
$response2 = array();
$accesskey=trim($data->accesskey);
$umrno=trim($data->umrno);
try {
     if(!empty($accesskey) && !empty($umrno)){    
$check = $pdoread -> prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
//get the total line of items count
$check_billno=$pdoread->prepare("SELECT `bill_no`,`patient_category`,`patient_name`,`gender`,`age`,DATE_FORMAT(`bill_date`,'%d-%b-%Y') AS billdate,`referral_doctorname` AS prescribedby,`umrno`,`servicecategory` FROM `lab_worklist` WHERE `umrno` = :umrno AND `bill_status` = 'B' AND `status` = 'A' AND patient_category='IPD' GROUP BY `bill_no` ORDER BY `bill_date` DESC");
$check_billno ->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$check_billno->execute();

if($check_billno->rowCount()>0){
            http_response_code(200);
			$response['error']= false;
	$response['message']="Data found";
	$response['Parameters']="Parameters";
	$response['Result']="Result";
	$response['referralranges']="Referral Ranges";
	while($get_rid=$check_billno->fetch(PDO::FETCH_ASSOC)){

		$level = $pdoread->prepare("SELECT @a:=@a+1 serial_number,E.service_code,E.service_name,E.parameters,E.requisition,E.barcode FROM (SELECT @a:= 0) AS a,(SELECT `service_code`,`service_name`,`barcode`,COUNT(`parametercode`) AS parameters,TO_BASE64(`requisition`) AS requisition  FROM `lab_worklist` WHERE `bill_no` = :bill_no AND `bill_status` = 'B' AND `status` = 'A'  AND patient_category='IPD'  GROUP BY `service_code` ORDER BY `service_name` ASC) AS E");
		$level ->bindParam(':bill_no',$get_rid['bill_no'],PDO::PARAM_STR);
	
		$level->execute();
		if($level->rowCount() > 0){
			
			while($levelget=$level->fetch(PDO::FETCH_ASSOC)){
				
				
				$reglist = $pdoread -> prepare("SELECT * FROM (SELECT `parametercode`, (CASE WHEN `parametername`='' THEN `service_name` ELSE `parametername` END) AS parametername, `result`, `service_name`,`gender`, `normalresult`, `method` FROM `sample_labreport_final` WHERE `barcode`= :barcode AND `service_code`= :servicecode) AS E WHERE   E.result != ''");
$reglist->bindParam(':barcode', $levelget['barcode'], PDO::PARAM_STR);
$reglist->bindParam(':servicecode', $levelget['service_code'], PDO::PARAM_STR);
$reglist -> execute();
if($reglist-> rowCount() > 0){
	
while($regres1 = $reglist->fetch(PDO::FETCH_ASSOC)){
  
$temp1=[
		"method"=>$regres1['method'],	
        "parametername"=>$regres1['parametername'],		
        "result"=>$regres1['result'],		
        "normalresult"=>$regres1['normalresult'],		
		];
		array_push($response2,$temp1);
		
	}
				
}else{

$reglist_old = $pdoread -> prepare("SELECT * FROM (SELECT `parametercode`, (CASE WHEN `parametername`='' THEN `service_name` ELSE `parametername` END) AS parametername, `result`, `service_name`,`gender` FROM `sample_report_final` WHERE `barcode`= :barcode AND `service_code`= :servicecode) AS E WHERE  E.result != ''");
$reglist_old->bindParam(':barcode', $levelget['barcode'], PDO::PARAM_STR);
$reglist_old->bindParam(':servicecode', $levelget['service_code'], PDO::PARAM_STR);
$reglist_old -> execute();
if($reglist_old-> rowCount() > 0){
	
while($regres1_old = $reglist_old->fetch(PDO::FETCH_ASSOC)){
    //$response['service_name'] = $regres1['service_name'];	
/* echo $regres1_old['parametername']; */
       if($regres1_old['service_name']=='CBP(COMPLETE BLOOD PICTURE)'){
		$reglist1=$pdoread->prepare("SELECT if(:gender='Male',`mranges`,`franges`) AS NORMALRANGE, `method` FROM `cbp_ranges` WHERE `parametercode`=:parametercode limit 1");
		$reglist1->bindParam(':parametercode', $regres1_old['parametercode'], PDO::PARAM_STR);
		$reglist1->bindParam(':gender', $regres1_old['gender'], PDO::PARAM_STR);
		 
	}else{
	$reglist1=$pdo->prepare("SELECT lab_param.NORMALRANGE, lab_param.`method` FROM `lab_param`  
    where lab_param.PARAMCD=:parametercode limit 1");
	$reglist1->bindParam(':parametercode', $regres1_old['parametercode'], PDO::PARAM_STR);	 
		
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
	

		$temp1=[
		"method"=>$method,	
        "parametername"=>$regres1_old['parametername'],		
        "result"=>$regres1_old['result'],		
        "normalresult"=>$ranges,		
		];
		array_push($response2,$temp1);
		
	}
				
}	
}			    
				$temp=[
					"serial_number"=>$levelget['serial_number'],
					"service_code"=>$levelget['service_code'],
					"service_name"=>$levelget['service_name'],			
					"parameters"=>$levelget['parameters'],		
					"requisition"=>$levelget['requisition'],	
					"barcode"=>$levelget['barcode'],
					"labreportlist"=>$response2,
				
					  ];
					  array_push($response1,$temp);
					  $response2= array();
				  }

				  $response['laboratorylist'] = $response1;
	
	
				}else{

			

					$response['laboratorylist'] = [];
				
				}
		
			
	}

	
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