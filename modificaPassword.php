<?php session_start();
include('functions.php');
importVariPerHomePage();
if(isset($_GET['id'])){
    $id = $_GET['id'];
    //  echo $id;
    $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $query = "SELECT r.utente, u.nome, u.cognome FROM recupero_credenziali r 
              INNER JOIN utente u ON u.email= r.utente
              WHERE randomvalue = :randomvalue
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':randomvalue', $id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$result) {
        header("Location: 404.php");
        exit();
    }
    $_SESSION['username'] = $result['utente'];
    $_SESSION['nome'] = $result['nome'];
    $_SESSION['cognome'] = $result['cognome'];
//    echo ">>".$_SESSION['username']." - ";
    $query = "DELETE FROM recupero_credenziali
              WHERE randomvalue = :randomvalue";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':randomvalue', $id, PDO::PARAM_STR);
    $stmt->execute();
}
?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Modifica Password</title>


</head>
<body>
<?php if (isset($_SESSION['firstaccess']) && $_SESSION['firstaccess'] = "YES"){?>
        <h1> BENVENUTO <?php echo $_SESSION['cognome']. " ". $_SESSION['nome'] ;?></h1>
        <div style="margin-bottom: 25px">AL TUO PRIMO ACCESSO DEVI CREARE UNA NUOVA PASSWORD:</div>
<?php   $_SESSION['firstaccess'] = "NO";
} else { ?>
<h1> PAGINA DI MODIFICA PASSWORD DI <?php echo $_SESSION['cognome']. " ". $_SESSION['nome'] ;?></h1> <?php } ?>

<div id="popup" class="popup">
    <div class="popup-content">
        <h2>ℹ️</h2>
        <p id="popup-text"></p>
    </div>
</div>


<form method="post" onsubmit="return testpassword(this)">
    <div class="center bred">
        <div class="mb-3">
            <label for="password1" class="form-label">Inserisci la nuova password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password1" name="password1">
                <button class="btn btn-outline-secondary toggle-password" type="button">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            <label for="password2" class="form-label">Inserisci nuovamente la password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password2" name="password2">
                <button class="btn btn-outline-secondary toggle-password" type="button">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
        </div>


        <input type="submit" class="button1 orange" value="MODIFICA PASSWORD" />


    </div>
</form>

<?php
if($_SERVER['REQUEST_METHOD']=='POST'){


$db = open_pg_connection();
$new_password = md5($_POST['password1']);
$params = array($_SESSION['username'], $new_password);
$sql = "UPDATE credenziali SET password = $2 WHERE username = $1";
$result = pg_prepare($db, 'modificapwd', $sql);

if ($result) {
    $result = pg_execute($db, 'modificapwd', $params);

            if ($result) {  ?>
            <script>
                const popup = document.getElementById('popup');
                const popupText = document.getElementById('popup-text');
                popupText.textContent = 'Password modificata con successo, attendi il redirecting alla Home Page';
                popup.classList.add('active');
                setTimeout(function() {
                    window.location.href = "index.php";
                }, 4000);
            </script>
            <?php

            } else {
                // Operazione fallita
                echo "Errore durante l'aggiornamento della password.";
            }
    } else {
        // Errore nella preparazione della query
        echo "Errore nella preparazione della query.";
    }
}
?>

<form> <!-- action="../index.php" > -->
    <input type="button" onclick="indietro()"
           class="button1 lightblue" value="indietro" />
</form>
</body>

<script>
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');

    togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function() {
            const parentInputGroup = this.closest('.input-group');
            const passwordInput = parentInputGroup.querySelector('input[class="form-control"]');

            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye-slash');
            icon.classList.toggle('fa-eye');
        });
    });
</script>





</html>