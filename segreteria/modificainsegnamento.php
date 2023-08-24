<?php
session_start();
include('../functions.php');
include('../conf.php');
controller("segreteria", $_SESSION['username'], $_SESSION['password']);
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <?php importVari();?>
    <script src="../js/modificainsegnamento.js"></script>
    <title>Modifica Insegnamento · PiGEU</title>
</head>


<body>

<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>MODIFICA UN INSEGNAMENTO</h1>

<div>
    <label for="exampleFormControlInput1" class="form-label">Seleziona l'Insegnamento che vuoi modificare:</label>
    <form id="ricercaInsegnamento" action="" method="POST">
        <label for="cdl" >Insegnamento:</label>
        <select type='insegnamento' id="insegnamento" name="insegnamento">
            <?php

            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // vedo TUTTI gli insegnamenti
                $query = "  SELECT distinct i.nome, i.codice  FROM insegnamento i ORDER BY i.nome";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {
?>                  <option
<?php               if ($_POST['insegnamento'] == $row['codice']){?>selected<?php }
?>                  value="<?php echo $row['codice']?>"><?php echo $row['nome']?>
                    </option>
<?php           }

            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
?>
        </select>
        <input type="submit" class="button1 green" value="CARICA INFORMAZIONI" >
    </form>
</div>

<?php

