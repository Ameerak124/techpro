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
$accesskey=trim($data->accesskey);
//$issues =$data->issues;
//$issue_details =$data->issue_details;
$remarks =$data->remarks;
$branch =$data->branch;
//$assigned_person =$assigned_person->remarks;
//$photo =$data->photo;
$raiseticketattach = $data->raiseticketattach;
$raiseticketattachfilename=$data->raiseticketattachfilename;
$decodedraiseticketattachimage=base64_decode($raiseticketattach);

try{
if(!empty($accesskey)){
	
	
$check =$pdo->prepare("SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();

if($check->rowCount()>0){
$result = $check->fetch(PDO::FETCH_ASSOC);






$select=$pdo->prepare("SELECT Concat('EMP',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`ticket_id`),'EMP23080000'),Concat('EMP',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS ticketid FROM `employee_raise_ticket` where ticket_id like concat('%EMP',DATE_FORMAT(CURRENT_DATE,'%y'),'%') ORDER BY SNO DESC LIMIT 1");
$select->execute();
$data=$select->fetch(PDO::FETCH_ASSOC);
$ticket_id=$data['ticketid'];

$time=date("Y-m-d-H-i-s");		
	if(!empty($raiseticketattachfilename)){ 
        
		 $documenttype = 'raiseticketattach';
		$filename = $raiseticketattachfilename;
		 $fileType = pathinfo($raiseticketattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($raiseticketattachfilename);
		$filesize = decodedraiseticketattachimage["size"];
		$filepath = decodedraiseticketattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$raiseticketattach = pdfupload($filename,$filesize,$ticket_id,$time,$documenttype,$decodedraiseticketattachimage);
		 } else {
			
				$raiseticketattach = imageupload($filename,$ticket_id,$time,$documenttype,$decodedraiseticketattachimage);
			 
		 }	
		
		}else{ 
        $raiseticketattach = 'Please select an image file to upload.'; 
    } 
		
		
		
		$stmt2= $pdo->prepare("SELECT concat(user_logins.username,' - ','(',user_logins.userid,')') as assigningperson,`tokenid`,username ,role FROM `employee_level_designations` inner join user_logins on user_logins.role=employee_level_designations.`NL`  where cost_center =:branch and employee_level_designations.CL=:role  limit 1");
		$stmt2->bindParam(":branch", $result['cost_center'], PDO::PARAM_STR);
		$stmt2->bindParam(":role", $result['role'], PDO::PARAM_STR);
		$stmt2->execute();
		if($stmt2->rowCount()>0){
    $result1 = $stmt2->fetch(PDO::FETCH_ASSOC);
       $tokenid = $result1['tokenid'];
					$empname = $result1['username'];


//echo $result1['assigningperson'];


$statusact='Pending at '.$result1['role'];
		

$insert=$pdo->prepare("INSERT INTO `employee_raise_ticket`(`user_id`, `user_name`, `role`, `branch`, `ticket_id`,  `remarks`, `photo`, `status`, `created_by`, `created_on`,`assigned_person`,realstatus) VALUES (:userid,:user_name,:role,:branch,:ticket_id,:remarks,:raiseticketattach,'Pending',:userid,CURRENT_TIMESTAMP,:assigningperson,:realstatus)");
$insert->bindParam(":type",$type,PDO::PARAM_STR);
$insert->bindParam(":ticket_id",$ticket_id,PDO::PARAM_STR);
/* $insert->bindParam(":category",$category,PDO::PARAM_STR);
$insert->bindParam(":issues",$issues,PDO::PARAM_STR);
$insert->bindParam(":issue_details",$issue_details,PDO::PARAM_STR); */
$insert->bindParam(":remarks",$remarks,PDO::PARAM_STR);
$insert->bindParam(":raiseticketattach",$raiseticketattach,PDO::PARAM_STR);
$insert->bindParam(":userid",$result['userid'],PDO::PARAM_STR);
$insert->bindParam(":user_name",$result['username'],PDO::PARAM_STR);
$insert->bindParam(":role",$result['role'],PDO::PARAM_STR);
$insert->bindParam(":branch",$branch,PDO::PARAM_STR);
$insert->bindParam(":assigningperson",$result1['assigningperson'],PDO::PARAM_STR);
$insert->bindParam(":realstatus",$statusact,PDO::PARAM_STR);
$insert->execute();

$sno_issued=$pdo->lastInsertId();










$insert1=$pdo->prepare("INSERT INTO `employee_issue_logs`(`issue_sno`, `ticket_id`, `assigned_person`, `role`, `created_by`, `created_by_name`, `created_on`, `status`,reason) VALUES (:issue_sno,:ticket_id,:assigned_person,:role,:userid,:user_name,CURRENT_TIMESTAMP,:status,:remarks)");
$insert1->bindParam(":issue_sno",$sno_issued,PDO::PARAM_STR);
$insert1->bindParam(":ticket_id",$ticket_id,PDO::PARAM_STR);
$insert1->bindParam(":assigned_person",$result1['assigningperson'],PDO::PARAM_STR);
$insert1->bindParam(":role",$result['role'],PDO::PARAM_STR);
$insert1->bindParam(":userid",$result['userid'],PDO::PARAM_STR);
$insert1->bindParam(":user_name",$result['username'],PDO::PARAM_STR);
$insert1->bindParam(":status",$statusact,PDO::PARAM_STR);
$insert1->bindParam(":remarks",$remarks,PDO::PARAM_STR);
$insert1->execute();

if ($insert->rowCount()>0){
    http_response_code(200);
   $response['error']=false;
   $response['message']="Data inserted";
   }else{
    http_response_code(503);	
   $response['error']=true;
   $response['message']="No Data inserted";
   }
   }else{
			
	http_response_code(503);	
   $response['error']=true;
   $response['message']="Unauthorized Access";
		}
}else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
}
}else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! some details are missing';
}   
}catch(PDOEXCEPTION $e){
    http_response_code(400);
    $response['error']=true;
    $response['message']= "Connection failed:".$e->getMessage();
 }
