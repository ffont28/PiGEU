<?php
session_start();
include('conf.php');
// HO RIMOSSO una start e end script che causava errori
function importVari(){
?>          <!-- import di Bootstrap-->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
            
            <!-- Includi jQuery dalla rete tramite un link CDN -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            
            <link rel="stylesheet" href="/css/cssSegreteria.css">
            <link rel="stylesheet" href="/css/from-re.css">
            <link rel="stylesheet" href="/css/calendarioesami.css">
            <link rel="stylesheet" href="/css/general.css">
            <script src="../js/segreteria.js"></script>
            <script src="../js/general.js"></script>
            
             <meta charset="utf-8">
<?php
}

function importVariPerHomePage(){
?>          <!-- import di Bootstrap-->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
            
            <!-- Includi jQuery dalla rete tramite un link CDN -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            
            <link rel="stylesheet" href="/css/cssSegreteria.css">
            <link rel="stylesheet" href="/css/from-re.css">
            <link rel="stylesheet" href="/css/calendarioesami.css">
            <link rel="stylesheet" href="/css/general.css">
            <script src="/js/segreteria.js"></script>
            <script src="/js/general.js"></script>
            
             <meta charset="utf-8">
<?php
}
function setNavbarSegreteria($link){
    $active = "active"; $disabled = "disabled";
    $h_active = $gu_active = $gi_active = $gc_active = $ce_active ="";
    $h_disab = $au_disab = $ai_disab = $ac_disab = $mu_disab = $mi_disab =
               $mc_disab = $ru_disab = $ri_disab = $rc_disab = $cv_disab = $cc_disab ="";
    if ($link == "/segreteria/main.php") {$h_active = $active; $h_disab = $disabled;}
    if ($link == "/segreteria/aggiungiutente.php") {$gu_active = $active; $au_disab = $disabled;}
    if ($link == "/segreteria/gestisciutente.php") {$gu_active = $active; $mu_disab = $disabled;}
    if ($link == "/segreteria/rimuoviutente.php") {$gu_active = $active; $ru_disab = $disabled;}
    if ($link == "/segreteria/aggiungiinsegnamento.php") {$gi_active = $active; $ai_disab = $disabled;}
    if ($link == "/segreteria/modificainsegnamento.php") {$gi_active = $active; $mi_disab = $disabled;}
    if ($link == "/segreteria/rimuoviinsegnamento.php") {$gi_active = $active; $ri_disab = $disabled;}
    if ($link == "/segreteria/aggiungicdl.php") {$gc_active = $active; $ac_disab = $disabled;}
    if ($link == "/segreteria/modificacdl.php") {$gc_active = $active; $mc_disab = $disabled;}
    if ($link == "/segreteria/rimuovicdl.php") {$gc_active = $active; $rc_disab = $disabled;}
    if ($link == "/segreteria/generacarrieravalida.php") {$ce_active = $active; $cv_disab = $disabled;}
    if ($link == "/segreteria/generacarrieracompleta.php") {$ce_active = $active; $cc_disab = $disabled;}
?>
    <!-- INIZIO NAVBAR -->
    <div class="container">
          <ul class="nav nav-tabs">

            <li class="nav-item">
              <a class="nav-link <?php echo $h_active.' '.$h_disab ?>" aria-current="page" href="main.php"><strong>🏠 HOME</strong></a>
            </li>
            <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle <?php echo $gu_active ?>" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                      <strong>🧑‍🔧 GESTIONE UTENZE</strong>
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                        <li><a class="dropdown-item <?php echo $au_disab ?>" href="aggiungiutente.php">Inserimento Utente</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo $mu_disab ?>" href="gestisciutente.php">Modifica Utente</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo $ru_disab ?>" href="rimuoviutente.php">Rimozione Utente</a></li>
                      </ul>
            </li>
            <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle <?php echo $gi_active ?>" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                      <strong>🧑‍🏫 GESTIONE INSEGNAMENTI</strong>
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                        <li><a class="dropdown-item <?php echo $ai_disab ?>" href="aggiungiinsegnamento.php">Inserimento Insegnamento</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo $mi_disab ?>" href="modificainsegnamento.php">Modifica Insegnamento</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo $ri_disab ?>" href="rimuoviinsegnamento.php">Rimozione Insegnamento</a></li>
                      </ul>
            </li>
            <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle <?php echo $gc_active ?>" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                      <strong>🎓 GESTIONE CORSI DI LAUREA</strong>
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                        <li><a class="dropdown-item <?php echo $ac_disab ?>" href="aggiungicdl.php">Inserimento Corso di Laurea</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo $mc_disab ?>" href="modificacdl.php">Modifica Corso di Laurea</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item <?php echo $rc_disab ?>" href="rimuovicdl.php">RimozioneCorso di Laurea</a></li>
                      </ul>
            </li>
            <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle <?php echo $ce_active ?>" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                      <strong>📄 CERTIFICAZIONI</strong>
                      </a>
                      <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                        <li><a class="dropdown-item <?php echo $cv_disab ?>" href="generacarriera.php">Genera certificato di carriera</a></li>
                      </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link disabled" aria-current="page" href="main.php">👤 <?php echo $_SESSION['cognome'].'  '.$_SESSION['nome'] ?></a>
            </li>
            <li class="nav-item dropdown">
            <div class="ml-auto logout-button">
                <a class="nav-link rounded-pill btn btn-danger" id="navbarDropdown" role="button" 
                   href="../logout.php"><strong>🚪 LOGOUT</strong></a>
            </div>
            </li>
          </ul>
    
          </div>
          <!-- FINE NAVBAR -->
<?php
}

