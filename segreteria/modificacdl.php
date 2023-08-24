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
    <title>Modifica CdL · PiGEU</title>
</head>
<body>
<!-- INIZIO NAVBAR -->
<?php setNavbarSegreteria($_SERVER['REQUEST_URI']);?>
<!-- FINE NAVBAR -->

<h1>MODIFICA UN CORSO DI LAUREA</h1>

<div>
    <label for="exampleFormControlInput1" class="form-label">Seleziona il Corso di Laurea che vuoi modificare:</label>
    <form id="ricercaCdL" action="" method="POST">
        <label for="cdl" >Corso di Laurea:</label>
        <select type='insegnamento' id="cdl" name="cdl">
            <?php

            try {
                $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // vedo TUTTI i corsi di laurea
                $query = "  SELECT distinct c.nome, c.codice  FROM corso_di_laurea c";
                $stmt = $conn->prepare($query);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($results as $row) {
                    echo "<option ";
                    if ($_POST['cdl'] == $row['codice']){ echo "selected";}
                    echo " value=\"".$row['codice']."\">".$row['nome']."</option> ";
                }
                echo ' </select>';

            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }

            ?>
        </select>
        <input type="submit" class="button1 green" value="CARICA LE INFORMAZIONI DEL CDL" >
    </form>
</div>

<?php

