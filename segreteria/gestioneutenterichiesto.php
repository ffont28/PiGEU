<?php
session_start();
include('../functions.php');
include('../conf.php');
if (isset($_POST["action"])){
    error_log("ACTION È " . $_POST['action']);
    if ($_POST['action'] == 'MODIFICA ANAGRAFICA UTENTE') {
        error_log("MODIFIC ANAGRAFICA OK");
        $targ = $_POST['hricercato'];

        // definisco le variabili

        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $indirizzo = $_POST['indirizzo'];
        $citta = $_POST['citta'];
        $cf = $_POST['codicefiscale'];
        $persemail = $_POST['emailpersonale'];

        try { echo "sono qui";


            $pdo = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $sql = "UPDATE utente SET nome = :nome,
                                            cognome = :cognome,
                                            codicefiscale = :cf,
                                            indirizzo = :indirizzo,
                                            citta = :citta,
                                            emailpersonale = :persemail
                                            WHERE email = :ricercato";

            // Step 3: Create a prepared statement
            $stmt = $pdo->prepare($sql);

            // Step 4: Bind parameters to the prepared statement
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $stmt->bindParam(':cf', $cf, PDO::PARAM_STR);
            $stmt->bindParam(':indirizzo', $indirizzo, PDO::PARAM_STR);
            $stmt->bindParam(':citta', $citta, PDO::PARAM_STR);
            $stmt->bindParam(':persemail', $persemail, PDO::PARAM_STR);
            $stmt->bindParam(':ricercato', $targ, PDO::PARAM_STR);

            // Step 5: Execute the prepared statement
            $stmt->execute();

            // Step 6: Check for success or handle any errors
            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                echo "Update successful.";
            } else {
                echo "No rows updated.";
            }
        } catch (PDOException $e) {
            // Handle any errors that occurred during the process
            echo "Error: " . $e->getMessage();
        }

    }



}

if (isset($_POST['utente'])){
    error_log('post utente ok'. $_POST['utente']);
    $ricercato = $_POST['utente'];

    try {

        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $query = "SELECT * FROM utente u 
                  LEFT JOIN studente s ON u.email = s.utente
                  LEFT JOIN docente d ON u.email = d.utente
                  LEFT JOIN segreteria a ON u.email = a.utente
                  where u.email = :value";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':value', $ricercato, PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ?>
        <form method="post" id="modUtente" action="applicamodificheutente.php">
            <div class="center bblue"> <?php

        foreach ($results as $row) {
            $corsoinq = $row['corso_di_laurea'];
            $tipoinq = $row['tipo'];
            $email = $row['email'];
            ?><div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Nome</label>
                      <input hidden type="text" value="<?php echo $row['email']?>" class="form-control" id="hricercato" name="hricercato">
                      <input type="text" value="<?php echo $row['nome']?>" class="form-control" id="nome" name="nome">
                        </div>
                       <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Cognome</label>
                     <input type="text" value="<?php echo $row['cognome']?>" class="form-control" id="cognome" name="cognome">
                       </div>
                       <div class="mb-3">
                               <label for="exampleFormControlInput1" class="form-label">CODICE FISCALE</label>
                     <input type="text" value="<?php echo $row['codicefiscale']?>" class="form-control" id="codicefiscale" name="codicefiscale">
                       </div>
                     <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">indirizzo</label>
                      <input  type="text" value="<?php echo $row['indirizzo']?>" class="form-control" id="indirizzo" name="indirizzo">
                       </div>
                     <div class="mb-3">
                               <label for="exampleFormControlInput1" class="form-label">città</label>
                     <input type="text" value="<?php echo $row['citta']?>" class="form-control" id="citta" name="citta">
                      </div>


                     <div class="mb-3">
                               <label for="exampleFormControlInput1" class="form-label">email personale</label>
                     <input type="text" value="<?php echo $row['emailpersonale']?>" class="form-control" id="emailpersonale" name="emailpersonale">
                       </div>

<?php
            // caso in cui l'utente sia uno STUDENTE
            if ($corsoinq != ""){
                ?>  <div id="cdl" class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Corso di Laurea a cui lo studente risulta iscritto:</label>
                        <select disabled class="form-select" id="cdl" name="cdl">

<?php
                try {
                    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $query = " SELECT c.nome, c.codice FROM corso_di_laurea c";

                    $stmt = $conn->query($query);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($results as $row) {
                        ?><option
                 <?php  if ($row['codice'] == $corsoinq){?>selected<?php } ?>
                        value="<?php echo $row['codice'] ?>"> <?php echo $row['nome']?></option> ";
              <?php }
                } catch (PDOException $e) {
                    echo "Errore: " . $e->getMessage();
                } ?>

                    </select>
                 </div>
<?php       }
            // caso in cui l'utente sia un DOCENTE
            if ($tipoinq != "") {
     ?>

                 <div id="tipodocente">
                       Tipo di contratto che ha il docente:
                        <select class="form-select" aria-label="Default select example" id="tipo" name="tipodocente">
                              <option
<?php      if ($tipoinq == "ordinario") {?> selected <?php } ?>
                value="ordinario">Ordinario</option>
                              <option
<?php      if ($tipoinq == "associato") {?> selected <?php } ?>
                value="associato">Associato</option>
                              <option
<?php      if ($tipoinq == "a contratto") {?> selected <?php } ?>
                value="a contratto">A contratto</option>
                              <option
<?php      if ($tipoinq == "ricercatore") {?> selected <?php } ?>
                value="ricercatore">Ricercatore</option>
                                      <option
<?php      if ($tipoinq == "ricercatore confermato") {?> selected <?php } ?>
                value="ricercatore confermato">Ricercatore confermato</option>
                                      <option
<?php      if ($tipoinq == "emerito") {?> selected <?php } ?>
                value="emerito">Emerito</option>
                                      <option
<?php      if ($tipoinq == "straordinario") {?> selected <?php } ?>
                value="straordinario">Straordinario</option>
                            </select>
                            
                    </div>
<?php       }
            $query2 = "SELECT * FROM studente WHERE utente = :email";
            $stmt2 = $conn->prepare($query2);
            $stmt2->bindParam(':email', $email);
            $stmt2->execute();
            //  echo "------->>>".$email;
            $isStudente = $stmt2->rowCount() > 0;

            ?> <input type="submit" class="button1 orange" value="MODIFICA ANAGRAFICA UTENTE" name="action"/>
            </div>
        </form>
                    <?php
        }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
}
$conn = null;
?>

<script>
    // Attendere il caricamento completo del documento
    document.addEventListener("DOMContentLoaded", function () {
        var anagraficaForm = document.getElementById("modUtente");

        // Verifica se il form esiste prima di aggiungere l"evento
        if (anagraficaForm) {
            anagraficaForm.addEventListener("submit", function (event) {
                event.preventDefault(); // Evita il comportamento predefinito del form
                var formData = new FormData(anagraficaForm);
                // Effettua una richiesta AJAX per inviare i dati
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "../apart_archivio/provapost.php", true); // Invia i dati alla stessa pagina
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            // Elabora la risposta se necessario
                            console.log(xhr.responseText);
                        } else {
                            console.error("Errore nella richiesta AJAX");
                        }
                    }
                };
                xhr.send(formData);
            });
        }
    });
</script>