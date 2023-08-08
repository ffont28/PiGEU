<?php
include('../functions.php');
include('../conf.php');

if (isset($_POST['insegnamento']) && isset($_POST['cdl']) && isset($_POST['anno'])) {
    //echo json_encode(['success' => true, 'message' => 'Riga inserita con successo in ambo le tabelle']);
    $insegnamento = $_POST['insegnamento'];
    $cdl = $_POST['cdl'];
    $anno = $_POST['anno'];

  //  console.log("anno ".$anno);
 //   echo json_encode(['success' => true, 'message' => '>>>>   '.$anno."  " .$cdl."  ".$insegnamento." ".$_POST['propedeuticita'] ]);

    // Esegui il codice per la connessione al database PostgreSQL come hai già fatto in precedenza
    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query per eliminare la riga dal database
    $sql = "INSERT INTO insegnamento_parte_di_cdl (insegnamento, corso_di_laurea, anno)
            VALUES (:insegnamento, :cdl, :anno)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
    $stmt->bindParam(':cdl', $cdl, PDO::PARAM_STR);
    $stmt->bindParam(':anno', $anno, PDO::PARAM_INT);

    // Esegui la query di eliminazione
    if ($stmt->execute()) {

        if (isset($_POST['propedeuticita'])){
            $proped = $_POST['propedeuticita'];

            $sql = "INSERT INTO propedeuticita (insegnamento1, insegnamento2, corso_di_laurea)
                    VALUES(:proped, :insegnamento, :c)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
            $stmt->bindParam(':c', $cdl, PDO::PARAM_STR);
            $stmt->bindParam(':proped', $proped, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Riga inserita con successo in ambo le tabelle']);
            }
        }

        // La riga è stata eliminata con successo
        // Puoi fare altre operazioni o restituire una risposta JSON per gestire la notifica lato client, se necessario

    } else {
        // Si è verificato un errore durante l'eliminazione
        // Puoi restituire un messaggio di errore come risposta JSON, se necessario
        echo json_encode(['success' => false, 'message' => 'Errore durante l\'inserimento della riga']);
    }
}

?>