<?php session_start();
include('functions.php');
importVariPerHomePage();
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>



    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Modifica Password</title>


</head>
<body>
<?php if (isset($_SESSION['firstaccess']) && $_SESSION['firstaccess'] = "YES"){?>
        <h1> BENVENUTO <?php echo $_SESSION['cognome']. " ". $_SESSION['nome'] ;?></h1>
        <div style="margin-bottom: 25px">AL TUO PRIMO ACCESSO DEVI CREARE UNA NUOVA PASSWORD:</div>
<?php   $_SESSION['firstaccess'] = "NO";
} else { ?>
<h1> PAGINA DI MODIFICA PASSWORD DI <?php echo $_SESSION['cognome']. " ". $_SESSION['nome'] ;?></h1> <?php } ?>

<div id="popup" class="popup">
    <div class="popup-content">
        <h2>ℹ️</h2>
        <p id="popup-text"></p>
    </div>
</div>


<form method="post" onsubmit="return testpassword(this)">
    <div class="center bred">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Inserisci la nuova password</label>
            <input type="password" class="form-control" id="password1" name="password1">
            <label for="exampleFormControlInput1" class="form-label">Inserisci nuovamente la password</label>
            <input type="password" class="form-control" id="password2" name="password2">

        </div>


        <input type="submit" class="button1 orange" value="MODIFICA PASSWORD" />


    </div>
</form>

<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    include_once('functions.php');


    $db = open_pg_connection();
$new_password = md5($_POST['password1']);
$params = array($_SESSION['username'], $new_password);
$sql = "UPDATE credenziali SET password = $2 WHERE username = $1";
$result = pg_prepare($db, 'modificapwd', $sql);

if ($result) {
    $result = pg_execute($db, 'modificapwd', $params);

    if ($result) {  ?>
    <script>
        const popup = document.getElementById('popup');
        const popupText = document.getElementById('popup-text');
        popupText.textContent = 'Password modificata con successo, attendi il redirecting alla Home Page';
        popup.classList.add('active');
        setTimeout(function() {
            window.location.href = "index.php";
        }, 4000);
    </script>
    <?php

    } else {
        // Operazione fallita
        echo "Errore durante l'aggiornamento della password.";
    }
} else {
    // Errore nella preparazione della query
    echo "Errore nella preparazione della query.";
}



}


?>

<form> <!-- action="../index.php" > -->
    <input type="button" onclick="indietro()"
           class="button1 lightblue" value="indietro" />
</form>
</body>

</html>