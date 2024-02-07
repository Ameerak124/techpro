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
$accesskey = $data->accesskey;
$pname = $data->pname;
$umrno = $data->umrno;
$ipno = $data->ipno;
$ref_doctorid = $data->ref_doctorid;
$ref_doctorname = $data->ref_doctorname;
$priority = $data->priority;
$pk_clinicalproblems = $data->pk_clinicalproblems;
$reason_refferal = $data->reason_refferal;
$reffered_for = $data->reffered_for;
$response = array();
try {	
if(!empty($accesskey) &&!empty($ref_doctorid) &&!empty($ref_doctorname) &&!empty($pname) &&!empty($umrno) &&!empty($ipno)&&!empty($priority) &&!empty($pk_clinicalproblems) &&!empty($reason_refferal) &&!empty($reffered_for)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`,`username` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$userid = $result['userid'];
$username = $result['username'];


if($check -> rowCount() > 0){
$stmt=$pdo4->prepare("INSERT INTO `cross_consultation`( `pname`, `umrno`, `ipno`, `doctorid`, `doctorname`, `ref_doctorid`, `ref_doctorname`,  `priority`, `pk_clinicalproblems`, `reason_refferal`, `reffered_for`,`status`,`created_on`,`created_by`) VALUES (:pname,:umrno,:ipno,:doctorid,:doctorname,:ref_doctorid,:ref_doctorname,:priority,:pk_clinicalproblems,:reason_refferal,:reffered_for,'pending',CURRENT_TIMESTAMP,:doctorid )");
$stmt -> bindParam(":pname", $pname, PDO::PARAM_STR);
$stmt -> bindParam(":umrno", $umrno, PDO::PARAM_STR);
$stmt -> bindParam(":ipno", $ipno, PDO::PARAM_STR);
$stmt -> bindParam(":ref_doctorid", $ref_doctorid, PDO::PARAM_STR);
$stmt -> bindParam(":ref_doctorname", $ref_doctorname, PDO::PARAM_STR);
$stmt -> bindParam(":doctorid", $userid, PDO::PARAM_STR);
$stmt -> bindParam(":doctorname", $username, PDO::PARAM_STR);
$stmt -> bindParam(":priority", $priority, PDO::PARAM_STR);
$stmt -> bindParam(":pk_clinicalproblems", $pk_clinicalproblems, PDO::PARAM_STR);
$stmt -> bindParam(":reason_refferal", $reason_refferal, PDO::PARAM_STR);
$stmt -> bindParam(":reffered_for", $reffered_for, PDO::PARAM_STR);
$stmt -> execute();
if($stmt -> rowCount() > 0){
		 http_response_code(200);  
		 $response['error']= false;
		 $response['message']="Data saved successfully";
	      
     }
	 else
     {
		 http_response_code(503);
          $response['error']= true;
	     $response['message']=" Data not saved";
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
 }catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e;
}
echo json_encode($response);
unset($pdo4);
unset($pdoread);
?>