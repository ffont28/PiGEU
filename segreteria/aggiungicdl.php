<!doctype html>
<html lang="en" data-bs-theme="auto">
  <head>
<!-- import di Bootstrap-->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


<script src="../js/segreteria.js"></script>
<link rel="stylesheet" href="../css/cssSegreteria.css">
<link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Inserimento nuovo utente</title>


  </head>
  <body>
  <!-- INIZIO NAVBAR  -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" aria-current="page" href="../segreteria.php">Homepage</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/segreteria/aggiungiutente.php">Aggiungi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="#">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>
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

    include('../functions.php');
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

}
 ?>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>