if($_SERVER['REQUEST_METHOD']=='POST') {
    $codiceCdL = $_POST['cdl'];
    $nomeCdL = $_POST['nome'];

    if ($_POST['action'] == 'MODIFICA CORSO DI LAUREA') {
        $db = open_pg_connection();
        echo "sono qui";
        // definisco le variabili

        $nome = $_POST['nome'];
        $tipo = $_POST['tipo'];

        try {

            $pdo = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
            $sql = "UPDATE corso_di_laurea SET nome = :nome,
                                            tipo = :tipo
                                            WHERE codice = :codice";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
            $stmt->bindParam(':codice', $codiceCdL, PDO::PARAM_STR);

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

        $query = "SELECT * FROM corso_di_laurea c
                  WHERE c.codice = :c";
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
        // Esecuzione della query e recupero dei risultati
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>      <form method="POST" >
        <div class="center bblue">

<?php     foreach ($results as $row) {
               $tipoinq = $row['tipo'];
               $nomeCdL = $row['nome'];
?>              <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Codice</label>
                    <input readonly type="text" value="<?php echo $row['codice']?>" class="form-control" id="cdl" name="cdl">
                </div>
                <div class="mb-3">
                    <label for="exampleFormControlInput1" class="form-label">Nome</label>
                    <input type="text" value="<?php echo $row['nome']?>" class="form-control" id="nome" name="nome">
                </div>
                <div id="tipocorso">
                    Tipo di corso di studi:
                    <select class="form-select" aria-label="Default select example" id="tipo" name="tipo">
                        <option
<?php                  if ($tipoinq == "triennale") {?> selected <?php }
?>                        value="triennale">triennale</option>
                        <option
<?php                  if ($tipoinq == "magistrale") {?> selected <?php }
?>                        value="magistrale">magistrale</option>
                        <option
<?php                  if ($tipoinq == "magistrale a ciclo unico") {?> selected <?php }
?>                        value="magistrale a ciclo unico">magistrale a ciclo unico</option>
                    </select>
                </div>
                <input type="submit" class="button1 orange" value="MODIFICA CORSO DI LAUREA" name='action'/>
        </div>
        </form>
<?php
            }
       } catch (PDOException $e) {
           echo "Errore: " . $e->getMessage();
       }
///////////////////////////// INSEGNAMENTI CHE FANNO PARTE DEL CDL E CHE POSSO RIMUOVERE //////////////////////
try {


    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $query = "SELECT DISTINCT i.codice, i.nome, ip.anno, i.cfu, u.cognome, u.nome nomedoc FROM insegnamento i
                            INNER JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento
                            INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                            INNER JOIN utente u ON d.docente = u.email
                            WHERE ip.corso_di_laurea = :c ORDER BY anno, cfu";

    $stmt = $conn->prepare($query);

    $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>      <div>
            <label for="exampleFormControlInput1" class="form-label"><h3>Insegnamenti che fanno parte del corso di laurea</h3></label>
        </div>
        <div>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Codice </th>
                    <th scope="col">Nome Insegnamento</th>
                    <th scope="col">Anno</th>
                    <th scope="col">CFU</th>
                    <th scope="col">Docente Responsabile</th>
                    <th scope="col" style="text-align: center;">RIMUOVI</th>
                </tr>
                </thead>
                <tbody>

<?php
    $counter = 1;
    foreach ($results as $row) {
?>              <tr>
                    <th scope="row"><?php echo $counter++?></th>
                    <td><?php echo $row["codice"]?></td>
                    <td><?php echo $row["nome"]?></td>
                    <td><?php echo $row["anno"]?></td>
                    <td><?php echo $row["cfu"]?></td>
                    <td><?php echo $row["cognome"]. " " . $row['nomedoc']?></td>
                    <td style="text-align: center;">
                      <button class="button-canc" 
                              insegnamento="<?php echo  $row["codice"] ?>"
                              cdl="<?php echo $codiceCdL?>">rimuovi dal CdL <?php echo $nomeCdL?></button></td>
                    </tr>
<?php
    }
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
  // Funzione per effettuare la richiesta AJAX
  function cancellaInsdaCdl(insegnamento, cdl) {
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

    xhttp.open('POST', 'rimuoviinsdacdl.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    const params = 'insegnamento=' + encodeURIComponent(insegnamento) + '&cdl=' + encodeURIComponent(cdl);
    xhttp.send(params);
  }

  // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
  const removeFromCdlButtons = document.querySelectorAll('.button-canc');
  removeFromCdlButtons.forEach(button => {
    button.addEventListener('click', function() {
      const insegnamento = this.getAttribute('insegnamento');
      const cdl = this.getAttribute('cdl');

      // Effettua la richiesta AJAX
      cancellaInsdaCdl(insegnamento, cdl);
    });
  });
</script>
<?php
    ///////////////////////////// INSEGNAMENTI CHE NON FANNO PARTE DEL CDL E CHE POSSO AGGIUNGERE ORA //////////////////////
    try {


        $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT DISTINCT i.codice, i.nome, 'ip.anno', i.cfu, u.cognome, u.nome nomedoc FROM insegnamento i
                            LEFT JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento
                            INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                            INNER JOIN utente u ON d.docente = u.email
                  EXCEPT
                            SELECT DISTINCT i.codice, i.nome, 'ip.anno', i.cfu, u.cognome, u.nome nomedoc FROM insegnamento i
                            LEFT JOIN insegnamento_parte_di_cdl ip ON i.codice = ip.insegnamento
                            INNER JOIN docente_responsabile d ON i.codice = d.insegnamento
                            INNER JOIN utente u ON d.docente = u.email
                            WHERE ip.corso_di_laurea = :c 
                  ORDER BY nome";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':c', $codiceCdL, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>      <div>
            <label for="exampleFormControlInput1" class="form-label"><h3>Insegnamenti che NON fanno parte del corso di laurea</h3></label>
        </div>
        <div>
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col">Anno</th>
                <th scope="col">CFU</th>
                <th scope="col">Docente Responsabile</th>
                <th scope="col" style="text-align: center;">AGGIUINGI</th>
            </tr>
            </thead>
            <tbody>
<?php
        $counter = 1;
        foreach ($results as $row) {
            $cfu = $row['cfu'];
            $docResp = $row['cognome']." ".$row['nomedoc'];
            $codiceIns = $row["codice"];
?>           <tr>
                <th scope="row"><?php echo $counter++?></th>
                <td><?php echo $row["codice"]?></td>
                <td><?php echo $row["nome"]?></td>
                <td>
                <select class="form-control" id=\"anno\" name=\"anno\">
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
<?php                 if ($row['tipo'] == 'magistrale a ciclo unico' || $row['tipo'] == 'triennale' ){
?>                  <option value="3">terzo</option>
<?php                 }
                      if ($row['tipo'] == 'magistrale a ciclo unico'){
?>                  <option value="4">quarto</option>
                    <option value="5">quinto</option>
<?php                 }
                }
            } catch (PDOException $e) {
                echo "Errore: " . $e->getMessage();
            }
?>               </select>
                    </td>
                    <td><?php echo $cfu ?></td>
                    <td><?php echo $docResp ?></td>
                    <td style="text-align: center;">
                      <button class="button-iscr" 
                              insegnamento="<?php echo  $codiceIns ?>"
                              cdl="<?php echo $codiceCdL ?>">inserisci nel CdL <?php echo $nomeCdL ?></button></td>
                    </tr>
<?php     }
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
  // Funzione per effettuare la richiesta AJAX
  function inserisciInsinCdL(insegnamento, cdl, anno) {
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
                    '&anno=' + encodeURIComponent(anno);
    xhttp.send(params);
  }

  // Aggiungi un evento clic per i pulsanti di classe \"button-canc\"
  const addToCdlButtons = document.querySelectorAll('.button-iscr');
  addToCdlButtons.forEach(button => {
    button.addEventListener('click', function() {
      const insegnamento = this.getAttribute('insegnamento');
      const cdl = this.getAttribute('cdl');
      var anno = this.closest('tr').querySelector('.form-control').value;
      // Effettua la richiesta AJAX
      inserisciInsinCdL(insegnamento, cdl, anno);
    });
  });
</script>

    <div id="sezionepropedeuticita">
        <div><label for="exampleFormControlInput1" class="form-label"><h3>GESTIONE PROPEDEUTICITÀ</h3></label></div>
        <div>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Codice </th>
                    <th scope="col">Nome Insegnamento</th>
                    <th scope="col" style="text-align: center">PROPEDEUTICO A</th>
                    <th scope="col">Codice</th>
                    <th scope="col">Nome Insegnamento</th>
                    <th scope="col" style="text-align: center;">RIMUOVI</th>
                </tr>
                </thead>
                <tbody id="propedeuticita">

                </tbody>
            </table>
        </div>
    </div>
<script>
    function updateTabPropedInBaseACdL() {
        console.log("richiesta funzione123"); //////////
        var sezioneHtml = document.getElementById("propedeuticita");
        var codiceCdL = '<?php echo $codiceCdL?>' ;

        // Ottieni il valore selezionato nel primo menù a tendina
        //var selezionecdl = codiceCdL.value;

        // Effettua una richiesta AJAX al server per ottenere il contenuto della tabella Propedeuticità
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                console.log("qui nell'XMLHTTP...."); ////////////////////////////////////////////////////////////////////////////////////////
                if (xhr.status === 200) {
                    console.log("CONNESSO OK"); /////////////////////////////////////////////////////////////////////
                    // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                    sezioneHtml.innerHTML = xhr.responseText;
                } else {
                    // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                    console.error("Errore durante la richiesta AJAX");
                }
            }
        };

        // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
        xhr.open("GET", "tabellapropedeuticita.php?value=" + codiceCdL, true);
        xhr.send();
    }

    // Aggiungi un ascoltatore di eventi per il menù a tendina 1
    document.getElementById("cdl").addEventListener('change', updateTabPropedInBaseACdL);

    // Inizializza il contenuto del secondo menù a tendina inizialmente
    updateTabPropedInBaseACdL();

    // Gestione evento di pressione del bottone
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('button-canc')) {
            const ins1 = event.target.getAttribute('ins1');
            const ins2 = event.target.getAttribute('ins2');
            const cdl = event.target.getAttribute('cdl');

            // Seconda chiamata AJAX per cancellare la riga
            const xhttp2 = new XMLHttpRequest();
            xhttp2.onreadystatechange = function() {
                if (this.readyState === 4 && this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    if (response.success) {
                        // Richiama la funzione per aggiornare la tabella
                        updateTabPropedInBaseACdL();
                    }
                }
            };

            // Configura e invia la seconda chiamata AJAX per cancellare la riga
            xhttp2.open('POST', 'rimuovipropedeuticita.php', true);
            xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            const params = 'insegnamento1=' + encodeURIComponent(ins1) +
                '&insegnamento2=' + encodeURIComponent(ins2) +
                '&cdl=' + encodeURIComponent(cdl);
            xhttp2.send(params);
            updateSecondMenutendina();
        }

    });
    console.log("fine del MODIFICA CDL -----------------");
