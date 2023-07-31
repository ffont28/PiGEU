<!DOCTYPE html>
<html>
<head>
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
        // Funzione che viene chiamata quando cambia il valore del primo menù a tendina
        function updateSecondMenutendina() {
            var menutendina1 = document.getElementById("menutendina1");
            var menutendina2 = document.getElementById("menutendina2");

            // Ottieni il valore selezionato nel primo menù a tendina
            var selezioneMenutendina1 = menutendina1.value;
               console.log(selezioneMenutendina1);
            // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        console.log("eccoci qui");
                        // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                        console.log(xhr.responseText);
                        menutendina2.innerHTML = xhr.responseText;
                    } else {
                        // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                        console.error("Errore durante la richiesta AJAX");
                    }
                }
            };

            // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
            xhr.open("GET", "query.php?value=" + selezioneMenutendina1, true);
            xhr.send();
        }

        // Aggiungi un ascoltatore di eventi per il menù a tendina 1
        document.getElementById("menutendina1").addEventListener("change", updateSecondMenutendina);

        // Inizializza il contenuto del secondo menù a tendina inizialmente
        updateSecondMenutendina();
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