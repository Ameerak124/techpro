<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$response = array();
try{
if(!empty($accesskey)){
	
	$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();

if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
		
$response["error"] = false;
$response["message"] = "Data found";
$response["title"] = "Sofa Score";

$listitem = array();
$listitem["title"] = "Respirational PaO2/FlO2";
$listitem1["title"] = "Coagulation Platelets(1000)";
$listitem2["title"] = "Liver Billirubin (mg/dL)";
$listitem3["title"] = "Cardiovascular Hypotension(MCG/KG/MIN)";
$listitem4["title"] = "CNS Glasgow Coma Score";
$listitem5["title"] = "Renal Createinine (mg/dL) or urine output (mL/d)";

//Respirational PaO2/FlO2
$sublist = array();
$sublistitem1 = array("title1" => ">400", "Count" => "0");
$sublistitem2 = array("title1" => "<400 221-301", "Count" => "1");
$sublistitem3 = array("title1" => "<300 142-220", "Count" => "2");
$sublistitem4 = array("title1" => "<200 67-141", "Count" => "3");
$sublistitem5 = array("title1" => "<100 <67", "Count" => "4");
//Coagulation Platelets(1000)
$sublistitem6 = array("title1" => ">150", "Count" => "0");
$sublistitem7 = array("title1" => "<150", "Count" => "1");
$sublistitem8 = array("title1" => "<100", "Count" => "2");
$sublistitem9 = array("title1" => "<50", "Count" => "3");
$sublistitem10 = array("title1" => "<20", "Count" => "4");
//Liver Billirubin (mg/dL)
$sublistitem11= array("title1" => "<1.2", "Count" => "0");
$sublistitem12= array("title1" => "1.2-1.9", "Count" => "1");
$sublistitem13= array("title1" => "2.0-5.9", "Count" => "2");
$sublistitem14= array("title1" => "6.0-11.9", "Count" => "3");
$sublistitem15= array("title1" => ">12.0", "Count" => "4");
//Cardiovascular Hypotension(MCG/KG/MIN)
$sublistitem16= array("title1" => "No hypotension", "Count" => "0");
$sublistitem17= array("title1" => "MAP <70", "Count" => "1");
$sublistitem18= array("title1" => "Dopamine </=5 or dobutamine(any)", "Count" => "2");
$sublistitem19= array("title1" => "Dopamine>5 or norepinephrine </=0.1", "Count" => "3");
$sublistitem20= array("title1" => "Dopamine > 15 or norepinephrine >0.1", "Count" => "4");
//CNS Glasgow Coma Score
$sublistitem21= array("title1" => "15", "Count" => "0");
$sublistitem22= array("title1" => "13-14", "Count" => "1");
$sublistitem23= array("title1" => "10-12", "Count" => "2");
$sublistitem24= array("title1" => "6-9", "Count" => "3");
$sublistitem25= array("title1" => "<6", "Count" => "4");
//Renal Createinine (mg/dL) or urine output (mL/d)
$sublistitem26= array("title1" => "<1.2", "Count" => "0");
$sublistitem27= array("title1" => "1.2-1.9", "Count" => "1");
$sublistitem28= array("title1" => "2.0-3.4", "Count" => "2");
$sublistitem29= array("title1" => "3.5-4.9 or <500", "Count" => "3");
$sublistitem30= array("title1" => ">5.0 or <200", "Count" => "4");




$sublist[] = $sublistitem1;
$sublist[] = $sublistitem2;
$sublist[] = $sublistitem3;
$sublist[] = $sublistitem4;
$sublist[] = $sublistitem5;

$sublist1[] = $sublistitem6;
$sublist1[] = $sublistitem7;
$sublist1[] = $sublistitem8;
$sublist1[] = $sublistitem9;
$sublist1[] = $sublistitem10;

$sublist2[] = $sublistitem11;
$sublist2[] = $sublistitem12;
$sublist2[] = $sublistitem13;
$sublist2[] = $sublistitem14;
$sublist2[] = $sublistitem15;

$sublist3[] = $sublistitem16;
$sublist3[] = $sublistitem17;
$sublist3[] = $sublistitem18;
$sublist3[] = $sublistitem19;
$sublist3[] = $sublistitem20;

$sublist4[] = $sublistitem21;
$sublist4[] = $sublistitem22;
$sublist4[] = $sublistitem23;
$sublist4[] = $sublistitem24;
$sublist4[] = $sublistitem25;

$sublist5[] = $sublistitem26;
$sublist5[] = $sublistitem27;
$sublist5[] = $sublistitem28;
$sublist5[] = $sublistitem29;
$sublist5[] = $sublistitem30;


$listitem["sublist"] = $sublist;
$listitem1["sublist"] = $sublist1;
$listitem2["sublist"] = $sublist2;
$listitem3["sublist"] = $sublist3;
$listitem4["sublist"] = $sublist4;
$listitem5["sublist"] = $sublist5;

$response["sofascorelist"] = array($listitem,$listitem1,$listitem2,$listitem3,$listitem4,$listitem5);


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
 
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
     
     //"Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>