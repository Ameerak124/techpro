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
$searchterm = trim($data->searchterm);
try {
if(!empty($accesskey) && !empty($searchterm)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
// Generate Registration List
$reglist = $pdoread -> prepare("SELECT `mwc_bed_master`.`backend_ward`,`mwc_bed_master`.`service_code`,`mwc_bed_master`.`ward_name`,`mwc_bed_master`.`bed_no`,IFNULL(`registration`.`patientname`,'--') AS patientname,IFNULL(CONCAT(DATE_FORMAT(FROM_DAYS(DATEDIFF(now(),`patientage`)), '%Y')+0,'Y(s)/',`patientgender`),'--') AS agesex,IFNULL(DATE_FORMAT(`registration`.`admittedon`,'%d-%b-%Y'),'--') AS admittedon,IFNULL(`registration`.`consultantname`,'--') AS consultantname,IFNULL(`registration`.`department`,'--') AS department,IFNULL(`registration`.`admissionstatus`,'--') AS admissionstatus,if(`registration`.`admissionstatus` = 'Hold','#F7DC6F',if(`registration`.`admissionstatus` != '','#F1B4B4','#7BBD7E')) AS lightcolor,if(`registration`.`admissionstatus` = 'Hold','#D4AC0D',if(`registration`.`admissionstatus` != '','#C32D2D','#19951E')) AS darkcolor,IFNULL(`registration`.`umrno`,'--') AS umrno FROM `mwc_bed_master` LEFT JOIN `registration` ON `mwc_bed_master`.`service_code` = `registration`.`ward_code` WHERE `mwc_bed_master`.`ward_name` LIKE :searchterm");
$reglist->bindParam(':searchterm', $searchterm, PDO::PARAM_STR);
$reglist -> execute();
if($reglist -> rowCount() > 0){
	http_response_code(200);
	$response['error']= false;
	$response['message']= "Data found";
	while($orderres = $reglist->fetch(PDO::FETCH_ASSOC)){
		$response['nursingstationlist'][] = $orderres;
	}
	
}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No data found";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	http_response_code(400);
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