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
$content_text = $data->content_text;
$imageattach = $data->imageattach;
$imageattachfilename=$data->imageattachfilename;
$decodedimageattachimage=base64_decode($imageattach);

$response = array();
try{

 if(!empty($accesskey)){
$accesscheck ="SELECT `userid`, `username`, `cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	
	
	
	  $result2 = $pdoread -> prepare("SELECT Concat('ART',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`unique_id`),'ART23120000'),Concat('ART',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS 'unique_id'  FROM `my_articles` where `unique_id` like concat('%ART',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
	  
					$result2->execute();
                        $data=$result2->fetch(PDO::FETCH_ASSOC);
					$id=$data['unique_id'];
	
	
	
   
$time=date("Y-m-d-H-i-s");		
	if(!empty($imageattachfilename)){ 
	
		 $documenttype = 'imageattach';
		$filename = $imageattachfilename;
		 $fileType = pathinfo($imageattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($imageattachfilename);
		$filesize = decodedimageattachimage["size"];
		$filepath = decodedimageattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$imageattach = pdfupload($filename,$filesize,$id,$time,$documenttype,$decodedimageattachimage);
			
		}else 	if( $fileType=='mp4'){
            $imageattach = videoupload($filename,$id,$time,$documenttype,$decodedimageattachimage);
			
		 } else {
			
				$imageattach = imageupload($filename,$id,$time,$documenttype,$decodedimageattachimage);
			 
		 }	
		
		}else{ 
        $imageattach = 'Please select an image file to upload.'; 
    } 
				
	
     $result = $pdo4 -> prepare("INSERT INTO `my_articles`(`date`,`unit`,`content_text`, `image`, `imagefilename`,`unique_id`, `created_on`, `created_by`, `authorized_name`, `status`) VALUES (CURRENT_TIMESTAMP,:unit ,:content_text, :imageattach, :imageattachfilename,:unique_id, CURRENT_TIMESTAMP, :created_by, :authorized_name,'Active')");

    
    $result->bindParam(':unit', $row['cost_center'], PDO::PARAM_STR);
    $result->bindParam(':content_text', $content_text, PDO::PARAM_STR);
    $result->bindParam(':unique_id', $id, PDO::PARAM_STR);
	$result->bindParam(':imageattach', $imageattach, PDO::PARAM_STR);
	$result->bindParam(':imageattachfilename', $imageattachfilename, PDO::PARAM_STR);
	$result->bindParam(':created_by', $row['userid'], PDO::PARAM_STR);
	$result->bindParam(':authorized_name', $row['username'], PDO::PARAM_STR);
	$result-> execute();
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
			http_response_code(400);
		    $response['error'] = true;
			$response['message']="Access denied!";
		}  
}else{
http_response_code(400);
$response['error'] = true;
$response['message'] ="Sorry! Some details are missing";
}
}catch(PDOException $e){
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
function pdfupload($filename,$filesize,$id,$time,$documenttype,$filepath){
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
if ($filesize < 500000)//500000 = 500kb
    {
		
       //$location1 = "documents/images/".$dependent_name;
       $location = "imageattach/".$id;
	 
    
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
function imageupload($filename,$id,$time,$documenttype,$filepath){
	
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 

			$allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
        if(in_array($fileType, $allowTypes)){ 
            
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	 
	   $imageUploadPath1 .= "imageattach/".$time."-".$id."-".$documenttype.".".$fileType;
	   file_put_contents($imageUploadPath1,$filepath);
		
$statusMsg =$imageUploadPath1;		
        }else{ 
            $statusMsg = 'Invalid image file'; 
        }
		return $statusMsg;
} 
function videoupload($filename,$id,$time,$documenttype,$filepath){
	
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 

			$allowTypes = array('mp4'); 
        if(in_array($fileType, $allowTypes)){ 
            
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	 
	   $imageUploadPath1 .= "imageattach/".$time."-".$id."-".$documenttype.".".$fileType;
	   file_put_contents($imageUploadPath1,$filepath);
		
$statusMsg =$imageUploadPath1;		
        }else{ 
            $statusMsg = 'Invalid image file'; 
        }
		return $statusMsg;
} 
echo json_encode($response);
unset($pdo4);
unset($pdoread);

?>