<?php
session_start();
$indirizzoPiGeu = "www.pigeu.net:8081";
include('functions.php');
include('mailer.php');
importVariPerHomePage();
?>
<!doctype html>
<html lang="it" data-bs-theme="auto">
<head>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LdIPLgnAAAAAPLVZKh8iSQMrD0H7xL-yaTEhP2x"></script>
</head>
<body>

    <h1> RECUPERA LA TUA PASSWORD</h1>
    Inserisci qui sotto il tuo indirizzo email di recupero per recuperare la password:

<div id="popup" class="popup">
    <div class="popup-content">
        <h2>ℹ️</h2>
        <p id="popup-text"></p>
    </div>
</div>


<form method="post" >
    <div class="center bred">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Inserisci la tua email di recupero</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="la tua email di recupero, ad es mariorossi@gmail.com">

        </div>
        <input type="submit" class="button1 orange" value="RECUPERA PASSWORD" />
    </div>
</form>

<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    $email = $_POST['email'];
    $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
    $query = "SELECT email, nome, cognome FROM utente WHERE emailpersonale = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {

            $emailist = $result['email'];
            $persona = $result['nome']." ".$result['cognome'];
            $randomValue = uniqid();
            $object = "Recupero credenziali PiGEU";
            $text = "Salve ".$persona.",<br>Di seguito il link monouso
                     per recuperare la tua password: ".$indirizzoPiGeu."/modificaPassword.php?id=".$randomValue.
                     "<br>Cordiali saluti,<br>il team di PiGEU.";
            $query = "INSERT INTO recupero_credenziali VALUES (:email, :randomvalue)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':email', $emailist, PDO::PARAM_STR);
            $stmt->bindParam(':randomvalue', $randomValue, PDO::PARAM_STR);
            $stmt->execute();
            sendMail($email, $object, $text);
            ?>
            <script>
                const popup = document.getElementById('popup');
                const popupText = document.getElementById('popup-text');
                popupText.textContent = 'Email inviata correttamente. Controlla la tua email di recupero';
                popup.classList.add('active');
                setTimeout(function() {
                    window.location.href = "index.php";
                }, 4000);
            </script>
            <?php

        } else {
            // Operazione fallita
            echo "Errore durante la ricerca della password di recupero o nell'invio della mail.";
        }
}
?>

<form action="../index.php" >
    <input type="submit"
           class="button1 lightblue" value="🏠🔑 RITORNA ALLA PAGINA DI ACCESSO" />
</form>
</body>




<script>
    function onClick(e) {
        e.preventDefault();
        grecaptcha.enterprise.ready(async () => {
            const token = await grecaptcha.enterprise.execute('6LdIPLgnAAAAAPLVZKh8iSQMrD0H7xL-yaTEhP2x', {action: 'LOGIN'});
            // IMPORTANT: The 'token' that results from execute is an encrypted response sent by
            // reCAPTCHA Enterprise to the end user's browser.
            // This token must be validated by creating an assessment.
            // See https://cloud.google.com/recaptcha-enterprise/docs/create-assessment
        });
    }
</script>