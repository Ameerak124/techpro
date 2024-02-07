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

	http_response_code(200);
	//start
	$response = array(
    "error" => false,
    "message" => "Data Found",

    "dialysisrecordsheet" => array(
        array(
            "title" => "Blood Analysis",
            "dialysis_list" => array(
                array("dialysis_title" => "Pre Dialysis"),
                array("dialysis_title" => "Post Dialysis"),
                array("dialysis_title" => "Other Investigations"),
                array("dialysis_title" => "Doctor Orders")
            )
        ),
        array(
            "title" => "Dialysis Orders",  
            "dialysis_list" => array(
                array("dialysis_title" => "Duration"),  
                array("dialysis_title" => "UF"),
                array("dialysis_title" => "QB"),
                array("dialysis_title" => "QD"),
                array("dialysis_title" => "NA+"),
                array("dialysis_title" => "K+"),
                array("dialysis_title" => "Qᵢₙբ"),
                array("dialysis_title" => "CV")
            )
        ),
        array(
            "title" => "Dialysis Plan",
            "dialysis_list" => array(
                array("dialysis_title" => "Medication (During Dialysis)"),
                array("dialysis_title" => "Use No"),
                array("dialysis_title" => "Initial Bonus"),
                array("dialysis_title" => "Heparinization"),
                array("dialysis_title" => "Heparin Units"),
                array("dialysis_title" => "Units/Hour")
            )
        ),
		),
			
    //end	
);
     
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