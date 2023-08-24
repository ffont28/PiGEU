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
    <title>Inserimento nuovo utente</title>


  </head>
  <body>
  <!-- INIZIO NAVBAR -->
  <?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
  <!-- FINE NAVBAR -->

INSERIMENTO DI UN NUOVO CORSO DI LAUREA

<form method="post" >
    <div class="center">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Nome del corso di laurea</label>
          <input type="text" class="form-control"  id="nome" placeholder="inserisci il Nome del CdL" name="nome">
        </div>

        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">CODICE</label>
          <input type="text" class="form-control" id="codice" placeholder="inserisci il codice del CdL" name="codice">
        </div>
Tipo di CdL
        <select class="form-select" aria-label="Default select example" id="tipo" name="tipo">
                <!--  <option selected>Open this select menu</option> -->
                  <option value="triennale">triennale</option>
                  <option value="magistrale">magistrale</option>
                  <option value="magistrale a ciclo unico">magistrale a ciclo unico</option>
                </select>


  <input type="submit" class="button1 green" value="INSERISCI CORSO DI LAUREA" />
    </div>
</form>

<?php
 if($_SERVER['REQUEST_METHOD']=='POST'){

    $db = open_pg_connection();

    // definisco le variabili
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $codice = $_POST['codice'];
    $docenteresponsabile = $_POST['docenteresponsabile'];

    // inserimento del corso in corso_di_laurea
    $params = array ($codice, $nome ,$tipo);
    $sql = "INSERT INTO corso_di_laurea VALUES ($1,$2,$3)";
    $result = pg_prepare($db,'insCdL',$sql);
    $result = pg_execute($db,'insCdL', $params);

    pg_close($db);
}

 ?>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>