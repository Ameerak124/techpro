
<?php

define('DB_SERVER', 'krkdbmed.c7i4sc0w6dwr.ap-south-1.rds.amazonaws.com');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'powerpopgirls1@');
define('DB_NAME', 'mhcpanel_mis');


try{
    $pdo_mis = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME.";charset=utf8", DB_USERNAME, DB_PASSWORD);
  
    $pdo_mis->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>