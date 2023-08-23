<?php
include('../functions.php');
include('../conf.php');

if (isset($_POST['insegnamento']) && isset($_POST['cdl']) && isset($_POST['anno'])) {

    $insegnamento = $_POST['insegnamento'];
    $cdl = $_POST['cdl'];
    $anno = $_POST['anno'];


    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $sql = "INSERT INTO insegnamento_parte_di_cdl (insegnamento, corso_di_laurea, anno)
            VALUES (:insegnamento, :cdl, :anno)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
        $stmt->bindParam(':cdl', $cdl, PDO::PARAM_STR);
        $stmt->bindParam(':anno', $anno, PDO::PARAM_INT);

    // Esegui la query di inserimento
    if (!$stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Errore durante l\'insert in insegnamento_parte_di_cdl']);
    }

    if (isset($_POST['propedeuticita'])){
            $proped = $_POST['propedeuticita'];
        if($proped != "no") {
            $sql = "INSERT INTO propedeuticita (insegnamento1, insegnamento2, corso_di_laurea)
                    VALUES(:proped, :insegnamento, :cdl)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':insegnamento', $insegnamento, PDO::PARAM_STR);
            $stmt->bindParam(':cdl', $cdl, PDO::PARAM_STR);
            $stmt->bindParam(':proped', $proped, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'insert in propedeuticita']);
            }
        }
    }


    echo json_encode(['success' => true, 'message' => 'Riga inserita con successo con anche eventuale propedeuticita']);

}
$db = null;
?>