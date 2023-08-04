<?php session_start(); ?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="../css/from-re.css">
    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <script src="../js/general.js"></script>

    <meta charset="utf-8">
    <title>Calendario Esami · PiGEU</title>
</head>


<body>
    <!-- INIZIO NAVBAR -->
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link " aria-current="page" href="#">Homepage</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../modificaPassword.php">Modifica la tua password</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="calendarioEsami.php">Gestisci Calendario Esami</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="segreteria/rimuoviutente.php">Verbalizza esiti</a>
        </li>
    </ul>
    <!-- FINE NAVBAR -->

    <h1> PAGINA DI GESTIONE CALENDARIO ESAMI</h1>

    <div class="alert alert-primary" role="alert">
        Benvenuto <?php echo $_SESSION['nome'] . " " . $_SESSION['cognome']; ?> !
    </div>
    <div>
        <label for="exampleFormControlInput1" class="form-label">Inserisci la data e l'ora per l'esame</label>
            <form action="/action_page.php">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data">
                <label for="birthday">Ora:</label>
                <input type="time" id="ora" name="ora">
                <input type="submit">
            </form>
    </div>

    <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
    </div>
</body>

</html>

