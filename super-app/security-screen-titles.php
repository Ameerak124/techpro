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
//$accesskey = trim($data->accesskey);
try {



	
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
    $response['purpose']= "purpose";
   $response['readingin']= "Reading In";
    $response['readingout']= "Reading Out";
   $response['intitle']= "IN";
    $response['authorizedby']= "Authorized By";
    $response['intimedetails']= "In Time Details";
     $response['outtimedetails']= "Out Time Details";
      $response['createdon']= "Created On";
      $response['address']= "Address";																							
      $response['datetime']= "Date & Time";
	  $response['intime']= "In time";
	  $response['outtime']= "Out time";
	  $response['whomtomeet']= "Whom to Meet";
	  $response['exit']= "Exit";
	  $response['reason']= "Reason";
	  $response['location']= "Location";
	  $response['remarks']= "Remarks";
	  $response['showsignature']= "Show Signature";
	  $response['broughtby']= "Brought By";
	  $response['dcinvoiceno']= "DC / Invoice No";
	  $response['dcinvoicedate']= "DC / Invoice Date";
	  $response['quantity']= "Quantity : ";
	  $response['id']= "Id";
	  $response['billnumber']= "Bill Number";
	  $response['rate_inr']= "Rate (INR)";
	  $response['sale_inr']= "Sale (INR)";
	  $response['volume_ltr']= "Volume (Ltr)";
	  $response['vehiclereading_kms']= "Vehicle Reading (Kms)";
	  $response['signature_label']= "Signature of Inward";
	  $response['totalkms']= "Total Kms";
	  $response['noofkeys']= "No of Keys";
	  $response['depositedby']= "Deposited By";
	  $response['empid']= "Emp Id";
	  $response['takensecuritysignature']= "Security\xASignature";
	  $response['takendigitalsignature']= "User\xASignature";
	  $response['depositsignature']= "Deposit\xASignature";
	  $response['depositsecuritysignature']= "Security\xASignature";
	  $response['outtitle']= "Out";
	  $response['keyno']= "Key No";
	  $response['empsignature']= "Employee Signature";
	  $response['pendingkeys']= "Pending Keys";
	

} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e;
}
echo json_encode($response);

?>