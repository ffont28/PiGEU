<?php
session_start();
include('conf.php');
function open_pg_connection(){
    include_once('conf.php');
    $connection= "host=".myhost." dbname=".mydbname." user=".myuser." password=".mypassword;
    return pg_connect($connection);
}

function check_login($user, $password){

    $_SESSION["username"] = $user;
    $_SESSION["password"] = $password;
    $params = array ($user, $password);

    //echo "stai verificando con user= ".$user." e passwd= ".md5($password);

    $db = open_pg_connection();
    // anzitutto verifico che esistano le credenziali nel DB
    $sql = "SELECT * FROM credenziali WHERE username = $1 AND password = $2";
    $result = pg_query_params($db, $sql, $params);

    if (pg_num_rows($result) > 0) {
        $param = array ($user);
        //caso in cui è SEGRETERIA
        $sql = "SELECT * FROM segreteria WHERE utente = $1";
        $result = pg_query_params($db, $sql, $param);
        if (pg_num_rows($result) > 0) {
            $_SESSION["tipo"] = "segreteria";
            header("Location: segreteria.php");
            exit();
        }
        //caso in cui è DOCENTE
        $sql = "SELECT * FROM docente WHERE utente = $1";
        $result = pg_query_params($db, $sql, $param);
        if (pg_num_rows($result) > 0) {
            $_SESSION["tipo"] = "docente";
            header("Location: docente/main.php");
        }
        //caso in cui è STUDENTE
        $sql = "SELECT * FROM studente WHERE utente = $1";
        $result = pg_query_params($db, $sql, $param);
        if (pg_num_rows($result) > 0) {
            $_SESSION["tipo"] = "studente";
            header("Location: studente/main.php");
        }

        //prelevo il NOME e il COGNOME dell'utente
        $sql = "SELECT nome, cognome FROM utente WHERE email = $1";
        $result = pg_query_params($db, $sql, $param);
        if ($row = pg_fetch_assoc($result)) {
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['cognome'] = $row['cognome'];
        }

    } else {
        header("Location: 404.php");
        return "ci hai provato!";
    }
    echo "<script>console.log('Debug Objects: " . $_SESSION['nome'] . " sono qui2 ' );</script>";


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

function logout(){
    $_SESSION["username"] = " ";
    $_SESSION["password"] = " ";
}

function controller($tipo, $username, $password){
    if(!isset($username)){
        header("Location: ../index.php");
    }

    $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = "SELECT * FROM ". $tipo ." t INNER JOIN credenziali c ON t.utente = c.username
               WHERE t.utente = :u AND c.password = :p";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':u', $username);
    $stmt->bindParam(':p', $password);
    $stmt->execute();

    if ($stmt->rowCount() == 0){
        echo "utente non autorizzato con le credenziali di " . $_SESSION['username']. " | " . $_SESSION['password'];
        die();
    }
}

?>