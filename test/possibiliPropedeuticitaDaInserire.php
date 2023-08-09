<?php
include('../functions.php');
include('../conf.php');
$codiceCdL = $_GET['value'];
?>
<table class="table" id="myTable">
    <div><label for="exampleFormControlInput1" class="form-label"><h3>INSERSICI UNA NUOVA PROPEDEUTICITÀ</h3></label></div>

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
        <?php
        try {
            // Connessione al database utilizzando PDO
            $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Esegui la query per ottenere i dati del secondo menù a tendina in base alla selezione del primo
            $stmt = $conn->prepare("SELECT distinct(i.nome), i.codice FROM insegnamento i
                            INNER JOIN insegnamento_parte_di_cdl p ON p.corso_di_laurea = :valore 
                                                                        AND p.insegnamento = i.codice 
                            ");
            $stmt->bindParam(':valore', $codiceCdL);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


            // echo "<option selected value=\"no\">nessuna propedeuticità</option>";
            // Genera le opzioni per il secondo menù a tendina
            foreach ($results as $row) {
                echo '<option value="' . $row['codice'] . '">' . $row['nome'] . '</option>';
            }
        } catch (PDOException $e) {
            echo "Errore: " . $e->getMessage();
        }
        ?>
</td>
<td style="text-align: center"> >>>>>>>>>> </td>
<td id="codice2"> </td>
<td> <select class="form-control" id="ins2" name="ins2"> </td>
<td>' . $row["nom1"] . '</td>

    </tbody>
</table>

<script>
    function updatePrimoCodice(){
        var codice = document.getElementById("cod1");
        var nome = document.getElementById("ins1").value;

        codice.innerText = nome;
        console.log("after change: " + document.getElementById("cod1").innerText);
    }

    document.getElementById("ins1").addEventListener('change', updatePrimoCodice);

    // function updateSecondoCodice(){
    //     var codice = document.getElementById("cod2");
    //     var nome = document.getElementById("ins2").value;
    //
    //     codice.innerText = nome;
    //
    // }

    //document.getElementById("ins2").addEventListener('change', updateSecondoCodice);
    updatePrimoCodice();
    //updateSecondoCodice();
</script>




<!---->
<!--<script>-->
<!--    document.addEventListener('DOMContentLoaded', function () {-->
<!--        const table = document.getElementById('myTable');-->
<!--        const rows = table.getElementsByTagName('tr');-->
<!---->
<!--        for (let i = 1; i < rows.length; i++) { // Inizia da 1 per escludere l'intestazione-->
<!--            const row = rows[i];-->
<!--            const dropdown = row.querySelector('.dropdown');-->
<!--            const updateCell = row.querySelector('.updateValue');-->
<!---->
<!--            dropdown.addEventListener('change', function () {-->
<!--                const selectedOption = dropdown.options[dropdown.selectedIndex].value;-->
<!--                updateCell.textContent = selectedOption; // Aggiorna il valore nella colonna 3-->
<!--            });-->
<!--        }-->
<!--    });-->
<!---->
<!--    function updatePrimoCodice(){-->
<!--        console.log("qioo")-->
<!--        var codice = document.getElementById("cod1");-->
<!--        var nome = document.getElementById("ins1").value;-->
<!---->
<!--        codice.innerText = nome;-->
<!--    }-->
<!---->
<!--    document.getElementById("ins1").addEventListener('change', updatePrimoCodice);-->
<!--</script>-->