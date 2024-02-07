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
try {
    if (!empty($accesskey)) {
        $check = $pdoread->prepare("SELECT `username`, `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        if ($check->rowCount() > 0) {
            $result = $check->fetch(PDO::FETCH_ASSOC);
            $my_array1 = array("History of Fall", "Secondary Diagnosis", "Ambulatory Aid", "IV or IV Acess", "Gait","Mental Status");
		  http_response_code(200);
		  $response =[
		 "error" => "false",
		 "message" => "Data Found",
		 "title"=>"Fall Risk Assessment",
		 "list" =>
           array( "title" => "History of Fall",
			    "Sublist" =>array("title1" =>"No","Count" =>"0"),
			    array("title1" => "Yes","Count" => "25"),),
          array("title" => "Secondary Diagnosis",
		        "Sublist" =>array("title1" =>"No","Count" =>"0"),
			   array("title1" => "Yes","Count" => "15"),),
          array("title" => "Ambulatory Aid",
			"Sublist" =>array("None/bed rest/Nurse Assist
			" =>"No","Count"=>"15","Furniture"=>"30"),
			array("Furniture" => "Yes","Count" => "20"),), 
          array("title" => "IV or IV Acess","Sublist" =>array("title1" =>"No","Count" =>"0"),
		array("title1" => "Yes","Count" => "20"),),
          array("title" => "Gait",
			"Sublist" =>array("Normal/Bed Rest/Wheel Chair
			" =>"0","Weak"=>"10","Impared"=>"20"),),
		array("title" => "Mental Status",
			"Sublist" =>array("Oriented own Ability
			" =>"No","Count" =>"0"),
			array("Over estimates or forgets Limitations" => "Yes","Count" => "15"),),
			];
        } else {
            http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Access denied!";
        }
    } else {
        http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry! Some details are missing";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>
