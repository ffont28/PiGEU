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
      <a class="nav-link active" href="/segreteria/aggiungiutente.php">Aggiungi Utenza</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="#">Inserisci corso di laurea</a>
    </li>
    <li class="nav-item">
      <a class="nav-link disabled" aria-disabled="true">Modifica Corso di Laurea</a>
    </li>
  </ul>
  <!-- FINE NAVBAR -->
  PAGINA DI NUOVA UTENZA

<form action="/action_page.php">
    <div class="center">
        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Nome</label>
          <input type="text" class="form-control" onchange="computeEmailUser()" id="nome" placeholder="inserisci il Nome dell'utente" name="nome">
        </div>

        <div class="mb-3">
          <label for="exampleFormControlInput1" class="form-label">Cognome</label>
          <input type="text" class="form-control" onchange="computeEmailUser()" id="cognome" placeholder="inserisci il Cognome dell'utente" name="cognome">
        </div>

        <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">CODICE FISCALE</label>
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="inserisci il Codice Fiscale dell'utente" name="codicefiscale">
            </div>

        <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">indirizzo</label>
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="inserisci l'indirizzo dell'utente" name="indirizzo">
            </div>

        <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">città</label>
              <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="inserisci la città di residenza dell'utente" name="citta">
            </div>
        Seleziona un'utenza
        <select class="form-select" onchange="computeEmailDomain()"  aria-label="Default select example" id="tipo" name="tipo">
        <!--  <option selected>Open this select menu</option> -->
          <option value="Studente">Studente</option>
          <option value="Docente">Docente</option>
          <option value="Segreteria">Segreteria</option>
        </select>

          Indirizzo email istituzionale e username di Istituto
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Inserisci l'username" aria-label="Recipient's username" aria-describedby="basic-addon2" name="username" id ="username">
            <span class="input-group-text" id="dominio">@example.com</span>
          </div>

         Indirizzo email personale: è l'account di recupero e la prima password di default dell'utente
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Inserisci l'indirizzo email personale" aria-label="Recipient's username" aria-describedby="basic-addon2" name="username" id ="username">
          </div>
  <input type="submit" class="button1 green" value="INSERISCI UTENTE" />
    </div>
</form>


<form action="../index.php">
    <input type="submit" class="button1 lightblue" value="RITORNA ALLA HOMEPAGE" />
    </form>
</body>
</html>