if($_SERVER['REQUEST_METHOD']=='POST') {
    $codiceInsegnamento = $_POST['insegnamento'];

    if ($_POST['action'] == 'MODIFICA INSEGNAMENTO') {
        $nome = $_POST['nome'];
        $codice = $_POST['codice'];
        $responsabile = $_POST['responsabile'];
        $descrizione = $_POST['descrizione'];
        $cfu = $_POST['cfu'];

        /////// AGGIORNO LA TABELLA INSEGNAMENTO
        try {

            $pdo = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $sql = "UPDATE insegnamento SET nome = :nome,
                                            codice = :codice,
                                            descrizione = :descrizione,
                                            cfu = :cfu
                            WHERE codice = :codice";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':descrizione', $descrizione, PDO::PARAM_STR);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);
            $stmt->bindParam(':cfu', $cfu, PDO::PARAM_STR);

            $stmt->execute();

            $rowCount = $stmt->rowCount();
            if ($rowCount > 0) {
                echo "Update successful.";
            } else {
                echo "No rows updated.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        /////// AGGIORNO LA TABELLA RESPONSABILE
        try {

            $sql = "UPDATE docente_responsabile SET docente = :docente
                                            WHERE insegnamento = :codice";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':docente', $responsabile, PDO::PARAM_STR);
            $stmt->bindParam(':codice', $codice, PDO::PARAM_STR);

            $stmt->execute();

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

    try {

        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);

        $query = "SELECT * FROM insegnamento i
                  INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                  WHERE i.codice = :i";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
        <form method="POST" >
            <div class="center bblue">
<?php
        foreach ($results as $row) {
            $responsabile = $row['docente'];
            $anno = $row['anno'];
            $descrizione = $row['descrizione'];
            $cfu = $row['cfu'];

?>              <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nome</label>
                    <input readonly type="text" value="<?php echo $row['nome']?>" class="form-control" id="nome" name="nome">
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Codice</label>
                    <input type="text" value="<?php echo $row['codice']?>" class="form-control" id="codice" name="codice">
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Docente Responsabile</label>
                    <select class="form-select" id="responsabile" name="responsabile">";
<?php
            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "     WITH selezione AS (
                                                  SELECT utente FROM docente
                                                  EXCEPT
                                                  SELECT docente FROM docente_responsabile
                                                  GROUP BY 1
                                                  HAVING count(*) >2
                                                  )
                                                  SELECT u.nome, u.cognome, u.email FROM utente u
                                                  INNER JOIN selezione s ON u.email = s.utente
                ";

                $stmt2 = $conn->query($query);
                $results = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {
?>                  <option
<?php                   if ($responsabile == $row['email']) {?>selected<?php }
?>                      value="<?php echo $row['email']?>"><?php echo $row['nome']." ".$row['cognome']?>
                    </option>
<?php           }
            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
?>
                   </select>
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Descrizione</label>
                    <input type="text" value="<?php echo $descrizione?>" class="form-control" id="descrizione" name="descrizione">
                </div>
                <div id="cfu">
                    <label for="exampleFormControlInput1" class="form-label">CFU previsti per l\'insegnamento</label>
                    <select class="form-select" aria-label="Default select example" id="cfu" name="cfu">
                        <option
<?php                   if ($cfu == '6') {?> selected <?php }
?>                      value="6">6</option>
                        <option
<?php                   if ($cfu == '9') {?> selected <?php }
?>                      value="9">9</option>
                        <option
<?php                   if ($cfu == '12') {?> selected <?php }
?>                      value="12">12</option>
                    </select>
                </div>

            <input type="submit" class="button1 orange" value="MODIFICA INSEGNAMENTO" name='action'/>
            </div>
        </form>
<?php   }
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
    ///////////// DOCENTI COINVOLTI CON L'INSEGNAMENTO: tabella "insegna" ///////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query_insegnano =   "SELECT u.nome, u.cognome, u.email
                              FROM utente u INNER JOIN docente d ON u.email = d.utente
                                            INNER JOIN insegna i ON d.utente = i.docente
                                            WHERE i.insegnamento = :i";
        $query_non_insegnano ="SELECT u.nome, u.cognome, u.email
                               FROM utente u INNER JOIN docente d ON u.email = d.utente
                                             WHERE d.utente <> ALL (SELECT docente FROM insegna WHERE insegnamento = :i)
                               EXCEPT
                               SELECT u.nome, u.cognome, u.email
                               FROM utente u INNER JOIN docente d ON u.email = d.utente
                                             INNER JOIN docente_responsabile dr ON d.utente = dr.docente
                                             WHERE dr.insegnamento = :i
                               ORDER BY cognome";

        $stmt1 = $conn->prepare($query_insegnano);
        $stmt2 = $conn->prepare($query_non_insegnano);

        $stmt1->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        $stmt2->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);

        $stmt1->execute();
        $stmt2->execute();

        $results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div><label for="exampleFormControlInput1" class="form-label"><h3>Docenti coinvolti con l'insegnamento</h3></label>
        </div>

        <div class="splitin2">
            <div>
                <label class="form-label"><h5>DOCENTI CHE INSEGNANO QUESTA DISCIPLINA</h5></label>
            </div>
            <div>
                <label class="form-label"><h5>DOCENTI CHE NON INSEGNANO QUESTA DISCIPLINA</h5></label>
            </div>
        </div>
        <div class="splitin2">
            <div>
            <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">DOCENTE </th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results1 as $row) {
            ?> <tr>
               <th scope="row"><?php echo $counter++ ?></th>
               <td><?php echo $row["cognome"]. " ".$row["nome"]?></td>
               <td style="text-align: center;">
               <button class="button-canc-doc"
                       docente="<?php echo $row['email'] ?>"
                       insegnamento="<?php echo $codiceInsegnamento ?>">rimuovi docente</button></td>
<?php   }
        ?>
            </tbody>
        </table>
    </div>
            <div>
                <div>
                    <label for="cdl" >Ricerca Docente:</label>
                    <input type="insertText" id="docdaricercare" placeholder="🔍 RICERCA per NOME o COGNOME del docente" name="utente">

                </div>
                <div id="tabelladocenti">



                </div>


            </div>
        </div>
    <?php
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }

   ?>

<?php
///////////////////////////// CDL DI CUI QUESTO INSEGNAMENTO FA PARTE E CHE POSSO RIMUOVERE //////////////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT c.codice, c.nome, ip.anno, ins.cfu, ins.codice codice_ins
                        FROM corso_di_laurea c 
                        INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                        INNER JOIN insegnamento ins ON ip.insegnamento = ins.codice
                        WHERE ip.insegnamento = :i
                        ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>      <div>
            <label for="exampleFormControlInput1" class="form-label"><h3>Corsi di laurea che contemplano questo insegnamento</h3></label>
        </div>
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Corso di Laurea</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col" style="text-align: center;">RIMUOVI</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
?>          <tr>
                <th scope="row"><?php echo $counter++?></th>
                <td><?php echo $row["codice"]?></td>
                <td><?php echo $row["nome"]?></td>
                <td><?php echo $row["anno"]?></td>
                <td><?php echo $row["cfu"]?></td>
                <td style="text-align: center;">
                  <button class="button-canc" 
                          insegnamento="<?php echo  $codiceInsegnamento ?>"
                          cdl="<?php echo $row["codice"]?>">rimuovi da questo CdL</button></td>
                </tr>
<?php   }
?>         </tbody>
        </table>
    </div>
<?php
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
?>
<script>

