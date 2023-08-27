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
    <script src="/js/gestisciutente.js"></script>
    <title>Gestione utente · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>GESTISCI UN UTENTE</h1>

<div>
    <div>
        <label for="exampleFormControlInput1" class="form-label">Ricerca l' utente:</label>
    </div>
    <label for="cdl" >Utente:</label>
    <input type="insertText" id="daricercare" placeholder="🔍 RICERCA per NOME, o COGNOME, o EMAIL ISTITUZIONALE..." name="utente">

</div>
                                <div id="popup" class="popup">
                                    <div class="popup-content">
                                        <h2>⚠️ ATTENZIONE</h2>
                                        <p id="popup-text"></p>
                                        <button id="confirmBtn" class="btn-confirm rounded-pill">Conferma</button>
                                        <button id="cancelBtn" class="btn-cancel rounded-pill">Annulla</button>
                                    </div>
                                </div>

<div id="tabellautenti">

</div>
</body>

</html>