function compressImage($source, $destination, $quality) { 
    // Get image info 
    $imgInfo = getimagesize($source); 
    $mime = $imgInfo['mime']; 
    
    // Create a new image from file 
    switch($mime){ 
	  case 'image/jpg': 
            $image = imagecreatefromjpeg($source); 
           imagejpeg($image, $destination, $quality);
            break; 
			case 'image/JPG': 
            $image = imagecreatefromJPEG($source); 
           imagejpeg($image, $destination, $quality);
            break;
        case 'image/jpeg': 
            $image = imagecreatefromjpeg($source); 
           imagejpeg($image, $destination, $quality);
            break; 
			case 'image/JPEG': 
            $image = imagecreatefromJPEG($source); 
           imagejpeg($image, $destination, $quality);
            break; 
        case 'image/png': 
            $image = imagecreatefrompng($source); 
            imagepng($image, $destination, $quality);
            break; 
			case 'image/PNG': 
            $image = imagecreatefromPNG($source); 
            imagepng($image, $destination, $quality);
            break;
        case 'image/gif': 
            $image = imagecreatefromgif($source); 
            imagegif($image, $destination, $quality);
            break; 
		case 'image/GIF': 
            $image = imagecreatefromGIF($source); 
            imagegif($image, $destination, $quality);
            break;
			
        default: 
            $image = imagecreatefromjpeg($source); 
           imagejpeg($image, $destination, $quality);
    } 
     
     
    // Return compressed image 
    return $destination; 
}
function pdfupload($filename,$filesize,$ticket_id,$time,$documenttype,$filepath){
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
if ($filesize < 500000)//500000 = 500kb
    {
		
       $location = "documents/raiseticketattach/".$ticket_id;
      // $location = "documents/raiseticketattach";
	 
    
			if(!is_dir($location)){
       mkdir($location, 0755);
     }
	  $location_upload .= $location."/".$time."-".$documenttype.".".$fileType;
	  //$location1 .= "/-".$documenttype.".".$fileType;
			//move_uploaded_file($filepath,$location);
			 file_put_contents($location_upload,$filepath);
			
			//move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetfolder_Appointment); 
		$statusMsg = $location_upload;		
	}else{ 
            $statusMsg = 'Invalid PDF File'; 
        }
		return $statusMsg;
}
function imageupload($filename,$ticket_id,$time,$documenttype,$filepath){
	//$imageUploadPath1 = "documents/raiseticketattach/".$time."-".$dependent_name."-".$documenttype.".".$fileType;;
	//$uploadPath = "documents/raiseticketattach/";
	//fetched filename
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
	//check file extension jpg,png
			$allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
        if(in_array($fileType, $allowTypes)){ 
            // Image temp source 
            // $imageTemp = $_FILES["fileToUpload"]["tmp_name"]; 
             
            // Compress size and upload image 
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	   //$imageUploadPath .= $uploadPath."/-".$documenttype.".".$fileType;
	   $imageUploadPath1 .= "documents/raiseticketattachment/".$time."-".$ticket_id."-".$documenttype.".".$fileType;
	   file_put_contents($imageUploadPath1,$filepath);
		
$statusMsg = $imageUploadPath1;		
        }else{ 
            $statusMsg = 'Invalid Image file'; 
        }
		return $statusMsg;
}
echo json_encode($response,true);
unset($pdo);
?>