</script>
<?php
    ///////////////////////////// CDL DI CUI QUESTO INSEGNAMENTO NON FA PARTE E CHE POSSO AGGIUNGERE ORA //////////////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $strcfu = strval($cfu);
        $query = "SELECT c.codice, c.nome, ins.codice codice_ins, c.tipo
                        FROM corso_di_laurea c 
                        INNER JOIN insegnamento ins ON ins.codice = :i
                  EXCEPT
                        SELECT c.codice, c.nome, ins.codice codice_ins, c.tipo
                        FROM corso_di_laurea c 
                        INNER JOIN insegnamento_parte_di_cdl ip ON c.codice = ip.corso_di_laurea
                        INNER JOIN insegnamento ins ON ip.insegnamento = ins.codice
                        WHERE ip.insegnamento = :i
                        ";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':i', $codiceInsegnamento, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>      <div>
            <label for="exampleFormControlInput1" class="form-label"><h3>Corsi di laurea che NON contemplano questo insegnamento</h3></label>
        </div>
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Corso di Laurea</th>
                <th scope="col">Tipo</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col">Propedeuticità</th>
                <th scope="col" style="text-align: center;">AGGIUNGI</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
            $codiceCdL = $row["codice"];
?>          <tr>
                <th scope="row"><?php echo $counter++?></th>
                <td><?php echo $row["codice"]?></td>
                <td><?php echo $row["nome"]?></td>
                <td><?php echo $row["tipo"]?></td>

                <td>
                <select class="form-control" id="anno" name="anno">
<?php
                try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT tipo
                          FROM corso_di_laurea
                          WHERE codice = :c";
                $stmt = $conn->prepare($query);

                $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $tipo = "";
                foreach ($results as $row) {
?>                  <option value="1">primo</option>
                    <option value="2">secondo</option>
<?php               if ($row['tipo'] == 'magistrale a ciclo unico' || $row['tipo'] == 'triennale' ){
?>                  <option value="3">terzo</option>
<?php               }
                    if ($row['tipo'] == 'magistrale a ciclo unico'){
?>                  <option value="4">quarto</option>
                    <option value="5">quinto</option>
<?php               }
                }
            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
?>
                </select>
                </td>
                <td><?php echo $cfu?></td>
                <td>
                <select class="form-control2" id="propedeuticita" name="propedeuticita">
<?php
            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $query = "SELECT i.codice, i.nome
                          FROM insegnamento i 
                          INNER JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento 
                          WHERE ip.corso_di_laurea = :c
                         ";
                $stmt = $conn->prepare($query);

                $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $tipo = "";
?>              <option selected value="no">nessuna propedeuticità</option>
<?php           foreach ($results as $row) {
?>              <option value="<?php echo $row['codice']?>"><?php echo $row['nome'] ?></option>
<?php           }
            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
?>              </select>
                </td>
                <td style="text-align: center;">
                    <button class="button-iscr"
                        insegnamento="<?php echo $codiceInsegnamento ?>"
                        cdl="<?php echo $codiceCdL?>">inserisci nel CdL</button></td>
            </tr>
<?php   }
?>
            </tbody>
        </table>
    </div>
<?php
    } catch (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }
?>
<script>
 document.addEventListener('DOMContentLoaded', function() {
  // Funzione per effettuare la richiesta AJAX
  function inserisciInsinCdL(insegnamento, cdl, anno, propedeuticita) {
    const xhttp = new XMLHttpRequest();

    xhttp.onreadystatechange = function() {
      if (this.readyState === 4) {
        if (this.status === 200) {
          // Gestisci la risposta del server
          const response = JSON.parse(this.responseText);
          console.log(response);
        if (response.success) {
          window.location.reload();
          }
        } else {
          // Gestisci eventuali errori
          console.error('Errore nella richiesta AJAX:', this.statusText);
         }
      }
    };

    xhttp.open('POST', 'inseriscinelcdl.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    const params = 'insegnamento=' + encodeURIComponent(insegnamento) + 
                    '&cdl=' + encodeURIComponent(cdl) +
                    '&anno=' + encodeURIComponent(anno) +
                    '&propedeuticita=' + encodeURIComponent(propedeuticita);
    xhttp.send(params);
  }

  // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
  const addToCdlButtons = document.querySelectorAll('.button-iscr');
  addToCdlButtons.forEach(button => {
    button.addEventListener('click', function() {
      const insegnamento = this.getAttribute('insegnamento');
      const cdl = this.getAttribute('cdl');
      const annoold = this.closest('tr').querySelector('select#anno.form-control');
      const anno = annoold.value;
      const propedeuticita = this.closest('tr').querySelector('select#propedeuticita.form-control2').value;
      
      console.log(anno);
      
      // Effettua la richiesta AJAX
      inserisciInsinCdL(insegnamento, cdl, anno, propedeuticita);
    });
  });
  
  });
</script>

<?php
}
$conn = null;
$pdo= null;
?>

</body>

</html>


