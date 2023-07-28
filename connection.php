<?php
define ("host","localhost");
define ("dbname","pigeu");
define ("user","fontanaf");
define ("password","font");
$test = "test";
function open_pg_connection(){
//$connection= "host=".host." port= 5432 dbname=".dbname." user=".user." password=".password;
$connection= "host=".host." dbname=".dbname." user=".user;
//$connection_string= "host= localhost dbname= pigeu user= fontanaf password= font";
return pg_connect($connection);
}
?>