<?php
session_start();
include('../functions.php');
include('../conf.php');
echo "<pre>";
var_dump($_POST);
echo "</pre>";
echo $_POST['nome'];
echo $_POST["action"];
//if (isset($_POST["action"])){
    if ($_POST['action'] == 'MODIFICA ANAGRAFICA UTENTE') {
        error_log("MODIFIC ANAGRAFICA OK");
        $targ = $_POST['hricercato'];
        $db = open_pg_connection();
        echo "sono qui";
        // definisco le variabili

        $nome = $_POST['nome'];
        $cognome = $_POST['cognome'];
        $indirizzo = $_POST['indirizzo'];
        $citta = $_POST['citta'];
        $cf = $_POST['codicefiscale'];
        $persemail = $_POST['emailpersonale'];
        //  $tipo = $_POST['tipo'];
        //  $tipodocente = $_POST['tipodocente'];
        //  $tiposegreteria = $_POST['tiposegreteria'];
        //  $cf = $_POST['codicefiscale'];
        ///////echo $_POST['tipo'];

        //  echo "elems= ".$nome.$cognome.$cf.$indirizzo.$citta.$persemail.$ricercato;
        // inserimento generale a livello di utente sia che sia docente, studente o segreteria
        try { echo "sono qui";
            ///////////////////////////

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
//}
header("Location: gestisciutente.php");
