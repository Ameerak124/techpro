<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db-new.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$name = $data->name;
$address = $data->address;
$phone_number = $data->phone_number;
$whoom_to_meet = $data->whoom_to_meet;
$purpose = $data->purpose;
$imageattach = $data->imageattach;
$imageattachfilename=$data->imageattachfilename;
$decodedimageattachimage=base64_decode($imageattach);
$signatureattach = $data->signatureattach;
$signatureattachfilename=$data->signatureattachfilename;
$decodedsignatureattachimage=base64_decode($signatureattach);
$response = array();
try{

 if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	
	
		$fetch = $pdo1 -> prepare("SELECT id, `name`, `created_on`, `out_time`,in_time, `status` FROM `visitor_pass` WHERE name=:name and purpose=:purpose and  status='Entered' and date(created_on)=CURRENT_DATE");
	$fetch->bindParam(':name', $name, PDO::PARAM_STR);
	$fetch->bindParam(':purpose', $purpose, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount() == 0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	

      $result2 = $pdo1 -> prepare("SELECT Concat('VIS',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`id`),'VIS23090000'),Concat('VIS',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS id FROM `visitor_pass` where id like concat('%VIS',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
					$result2->execute();
                        $data=$result2->fetch(PDO::FETCH_ASSOC);
					$visitor_id=$data['id'];

$time=date("Y-m-d-H-i-s");		
	if(!empty($imageattachfilename)){ 
        
		 $documenttype = 'imageattach';
		$filename = $imageattachfilename;
		 $fileType = pathinfo($imageattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($imageattachfilename);
		$filesize = decodedimageattachimage["size"];
		$filepath = decodedimageattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$imageattach = pdfupload($filename,$filesize,$visitor_id,$time,$documenttype,$decodedimageattachimage);
		 } else {
			
				$imageattach = imageupload($filename,$visitor_id,$time,$documenttype,$decodedimageattachimage);
			 
		 }	
		
		}else{ 
        $imageattach = 'Please select an image file to upload.'; 
    } 
				
	if(!empty($signatureattachfilename)){ 
        
		 $documenttype = 'signatureattach';
		$filename = $signatureattachfilename;
		 $fileType = pathinfo($signatureattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($signatureattachfilename);
		$filesize = decodedsignatureattachimage["size"];
		$filepath = decodedsignatureattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$signatureattach = pdfupload($filename,$filesize,$visitor_id,$time,$documenttype,$decodedsignatureattachimage);
		 } else {
			
				$signatureattach = imageupload($filename,$visitor_id,$time,$documenttype,$decodedsignatureattachimage);
			 
		 }	
		
		}else{ 
        $signatureattach = 'Please select an image file to upload.'; 
    } 

     $result = $pdo1 -> prepare("INSERT  ignore INTO `visitor_pass`(id,`name`, `address`, `phone_number`, `whoom_to_meet`, `purpose`, `in_time`, `signature`, `photo`, `created_by`, `created_on`, `status`) VALUES (:id,:name,:address,:phone_number,:whoom_to_meet,:purpose,CURRENT_TIME,:signatureattach,:imageattach,:userid,CURRENT_TIMESTAMP,'Entered')");
	$result->bindParam(':id', $visitor_id, PDO::PARAM_STR);
	$result->bindParam(':name', $name, PDO::PARAM_STR);
	$result->bindParam(':address', $address, PDO::PARAM_STR);
	$result->bindParam(':phone_number', $phone_number, PDO::PARAM_STR);
	$result->bindParam(':whoom_to_meet', $whoom_to_meet, PDO::PARAM_STR);
	$result->bindParam(':purpose', $purpose, PDO::PARAM_STR);
	$result->bindParam(':imageattach', $imageattach, PDO::PARAM_STR);
	$result->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
    $result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();

     $result1 = $pdo1 -> prepare("INSERT INTO `visitor_pass_logs`(`id`,`name`, `time`, `created_by`, `created_on`,`status`) VALUES (:id,:name,CURRENT_TIME,:userid,CURRENT_TIMESTAMP,'Entered')");
	$result1->bindParam(':id', $visitor_id, PDO::PARAM_STR);
	$result1->bindParam(':name', $name, PDO::PARAM_STR);
	//$result1->bindParam(':time', $time, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1-> execute();

	
   if($result->rowCount()>0){
	   
   http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data inserted";
	
    }else{
       http_response_code(503);
       $response['error'] = true;
       $response['message']="Data not inserted";
     }
	 
	 }else{
	 http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data already inserted";
	   }
	 
    	}else{
			http_response_code(400);
		    $response['error'] = true;
			$response['message']="Access denied!";
		}  
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
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
function pdfupload($filename,$filesize,$visitor_id,$time,$documenttype,$filepath){
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
if ($filesize < 500000)//500000 = 500kb
    {
		
       //$location1 = "documents/images/".$dependent_name;
       $location = "documents/images/".$visitor_id;
	 
    
			if(!is_dir($location)){
       mkdir($location, 0755);
     }
	  $location_upload .=  $location."/".$time."-".$documenttype.".".$fileType;
	 // $location1_file .=  $location."/".$time."-".$documenttype.".".$fileType;
			//move_uploaded_file($filepath,$location);
			 file_put_contents($location_upload,$filepath);
			
			//move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetfolder_Appointment); 
		$statusMsg = $location_upload;		
	}else{ 
            $statusMsg = 'Invalid PDF File'; 
        }
		return $statusMsg;
}
function imageupload($filename,$visitor_id,$time,$documenttype,$filepath){
	
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 

			$allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
        if(in_array($fileType, $allowTypes)){ 
            
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	 
	   $imageUploadPath1 .= "documents/images/".$time."-".$visitor_id."-".$documenttype.".".$fileType;
	   file_put_contents($imageUploadPath1,$filepath);
		
$statusMsg =$imageUploadPath1;		
        }else{ 
            $statusMsg = 'Invalid image file'; 
        }
		return $statusMsg;
} 
echo json_encode($response,true);
unset($pdoread);
unset($pdo1);

?>