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
    <script src="/js/generacarriera.js"></script>
    <title>Genera Carriera · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>GENERA LA CARRIERA DI UNO STUDENTE</h1>

<div>
    <div>
        <label for="exampleFormControlInput1" class="form-label">Ricerca lo studente:</label>
    </div>
    <label for="cdl" >Studente:</label>
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


