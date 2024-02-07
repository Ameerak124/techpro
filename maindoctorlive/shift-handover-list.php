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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$ip = trim($data->ip);

try {
if(!empty($ip) &&!empty($accesskey)){

$check = $pdoread -> prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
if($check -> rowCount() > 0){
   $list = $pdoread->prepare("SELECT @a:=@a+1 serial_number, `shifttype`,substring_index(`modifiedby`,'-',-1) as notes_done_by, (DATE_FORMAT(`modifiedon`, '%d-%b-%Y %H:%i:%s')) as date_time, `wardname` as loc, `bq1`, `tq1`, `bq2`, `tq2`, `bq3`, `tq3`, `bq4`, `tq4`, `bq5`, `tq5`, `bq6`, `tq6`, `bq7`, `tq7`, `bq8`, `tq8`, `bq9`, `tq9`, `bq10`, `tq10`, `q11`, `q12`, `q13`, `q14`, `remarks`, `handoverby_n`, `takeoverby_n`, `handoverby_d`, `takeoverby_d` FROM (SELECT @a:= 0) a, `shift_handover` where `admissionno` = :ip and `estatus` = 'Active' ");
        $list->bindParam(':ip', $ip, PDO::PARAM_STR);
        $list->execute();
        if($list-> rowCount() > 0){
			http_response_code(200);
            $response['error'] = false;
          $response['message']= "Data found";
          $response['notesdoneby']= "Notes Done By";
          $response['datetime']= "Date & Time";
          $response['location']= "Location";
          $response['verificationwithdocumentation']= "Bed Side & Verification With Documentation";
          $response['visitedthepatient']= "Hand Off &Receiving Nurse Together Visited The Patient";
          $response['shiftshiftchange']= "Informed The Patient Regarding The Shift & Shift Change";
          $response['Nameumrno']= "Identified The Patient Name & UMR NO";
          $response['linestubesivfluiddone']= "Quick Assessment Of Patient,Lines,Tubes,IV Fluid Done";
          $response['identifiedkeptunderlock']= "Stock Medication Identified & Kept Under Lock";
          $response['isbarformat']= "Handling Over & Receiving Done On ISBAR Format";
          $response['currentsituationexplained']= "Admitted Back Ground & Current Situation Explained";
          $response['signsverified']= "Current Vitals Signs Verified";
          $response['dischargeplanexplained']= "Current Condition And Discharge Plan Explained";
          $response['explainedandverified']= "All Drug & Non-Drug Orders Explained And Verified";
          $response['ordersorpendingordersidentified']= "Any New Orders Or Pending Orders Identified";
          $response['pendingreports']= "Any New Lab Investigations Or Pending Reports";
          $response['isolationrequirements']= "Any Critical Value/Positive Or Culture/Isolation Requirements";
          $response['nursingdocuments']= "Nursing Documents";
          $response['remarks']= "Remarks";
          $response['handoverbynurse']= "Handover By Nurse";
          $response['handoverdt']= "Handover Dt";
          $response['takeoverbynurse']= "Takeover By Nurse";
          while(  $results = $list->fetch(PDO::FETCH_ASSOC)){
            $response['shifthandoverlist'][] = $results;
          }
          }else{
			  http_response_code(503);
              $response['error'] = true;
              $response['message']= "No data found";
          }
//Check User Access End
}else{
	http_response_code(400);
    $response['error'] = true; 
      $response['message']= "Access Denied";
  }
}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e->getMessage();;
}
echo json_encode($response);
$pdoread = null;
?>
