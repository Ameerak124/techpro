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
$sno = $data->sno;
$no_of_key = $data->no_of_key;
$deposited_by = $data->deposited_by;
$signatureattach = $data->signatureattach;
$signatureattachfilename=$data->signatureattachfilename;
$decodedsignatureattachimage=base64_decode($signatureattach);
$securitysignatureattach = $data->securitysignatureattach;
$securitysignatureattachfilename=$data->securitysignatureattachfilename;
$decodedsecuritysignatureattachimage=base64_decode($securitysignatureattach);
$response = array();
try{
 if(!empty($accesskey) && !empty($sno) && !empty($no_of_key) && !empty($deposited_by)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	$result2 = $pdo1 -> prepare("SELECT  `id`,department,taken_by_userid,key_no,keys_return,no_of_key  as noofkeys,(keys_return + :no_of_key ) as totalkeys FROM `key_reg` WHERE `sno`=:sno");
	$result2->bindParam(':sno', $sno, PDO::PARAM_STR);
	$result2->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result2-> execute();
     $data = $result2->fetch(PDO::FETCH_ASSOC);
	$keyreg_id=$data['id'];
	$noofkeys=$data['noofkeys'];
	$totalkeys=$data['totalkeys'];
	$time=date("Y-m-d-H-i-s");					
	if(!empty($signatureattachfilename)){ 
		 $documenttype = 'signatureattach';
		$filename = $signatureattachfilename;
		 $fileType = pathinfo($signatureattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($signatureattachfilename);
		$filesize = decodedsignatureattachimage["size"];
		$filepath = decodedsignatureattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
		$signatureattach = pdfupload($filename,$filesize,$keyreg_id,$time,$documenttype,$decodedsignatureattachimage);
		 } else {
			
		$signatureattach = imageupload($filename,$keyreg_id,$time,$documenttype,$decodedsignatureattachimage);
			 
		 }	
		
		}else{ 
        $signatureattach = 'Please select an image file to upload.'; 
    } 
		$time=date("Y-m-d-H-i-s");	
		if(!empty($securitysignatureattachfilename)){ 
        
		 $documenttype = 'securitysignatureattach';
		$filename = $securitysignatureattachfilename;
		 $fileType = pathinfo($securitysignatureattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($securitysignatureattachfilename);
		$filesize = decodedsecuritysignatureattachimage["size"];
		$filepath = decodedsecuritysignatureattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$securitysignatureattach = pdfupload($filename,$filesize,$keyreg_id,$time,$documenttype,$decodedsecuritysignatureattachimage);
		 } else {
			
		$securitysignatureattach = imageupload($filename,$keyreg_id,$time,$documenttype,$decodedsecuritysignatureattachimage);
			 
		 }	
		
		}else{ 
        $securitysignatureattach = 'Please select an image file to upload.'; 
    }
	
	if($totalkeys > $noofkeys){
		 http_response_code(503);
       $response['error'] = true;
       $response['message']="Please Enter proper keys";
	}else{
    if($totalkeys == $noofkeys){
     $result = $pdo1 -> prepare("UPDATE `key_reg` SET `keys_return`=(keys_return + :no_of_key),`in_time`=CURRENT_TIME,`deposit_signature`=:signatureattach,`deposit_security_signature`=:securitysignatureattach,`deposited_by`=:deposited_by,`modified_by`=:userid,`modified_on`=CURRENT_TIMESTAMP,`status`='Entered' WHERE `sno`=:sno and `status`='Exit'");
	$result->bindParam(':sno', $sno, PDO::PARAM_STR);
	$result->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
	$result->bindParam(':securitysignatureattach', $securitysignatureattach, PDO::PARAM_STR);
	$result->bindParam(':deposited_by', $deposited_by, PDO::PARAM_STR);
	$result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();
     // if($result->rowCount()>0){ 



$result1 =$pdo1-> prepare("INSERT INTO `key_submit`(`id`, `department`, `key_no`, `no_of_key`, `in_time`,`created_by`, `created_on`, `status`,deposit_signature,deposit_security_signature,deposited_by) VALUES(:id,:department,:key_no,:no_of_key,CURRENT_TIME,:userid,CURRENT_TIMESTAMP,'Pending',:signatureattach,:securitysignatureattach,:deposited_by)");
	$result1->bindParam(':id', $keyreg_id, PDO::PARAM_STR);
	$result1->bindParam(':department', $data['department'], PDO::PARAM_STR);
	$result1->bindParam(':key_no',$data['key_no'], PDO::PARAM_STR);
	$result1->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
	$result1->bindParam(':securitysignatureattach', $securitysignatureattach, PDO::PARAM_STR);
	$result1->bindParam(':deposited_by', $deposited_by, PDO::PARAM_STR);
	$result1-> execute();
	}else{
		
	 $result = $pdo1 -> prepare("UPDATE `key_reg` SET `keys_return`=(keys_return+:no_of_key),`in_time`=CURRENT_TIME,`deposit_signature`=:signatureattach,`deposit_security_signature`=:securitysignatureattach,`deposited_by`=:deposited_by,`modified_by`=:userid,`modified_on`=CURRENT_TIMESTAMP WHERE `sno`=:sno and `status`='Exit'");
	$result->bindParam(':sno', $sno, PDO::PARAM_STR);
	$result->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
	$result->bindParam(':securitysignatureattach', $securitysignatureattach, PDO::PARAM_STR);
	$result->bindParam(':deposited_by', $deposited_by, PDO::PARAM_STR);
	$result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();	
		
		
		
		
		
	$result1 =$pdo1-> prepare("INSERT INTO `key_submit`(`id`, `department`, `key_no`, `no_of_key`, `in_time`,`created_by`, `created_on`, `status`,deposit_signature,deposit_security_signature,deposited_by) VALUES(:id,:department,:key_no,:no_of_key,CURRENT_TIME,:userid,CURRENT_TIMESTAMP,'Pending',:signatureattach,:securitysignatureattach,:deposited_by)");
	$result1->bindParam(':id', $keyreg_id, PDO::PARAM_STR);
	$result1->bindParam(':department', $data['department'], PDO::PARAM_STR);
	$result1->bindParam(':key_no',$data['key_no'], PDO::PARAM_STR);
	$result1->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
	$result1->bindParam(':securitysignatureattach', $securitysignatureattach, PDO::PARAM_STR);
	$result1->bindParam(':deposited_by', $deposited_by, PDO::PARAM_STR);
	$result1-> execute();
	
	}
	if($result->rowCount()>0){

	$result2 = $pdo1 -> prepare("INSERT INTO `keyreg_log`(`id`, `department`, `taken_by_userid`,`key_no`, `no_of_key`, `deposited_by`, `created_by`, `created_on`, `status`) VALUES (:id,:department,:taken_by_userid,:key_no,:no_of_key,:deposited_by,:userid,CURRENT_TIMESTAMP,'Entered')");
	$result2->bindParam(':id', $keyreg_id, PDO::PARAM_STR);
	$result2->bindParam(':department', $data['department'], PDO::PARAM_STR);
	$result2->bindParam(':taken_by_userid', $data['taken_by_userid'], PDO::PARAM_STR);
	$result2->bindParam(':key_no',$data['key_no'], PDO::PARAM_STR);
	$result2->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result2->bindParam(':deposited_by', $deposited_by, PDO::PARAM_STR);
	$result2->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result2-> execute();
	   
    http_response_code(200);
    $response['error'] = false; 
    $response['message']= "Data updated";
    }else{
       http_response_code(503);
       $response['error'] = true;
       $response['message']="Data not updated";
     }
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
function pdfupload($filename,$filesize,$keyreg_id,$time,$documenttype,$filepath){
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
if ($filesize < 500000)//500000 = 500kb
    {
		
       //$location1 = "documents/images/".$dependent_name;
       $location = "documents/images/".$keyreg_id;
	 
    
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
function imageupload($filename,$keyreg_id,$time,$documenttype,$filepath){
	
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 

			$allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
        if(in_array($fileType, $allowTypes)){ 
            
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	   $imageUploadPath1 .= "documents/images/".$time."-".$keyreg_id."-".$documenttype.".".$fileType;
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