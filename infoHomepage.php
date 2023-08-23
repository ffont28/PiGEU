<?php
session_start();
include ('conf.php');
if(isset($_POST['submit'])) {

    $originalFileName = $_FILES["fileToUpload"]["name"];
    $extension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

    // Genera un nuovo nome univoco per l'immagine
    $newFileName = $_SESSION['username'] . uniqid() . '.' . $extension;

    $targetDir = "photos/"; // Cartella in cui verrà caricata l'immagine
    $targetFile = $targetDir . $newFileName;
    $targetDir4query = '../'.$targetFile;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));


    // Controlla se l'immagine è reale o falsa
    if(isset($_POST["submit"])) {
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
         //         echo "Il file è un'immagine - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
          //       echo "Il file non è un'immagine.";
            $uploadOk = 0;
        }
    }

    // Controlla la dimensione del file
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Spiacente, il file è troppo grande.";
        $uploadOk = 0;
    }

    // Consentire solo alcuni formati di file
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
        echo "Spiacente, sono consentiti solo file JPG, JPEG, PNG e GIF.";
        $uploadOk = 0;
    }
    $uploadedFile = $_FILES['file']['tmp_name'];

    // Controlla se $uploadOk è impostato su 0 da un errore
    if ($uploadOk == 0) {
        echo "Spiacente, il tuo file non è stato caricato.";
        // Se tutto è ok, prova a caricare il file

    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $_SERVER['DOCUMENT_ROOT']. '/' .$targetFile)) {

            try {
                $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Inserisci il percorso dell'immagine nel database
                $stmt = $db->prepare("INSERT INTO foto_profilo (utente, path, timestamp)
                                              VALUES (:utente, :percorsoImmagine, CURRENT_TIMESTAMP)");
                $stmt->bindParam(':utente', $_SESSION['username'], PDO::PARAM_STR);
                $stmt->bindParam(':percorsoImmagine', $targetDir4query, PDO::PARAM_STR);
                $stmt->execute();

                //       echo "L'immagine è stata caricata e il percorso è stato inserito nel database.";
            } catch (PDOException $e) {
                echo "Errore durante l'inserimento del percorso immagine nel database: " . $e->getMessage();
            }
        } else {
            echo "Spiacente, si è verificato un errore durante il caricamento del file.";
        }
    }
}
try{
    $db = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $stmt = $db->prepare("SELECT path FROM foto_profilo
                                  WHERE utente = :utente
                                  ORDER BY timestamp DESC
                                  LIMIT 1");
    $stmt->bindParam(':utente', $_SESSION['username'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="photo-container" style="margin: 30px">
        <img class="photo-container rounded float-left photo" src="<?php echo $result['path'] ?>" alt="Immagine non caricata">

        <div class="text" >
            <h2><?php echo $_SESSION['nome']?> <?php echo $_SESSION['cognome'] ?></h2>
            <p>tipo di utenza: <?php echo $_SESSION['tipo']?></p>
            <p>---</p>
        </div>
        <div>
            <div class="photo-container-my">
                Aggiorna la tua foto profilo
                <form action="#" method="post" enctype="multipart/form-data">
                    <div class="custom-file">
                        <label for="fileToUpload" class="custom-file-label">Scegli un'immagine</label>
                        <input type="file" name="fileToUpload" id="fileToUpload" class="custom-file-input">
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary mt-3 background-green">Aggiorna Foto</button>
                </form>
            </div>
            <div class="photo-container-my">
                Aggiorna la tua password
                <form action="../modificaPassword.php">
                    <input class="reset-password" type="submit" value="MODIFICA PASSWORD" />
                </form>
            </div>
        </div>
    </div>
    <?php
} catch (PDOException $e) {
    echo "Errore durante il recupero del percorso immagine: " . $e->getMessage();
}
?>