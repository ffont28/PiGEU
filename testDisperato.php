<?php
/*
define ("myhost","localhost");
define ("mydbname","pigeu");
define ("myuser","fontanaf");
define ("mypassword","font");
$test = "test";
*/
include('conf.php');
include('functions.php');

//$connection= "host=".myhost." dbname=".mydbname." user=".myuser." password=".mypassword;


//$db = pg_connect($connection);

$db =

$sql = "SELECT * FROM utente";
                            $result = pg_query($db,$sql);

                            while ($row = pg_fetch_assoc($result)){
                                print_r($row);
                            }


?>