function setNavbarStudente($link){
    $active = "active"; $disabled = "disabled";
    $h_active = $ie_active = $ca_active = $in_active = "";
    $h_disab = $ie_disab = $cv_disab = $cc_disab = $in_disab ="";
    if ($link == "/studente/main.php") {$h_active = $active; $h_disab = $disabled;}
    if ($link == "/studente/iscrizioneEsame.php") {$ie_active = $active; $ie_disab = $disabled;}
    if ($link == "/studente/infoCdL.php") {$in_active = $active; $in_disab = $disabled;}
    if ($link == "/studente/carrieravalida.php") {$ca_active = $active; $cv_disab = $disabled;}
    if ($link == "/studente/carrieracompleta.php") {$ca_active = $active; $cc_disab = $disabled;}
?>
    <!-- INIZIO NAVBAR -->
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link <?php echo $h_active.' '.$h_disab ?>" aria-current="page" href="main.php"><strong>🏠 HOME</strong></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $ie_active. ' '.$ie_disab ?>" href="iscrizioneEsame.php" role="button" aria-expanded="true">
                <strong>🧑‍🔧 ISCRIZIONE ESAMI</strong>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link dropdown-toggle<?php echo $ca_active ?>"  id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                <strong>🧑‍🏫 LA TUA CARRIERA</strong>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown" data-bs-popper="none">
                    <li><a class="dropdown-item <?php echo $cv_disab ?>" href="carrieravalida.php" >Carriera Valida</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item <?php echo $cc_disab ?>" href="carrieracompleta.php" >Carriera Completa</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $in_active. ' '.$in_disab ?>" href="infoCdL.php" role="button" aria-expanded="true">
                <strong>🎓 INFORMAZIONI SUI CORSI DI LAUREA</strong>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" aria-current="page" href="main.php">👤 <?php echo $_SESSION['cognome'].'  '.$_SESSION['nome'] ?></a>
            </li>
            <li class="nav-item dropdown">
                <div class="ml-auto logout-button">
                <a class="nav-link rounded-pill btn btn-danger" id="navbarDropdown" role="button"
                   href="../logout.php"><strong>🚪 LOGOUT</strong></a>
                </div>
            </li>
        </ul>
    </div>
    <!-- FINE NAVBAR -->
<?php
}

function setNavbarDocente($link){
    $active = "active"; $disabled = "disabled";
    $h_active = $ce_active = $ve_active  = "";
    $h_disab = $ce_disab = $ve_disab = "";
    if ($link == "/docente/main.php") {$h_active = $active; $h_disab = $disabled;}
    if ($link == "/docente/calendarioEsami.php") {$ce_active = $active; $ce_disab = $disabled;}
    if ($link == "/docente/verbalizzazione.php") {$ve_active = $active; $ve_disab = $disabled;}
?>
    <!-- INIZIO NAVBAR -->
    <div class="container">
        <ul class="nav nav-tabs">

            <li class="nav-item">
                <a class="nav-link <?php echo $h_active.' '.$h_disab ?>" aria-current="page" href="main.php"><strong>🏠 HOME</strong></a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $ce_active. ' '.$ce_disab ?>" href="calendarioEsami.php" role="button" aria-expanded="true">
                    <strong>🧑‍🔧 GESTIONE CALENDARIO ESAMI</strong>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo $ve_active. ' '.$ve_disab ?>" href="verbalizzazione.php"  role="button" aria-expanded="true">
                <strong>🧑‍🏫 VERBALIZZAZIONE APPELLI D\'ESAME</strong>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" aria-current="page" href="main.php">👤 <?php echo $_SESSION['cognome'].'  '.$_SESSION['nome'] ?></a>
            </li>
            <li class="nav-item dropdown">
                <div class="ml-auto logout-button">
                    <a class="nav-link rounded-pill btn btn-danger" id="navbarDropdown" role="button"
                        href="../logout.php"><strong>🚪 LOGOUT</strong></a>
                </div>
            </li>
        </ul>
    </div>
    <!-- FINE NAVBAR -->
<?php
}


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
        $emailpersonale = "";

        //prelevo il NOME e il COGNOME dell'utente
        $sql = "SELECT nome, cognome, emailpersonale FROM utente WHERE email = $1";
        $result = pg_query_params($db, $sql, $param);
        if ($row = pg_fetch_assoc($result)) {
            $_SESSION['nome'] = $row['nome'];
            $_SESSION['cognome'] = $row['cognome'];
            $emailpersonale = $row['emailpersonale'];
        }

        // caso in cui è il primo accesso dell'utente, deve modificare la password
        if (md5($emailpersonale) == $password){
            $_SESSION['firstaccess'] = "YES";
            header("Location: modificaPassword.php");
            exit();
        }

        //caso in cui è SEGRETERIA
        $sql = "SELECT * FROM segreteria WHERE utente = $1";
        $result = pg_query_params($db, $sql, $param);
        if (pg_num_rows($result) > 0) {
            $_SESSION["tipo"] = "segreteria";
            header("Location: segreteria/main.php");
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



    } else {
        header("Location: 404.php");
        return "non sei né SEGRETERIA né STUDENTE né DOCENTE!";
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

    if ($tipo != "studente" && $tipo != "segreteria" && $tipo != "docente"){
        error_log("TIPO UTENZA NON VALIDO");
        echo "<script>alert('NON VALIDO');</script>";
        header("Location: ../logout.php");
        die();
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
        header("Location: ../logout.php");
        echo "utente non autorizzato con le credenziali di " . $_SESSION['username']. " | " . $_SESSION['password'];
        die();
    }
}

?>