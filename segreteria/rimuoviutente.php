<?php
session_start();
include('../functions.php');
include('../conf.php');
controller("segreteria", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
  <head>
    <?php importVari();?>
    <title>Rimozione utente</title>
  </head>
  <body>
  <!-- INIZIO NAVBAR -->
  <?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
  <!-- FINE NAVBAR -->
 <h1> PAGINA DI RIMOZIONE UTENZA </h1>

<form method="post" >
    <div class="center bred">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Nome Utente da rimuovere</label>
          <input type="text" class="form-control" id="nome" placeholder="inserisci l'ID utente che si vuole rimuovere" name="utente">

        </div>


  <input type="submit" class="button1 red" value="RIMUOVI UTENTE" />


    </div>
</form>

<?php
 if($_SERVER['REQUEST_METHOD']=='POST'){  //if(isset($_POST)){

    include_once('../functions.php');
    $db = open_pg_connection();

    //inserimento generale a livello di utente sia che sia docente, studente o segreteria
    $params = array ($_POST['utente']);
    $sql = "DELETE FROM utente WHERE email = $1";
    $result = pg_prepare($db,'rem',$sql);
    $result = pg_execute($db,'rem', $params);
}
 ?>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>