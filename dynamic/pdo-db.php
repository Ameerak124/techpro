<?PHP
$servernameread = "med-techpro-qa-aps1-db.cluster-ro-c7qsruv54dfl.ap-south-1.rds.amazonaws.com";
$servername = "med-techpro-qa-aps1-db.cluster-c7qsruv54dfl.ap-south-1.rds.amazonaws.com";
$username = "hims";
$password = "QI7TQ2Ga1ZoVZ0-[";
$database = "hims";
$baseurl = "http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/dynamic/";
try{
    $pdoread = new PDO("mysql:host=" . $servernameread . ";dbname=" . $database.";charset=utf8", $username, $password);
  
    $pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$pdo4 = new PDO("mysql:host=" . $servername . ";dbname=" . $database.";charset=utf8", $username, $password);
  
    $pdo4->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>