</script>
        <!-------------          SEZIONE PER INSERIRE LE PROPEDEUTICITÀ        ------------------->

<?php
    $counter = 1;
?>

    <div id="possibiliPropedeuticitaDaInserire">
        <table class="table" id="myTable">
            <div><label for="exampleFormControlInput1" class="form-label"><h3>INSERSICI UNA NUOVA PROPEDEUTICITÀ</h3></label></div>
            ricorda che un corso A può essere propedeutico a un corso B solamente se B è un corso di anno pari o superiore ad A
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Codice </th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col" style="text-align: center">PROPEDEUTICO A</th>
                <th scope="col">Codice</th>
                <th scope="col">Nome Insegnamento</th>
                <th scope="col" style="text-align: center;">AGGIUNGI</th>
            </tr>
            </thead>
            <tbody >


            <th scope="row"> > </th>
            <td id="cod1" class="updateValue">  </td>
            <td> <select class="form-control dropdown" id="ins1" name="ins1">

                </select>
            </td>
            <td style="text-align: center"> >>>>> </td>
            <td id="cod2"> </td>
                <td> <select class="form-control" id="ins2" name="ins2">
                    </select> </td>
            <td><button class="button-iscr button-iscr2">✅</button></td>

            </tbody>
        </table>



    </div>
