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
    <title>Rimuovi CdL · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>RIMOZIONE CORSO DI LAUREA</h1>

<div>
    <div>
        <label for="exampleFormControlInput1" class="form-label">Ricerca un Corso di Laurea da rimuovere:</label>
    </div>
    <label for="cdl" >CdL:</label>
    <input type="insertText" id="daricercare" placeholder="🔍 RICERCA per NOME o CODICE del Corso di Laurea..." name="utente">

    </div>

</div>

<div id="tabellautenti">

</div>

<div id="popup" class="popup">
    <div class="popup-content">
        <h2>⚠️ ATTENZIONE</h2>
        <p id="popup-text"></p>
        <button id="confirmBtn" class="btn-confirm rounded-pill">Conferma</button>
        <button id="cancelBtn" class="btn-cancel rounded-pill">Annulla</button>
    </div>

<script src="../js/rimuovicdl.js"></script>

</body>

</html>