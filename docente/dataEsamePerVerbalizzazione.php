<?php session_start();
include('../functions.php');
include('../conf.php');

$insegnamento = $_GET['value'];
$datacfr = $_SESSION['dataimpostata'];

try {

    $conn = new PDO("pgsql:host=".myhost.";dbname=".mydbname, myuser, mypassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM insegnamento i  
                                  INNER JOIN calendario_esami c ON i.codice = c.insegnamento
                                  WHERE i.codice = :valore");
    $stmt->bindParam(':valore', $insegnamento);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Genera le opzioni per il secondo menù a tendina
    foreach ($results as $row) {
?>      <option
<?php   $data = new DateTime($row['data']);
        if ($datacfr == $row['data']){?> selected <?php }
?>        value="<?php echo $row['data']?>"> <?php echo $data->format("d/m/Y") ?></option>

<?php
    }
?>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        var selectedDate = "<?php echo isset($datacfr) ? $datacfr : ''; ?>";

var selectElement = document.getElementById('data');

    if (selectedDate) {
        for (var i = 0; i < selectElement.options.length; i++) {
            if (selectElement.options[i].value === selectedDate) {
                selectElement.options[i].selected = true;
                break;
            }
        }
    }
});
</script>

<?php
} catch (PDOException $e) {
    echo "Errore: " . $e->getMessage();
}
$conn = null;
?>



