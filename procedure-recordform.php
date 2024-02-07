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
$procedure_type = $data->procedure_type;
//$procedure_name = $data->procedure_name;
$indication = $data->indication;
$note = $data->note;
$intra_pro_complications = $data->intra_pro_complications;
$post_pro_complications = $data->post_pro_complications;
$response = array();
try {	
if(!empty($accesskey) &&!empty($pname) &&!empty($umrno) &&!empty($ipno) &&!empty($procedure_type)  &&!empty($indication) &&!empty($note) &&!empty($intra_pro_complications) &&!empty($post_pro_complications)){ 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey ");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$userid = $result['userid'];
if($check -> rowCount() > 0){
$stmt=$pdo4->prepare("INSERT INTO `procedure_record`( `pname`, `umrno`, `ipno`, `procedure_type`, `doctor_id` ,`indication`, `note`, `intra_pro_complications`, `post_pro_complications`, `created_on`, `created_by`) VALUES (:pname,:umrno,:ipno,:procedure_type,:doctor_id,:indication,:note,:intra_pro_complications,:post_pro_complications,CURRENT_TIMESTAMP,:userid)");
$stmt -> bindParam(":pname", $pname, PDO::PARAM_STR);
$stmt -> bindParam(":umrno", $umrno, PDO::PARAM_STR);
$stmt -> bindParam(":ipno", $ipno, PDO::PARAM_STR);
$stmt -> bindParam(":procedure_type", $procedure_type, PDO::PARAM_STR);
$stmt -> bindParam(":doctor_id", $userid, PDO::PARAM_STR);
//$stmt -> bindParam(":procedure_name", $procedure_name, PDO::PARAM_STR);
$stmt -> bindParam(":indication", $indication, PDO::PARAM_STR);
$stmt -> bindParam(":note", $note, PDO::PARAM_STR);
$stmt -> bindParam(":intra_pro_complications", $intra_pro_complications, PDO::PARAM_STR);
$stmt -> bindParam(":post_pro_complications", $post_pro_complications, PDO::PARAM_STR);
$stmt -> bindParam(":userid",$result['userid'], PDO::PARAM_STR);
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
	     $response['message']="Data not saved";
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
