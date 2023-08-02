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

function inserisciStudente($arg){
    $db = open_pg_connection();
    $sql = "INSERT INTO studente (utente, corso_di_laurea) VALUES ($1,$2)";
    $result = pg_prepare($db,'insstud',$sql);
    $result = pg_execute($db,'insstud', $arg);
}

function inserisciDocente($arg){
    $db = open_pg_connection();
    $sql = "INSERT INTO docente VALUES ($1, $2)";
    $result = pg_prepare($db,'insdoc',$sql);
    $result = pg_execute($db,'insdoc', $arg);
}

function inserisciSegreteria($arg){
    $db = open_pg_connection();
    $sql = "INSERT INTO segreteria VALUES ($1, $2)";
    $result = pg_prepare($db,'insseg',$sql);
    $result = pg_execute($db,'insseg', $arg);
}


function docentiCandidatiResponsabili(){
   include('../conf.php');
   try {
       // Connessione al database utilizzando PDO
       $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

       // Query con CTE
       $query = "
           WITH selezione AS (
                                  SELECT utente FROM docente
                                  EXCEPT
                                  SELECT docente FROM docente_responsabile
                                  GROUP BY 1
                                  HAVING count(*) >2
                                  )
                                  SELECT u.nome, u.cognome FROM utente u
                                  INNER JOIN selezione s ON u.email = s.utente
       ";

       // Esecuzione della query e recupero dei risultati
       $stmt = $conn->query($query);
       $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

  //     $c = 0;
  //     $risultato = array();

       foreach ($results as $row) {
               // Utilizza $row per accedere ai dati dei singoli record
               echo $row['nome'] . ' - ' . $row['cognome'] . '<br>';
             //  $risultato[$c] = $row['nome'] . ' ' . $row['cognome'];
             }
   } catch (PDOException $e) {
       echo "Errore: " . $e->getMessage();
   }

   // return $risultato;

}


?>