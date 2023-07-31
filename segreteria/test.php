<!DOCTYPE html>
<html>
<head>
<script src="../js/testivari.js"></script>
    <title>Menù a tendina dipendente con query</title>
</head>
<body>
    <h1>Menù a tendina dipendente con query</h1>

    <label for="menutendina1">Menù a tendina 1:</label>
    <select id="menutendina1">
        <option value="Informatica">Informatica</option>
        <option value="informatica">informatica</option>
        <option value="test">test</option>
    </select>

    <label for="menutendina2">Menù a tendina 2:</label>
    <select id="menutendina2">
        <!-- Contenuto del menù a tendina dipendente verrà generato dinamicamente tramite AJAX -->
    </select>

    <script>

    </script>
</body>
</html>

<!--


<?php


try {
    // Connessione al database utilizzando PDO
    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query con CTE
    $query = "
        WITH selezione AS (
                               SELECT utente FROM docente
                               EXCEPT
                               SELECT docente FROM docente_responsabile
                               GROUP BY 1
                               HAVING count(*) >2
                               )
                               SELECT u.nome, u.cognome FROM utente u
                               INNER JOIN selezione s ON u.email = s.utente
    ";

    // Esecuzione della query e recupero dei risultati
    $stmt = $conn->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);




    // Elaborazione dei risultati
   foreach ($results as $row) {
        // Utilizza $row per accedere ai dati dei singoli record
        echo $row['nome'] . ' ' . $row['cognome'] . '<br>';
    }
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}


         /* include('../functions.php');
          $db = open_pg_connection();
          echo "ma ci arrivo qui? ";
          $sql = "WITH selezione AS (
                       SELECT utente FROM docente
                       EXCEPT
                       SELECT docente FROM docente_responsabile
                       GROUP BY 1
                       HAVING count(*) >2
                       )
                       SELECT u.nome, u.cognome FROM utente u
                       INNER JOIN selezione s ON u.email = s.utente
                       ";

                   SELECT u.nome, u.cognome FROM utente u
                   INNER JOIN selezione s ON u.email = s.utente

          echo pg_query($db,$sql);
  /*        echo "ma ci arrivo qui? ";
          echo $row; */
?>

-->