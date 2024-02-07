<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$source = trim($data->id);
$tempname = trim($data->tempname);
$template = trim($data->template);
$category = trim($data->category);
try {
    if(!empty($accesskey)&& !empty($tempname)&& !empty($template) && !empty($category) && !empty($source)){

$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$doctorid = $result['userid'];
	$verify=$pdoread->prepare("SELECT * FROM `assessment_templates` WHERE `category`=:category AND `template_name`=:tempname AND`doctorid`=:doctorid AND `source`=:id ");
	$verify -> bindParam(":tempname", $tempname, PDO::PARAM_STR);
	$verify -> bindParam(":doctorid", $doctorid, PDO::PARAM_STR);
	$verify -> bindParam(":category", $category, PDO::PARAM_STR);
	$verify -> bindParam(":id", $source, PDO::PARAM_STR);
    $verify->execute();
	if($verify->rowCount()>0){
		$response['error'] = true;
		$response['message'] = "Sorry! template already exists";
	}else{
	/* submit template statrt */
	$submit_template = $pdo4 -> prepare("INSERT INTO `assessment_templates`( `doctorid`,`source`, `category`, `template_name`, `template`, `created_by`, `created_on`) VALUES (:doctorid,:source,:category,:template_name, :template, :created_by, CURRENT_TIMESTAMP)");
	$submit_template -> bindParam(":doctorid", $doctorid, PDO::PARAM_STR);
	$submit_template -> bindParam(":source", $source, PDO::PARAM_STR);
	$submit_template -> bindParam(":category", $category, PDO::PARAM_STR);
	$submit_template -> bindParam(":template_name", $tempname, PDO::PARAM_STR);
	$submit_template -> bindParam(":template", $template, PDO::PARAM_STR);
	$submit_template -> bindParam(":created_by", $doctorid, PDO::PARAM_STR);
	$submit_template -> bindParam(":source", $source, PDO::PARAM_STR);
	$submit_template -> execute();
	if($submit_template -> rowCount() > 0){
		$lastid = $pdo -> lastInsertId();
		$response['error'] = false;
		$response['message'] = "Template Saved Sucessfully";
		$response['lastid'] = $lastid;
		$response['templatename'] = $tempname;
	}
	else{
		$response['error'] = true;
		$response['message'] = "Unable save template";
	}
}
	
}else{
	$response['error']= true;
	$response['message']="Access denied! please try to re-login again";
}
}else{
    $response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} catch(PDOException $e) {
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>