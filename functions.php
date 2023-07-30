<?php

function open_pg_connection(){
include_once('conf.php');
$connection= "host=".myhost." dbname=".mydbname." user=".myuser." password=".mypassword;
return pg_connect($connection);
}

function check_login($user, $password){
session_start();
$_SESSION["username"] = $user;
$_SESSION["password"] = $password;
$params = array ($user, $password);

//echo "stai verificando con user= ".$user." e passwd= ".md5($password);

$db = open_pg_connection();

$sql_s = "SELECT *
         FROM segreteria s INNER JOIN credenziali c ON s.utente=c.username AND
         s.utente = $1 AND c.password = $2";

$result_s = pg_prepare($db,'dis',$sql_s);
$result_s = pg_execute($db, 'dis', $params);

    if ($row = pg_fetch_assoc($result_s)){

        header("Location: segreteria.php");
        exit();
        //echo "sono qui";
        return "UTENTE AUTENTICATO come ".$row['livello']; //$row['email'];
        } else {
        header("Location: 404.php");
        return "ci hai provato!";
        }

}


?>