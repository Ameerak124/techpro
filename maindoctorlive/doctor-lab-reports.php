<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$response1 = array();
$accesskey = $data->accesskey;
$barcode =$data->barcode;
$servicecode =$data->servicecode;

$class_obj = new numbertowordconvertsconver();
try {
/* $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
 set the PDO error mode to exception
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
echo "Connected successfully"; */
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT * FROM (SELECT `parametercode`, (CASE WHEN `parametername`='' THEN `service_name` ELSE `parametername` END) AS parametername, `result`, `service_name`,`gender` FROM `sample_report_final` WHERE `barcode`= :barcode AND `service_code`= :servicecode) AS E WHERE E.parametername NOT IN ('NOTE','') AND  E.result != ''");
$reglist->bindParam(':barcode', $barcode, PDO::PARAM_STR);
$reglist->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
$reglist -> execute();
if($reglist-> rowCount() > 0){
	$temp1=[
		"method"=>"",	
        "parametername"=>"Parameters",		
        "result"=>"Result",		
        "normalresult"=>"Referral Ranges",		
		];
		array_push($response1,$temp1);
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
    /* $response['error']= false;
	$response['message']="Data found";
    $response['labreportlist']=$response1; */

    $response['error']= false;
	$response['message']="Data found";
	$response['Parameters']="Parameters";
	$response['Result']="Result";
	$response['referralranges']="Referral Ranges";
	
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
<?php
class numbertowordconvertsconver {
    function convert_number($number) 
    {
        if (($number < 0) || ($number > 999999999)) 
        {
            throw new Exception("Number is out of range");
        }
        $giga = floor($number / 1000000);
        // Millions (giga)
        $number -= $giga * 1000000;
        $kilo = floor($number / 1000);
        // Thousands (kilo)
        $number -= $kilo * 1000;
        $hecto = floor($number / 100);
        // Hundreds (hecto)
        $number -= $hecto * 100;
        $deca = floor($number / 10);
        // Tens (deca)
        $n = $number % 10;
        // Ones
        $result = "";
        if ($giga) 
        {
            $result .= $this->convert_number($giga) .  "Million";
        }
        if ($kilo) 
        {
            $result .= (empty($result) ? "" : " ") .$this->convert_number($kilo) . " Thousand";
        }
        if ($hecto) 
        {
            $result .= (empty($result) ? "" : " ") .$this->convert_number($hecto) . " Hundred";
        }
        $ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
        $tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
        if ($deca || $n) {
            if (!empty($result)) 
            {
                $result .= " and ";
            }
            if ($deca < 2) 
            {
                $result .= $ones[$deca * 10 + $n];
            } else {
                $result .= $tens[$deca];
                if ($n) 
                {
                    $result .= "-" . $ones[$n];
                }
            }
        }
        if (empty($result)) 
        {
            $result = "zero";
        }
        return $result;
    }
}
?>