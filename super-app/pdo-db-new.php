<?PHP
$servername = "3.6.65.78";
$servername1 = "65.2.7.174";
$servername2 = "13.235.101.8";
$username = "hims";
$password = "QI7TQ2Ga1ZoVZ0-[";
$database = "hims";
$baseurl = "http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/super-app/";
$username1 = "root";
$username2 = "remote";
$password1 = "Dearsuperman16@";
$database1 = "i_assist";
$database2 = "po_module";
$database3 = "purchase_indent";
$password2 = "daffyduck28@";
$servername5 = "krkdbmed.c9g5xuzwcfdr.ap-south-1.rds.amazonaws.com";
$username5 = "root";
$password5 = "powerpopgirls1@";
$database5 = "mhcpanel_mis";
$servername6 = "med-techpro-qa-aps1-db.cluster-c7qsruv54dfl.ap-south-1.rds.amazonaws.com";
$servernameread = "med-techpro-qa-aps1-db.cluster-ro-c7qsruv54dfl.ap-south-1.rds.amazonaws.com";
$username6 = "hims";
$password6 = "QI7TQ2Ga1ZoVZ0-[";
$database6 = "hims";
$servername_hrms = "3.7.119.125";
$username_hrms = "mobi_app_hr";
$password_hrms = "mobihuman98@";
$database_hrms = "hrms";

/* $servername_himsdemo = "65.1.244.68";
$username_himsdemo = "svshyduser";
$password_himsdemo = "!L8AYc/K@BGdAXSB";
$database_himsdemo = "hims-demo";
 */
/* $servername_newhrms = "139.59.44.155";
$username_newhrms = "pingpong";
$password_newhrms = "Insource@med098";
$database_newhrms = "hrms"; */
//$servername7 = "13.232.176.192";






try{
	/* $himsdemo = new PDO("mysql:host=" . $servername_himsdemo . ";dbname=" . $database_himsdemo.";charset=utf8", $username_himsdemo, $password_himsdemo);
  
    $himsdemo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); */
	
   /*  $pdo = new PDO("mysql:host=" . $servername . ";dbname=" . $database.";charset=utf8", $username, $password);
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); */
	
	$pdo1 = new PDO("mysql:host=" . $servername1 . ";dbname=" . $database1.";charset=utf8", $username2, $password1);
  
   $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	 $pdo2 = new PDO("mysql:host=" . $servername2 . ";dbname=" . $database2.";charset=utf8", $username2, $password2);
  
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$pdo3 = new PDO("mysql:host=" . $servername2 . ";dbname=" . $database3.";charset=utf8", $username2, $password2);
  
    $pdo3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
	
    $con = new PDO("mysql:host=$servername5;dbname=$database5;charset=utf8", $username5, $password5);

    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	
	$pdo4 = new PDO("mysql:host=" . $servername6 . ";dbname=" . $database6.";charset=utf8", $username6, $password6);
  
    $pdo4->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$pdoread = new PDO("mysql:host=" . $servernameread . ";dbname=" . $database6.";charset=utf8", $username6, $password6);
  
    $pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$pdo_hrms = new PDO("mysql:host=" . $servername_hrms . ";dbname=" . $database_hrms.";charset=utf8", $username_hrms, $password_hrms);
  
    $pdo_hrms->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	/* $pdo5 = new PDO("mysql:host=" . $servername7 . ";dbname=" . $database6.";charset=utf8", $username6, $password6);
  
    $pdo5->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); */
	
	
		
	/* $pdo_newhrms = new PDO("mysql:host=" . $servername_newhrms . ";dbname=" . $database_newhrms.";charset=utf8", $username_newhrms, $password_newhrms);
  
    $pdo_newhrms->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); */
	
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e);
}
?>