<script>
        //////////////////////////////////////////// KLINI

        function updatePrimoMenutendina() {
            console.log("richiesta funzione");
            var cdl = '<?php echo $codiceCdL;?>';
            var ins1 = document.getElementById("ins1");

            // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {

                    if (xhr.status === 200) {

                        // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                        ins1.innerHTML = xhr.responseText;
                        updatePrimoCodice();
                    } else {
                        // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                        console.error("Errore durante la richiesta AJAX");
                    }
                }
            };

            // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
            xhr.open("GET", "ins1Propedeuticita.php?cdl=" + cdl, true);
            xhr.send();
        }
        updatePrimoMenutendina();
        ////////////////////////////////////////////// KLINI
        function updatePrimoCodice(){
            var codice = document.getElementById("cod1");
            var nome = document.getElementById("ins1").value;

            codice.innerText = nome;
            console.log("after change: " + document.getElementById("cod1").innerText);

            updateSecondMenutendina();
        }

        document.getElementById("ins1").addEventListener('change', updatePrimoCodice);

        function updateSecondoCodice(){
            var codice = document.getElementById("cod2");
            var nome = document.getElementById("ins2").value;

            codice.innerText = nome;

        }

        document.getElementById("ins2").addEventListener('change', updateSecondoCodice);
        updatePrimoCodice();

function updateSecondMenutendina() {
    var cdl = '<?php echo $codiceCdL;?>';
    var cod1 = document.getElementById("cod1").innerText;
    var secondaparte = document.getElementById("ins2");

    // Effettua una richiesta AJAX al server per ottenere il contenuto del secondo menù a tendina
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {

            if (xhr.status === 200) {
                // Se la richiesta è riuscita, aggiorna il contenuto del secondo menù a tendina
                secondaparte.innerHTML = xhr.responseText;
                updateSecondoCodice();
            } else {
                // Se la richiesta ha avuto esito negativo, mostra un messaggio di errore
                console.error("Errore durante la richiesta AJAX");
            }
        }
    };

    // Modifica l'URL della richiesta AJAX in base alla selezione del primo menù a tendina
    xhr.open("GET", "ins2Propedeuticita.php?cdl=" + cdl + "&cod1=" + cod1, true);
    xhr.send();
}

// Aggiungi un ascoltatore di eventi per il menù a tendina 1
document.getElementById("ins1").addEventListener('change', updateSecondMenutendina);

// Inizializza il contenuto del secondo menù a tendina inizialmente
updateSecondMenutendina();

        document.addEventListener('click', function(event) {
            const target = event.target;
            if (event.target.classList.contains('button-iscr2')) {
                const cod1 = document.getElementById('cod1').innerHTML;
                const cod2 = document.getElementById('cod2').innerHTML;
                // const ins1 = event.target.getAttribute('ins1');
                // const ins2 = event.target.getAttribute('ins2');
                const cdl = '<?php echo $codiceCdL;?>';
                console.log("evento scatenato con " + cod1 + " " + cod2 + " " + cdl);
                // Seconda chiamata AJAX per cancellare la riga
                const xhttp2 = new XMLHttpRequest();
                xhttp2.onreadystatechange = function() {
                    if (this.readyState === 4 && this.status === 200) {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            // Richiama la funzione per aggiornare la tabella
                            updateTabPropedInBaseACdL();
                            updatePrimoMenutendina();
                            updateSecondMenutendina();
                        }
                    }
                };

                // Configura e invia la seconda chiamata AJAX per cancellare la riga
                xhttp2.open('POST', 'inseriscipropedeuticita.php', true);
                xhttp2.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                const params = 'insegnamento1=' + encodeURIComponent(cod1) +
                    '&insegnamento2=' + encodeURIComponent(cod2) +
                    '&cdl=' + encodeURIComponent(cdl);
                xhttp2.send(params);
            }
        });


    </script>

<?php
}

$pdo = null;
$conn = null;
?>

</body>

</html>


