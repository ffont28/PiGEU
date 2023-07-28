<?php

function open_pg_connection(){
//$connection= "host=".host." port= 5432 dbname=".dbname." user=".user." password=".password;
include_once('conf.php');
$connection= "host=".myhost." dbname=".mydbname." user=".myuser." password=".mypassword;
//$connection_string= "host= localhost dbname= pigeu user= fontanaf password= font";
return pg_connect($connection);
}

$f_test= "function_test";
?>