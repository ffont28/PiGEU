<?php session_start(); ?>
<!doctype html>
<html lang="IT" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <link rel="stylesheet" href="../css/from-re.css">


    <meta charset="utf-8">

    <title>Studente · PiGEU</title>


</head>
<body>
<!-- INIZIO NAVBAR -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="#">Homepage</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="../modificaPassword.php">Modifica la tua password</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="iscrizioneEsame.php">Iscrizione Esami</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="infoCdL.php">Info sui Corsi di Laurea</a>
    </li>
    <li class="nav-item mr-2">
        <a class="nav-link" href="../index.php">LOGOUT</a>
    </li>
</ul>
<!-- FINE NAVBAR -->


<h1>Benvenuto <?php echo $_SESSION['nome']." ".$_SESSION['cognome'] ?></h1>

<form action="../index.php" method="post">
    <input type="submit" name="logout" class="button1 black" value="logout" />
</form>



</body>
</html>


<!--

<!doctype html>
<html lang="it">
<head><title>Segreteria</title></head>

<body>
<h1>Accesso segreteria</h1>
<p>==> accesso eseguito come <?php echo $_SESSION['username']  ?></p>
</body>
</html>

-->