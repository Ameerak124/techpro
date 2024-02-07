<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$response1 = array();
$accesskey = $data->accesskey;
$barcode =$data->barcode;
$servicecode =$data->servicecode;
try {

if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT * FROM (SELECT `parametercode`, (CASE WHEN `parametername`='' THEN `service_name` ELSE `parametername` END) AS parametername, `result`, `service_name`,`gender` FROM `sample_report_final` WHERE  `service_code`= :servicecode) AS E WHERE E.parametername NOT IN ('NOTE','') AND  E.result != ''");
$reglist->bindParam(':barcode', $barcode, PDO::PARAM_STR);
$reglist->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$reglist -> execute();
if($reglist-> rowCount() > 0){
while($regres1 = $reglist->fetch(PDO::FETCH_ASSOC)){
    $response['service_name'] = $regres1['service_name'];	

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
		$temp=[
        "method"=>$method,
        "parametername"=>$regres1['parametername'],		
        "result"=>$regres1['result'],		
        "normalresult"=>$ranges,			
		];
		array_push($response1,$temp);
		
	}

	
    http_response_code(200);
    $response['error']= false;
	$response['message']="Data found";
	$response['list']=$response1;

	
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>