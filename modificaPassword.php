<?php session_start(); ?>
<!doctype html>
<html lang="en" data-bs-theme="auto">
<head>
    <!-- import di Bootstrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>


    <script src="../js/general.js"></script>
    <link rel="stylesheet" href="../css/cssSegreteria.css">
    <link rel="stylesheet" href="../css/from-re.css">

    <meta charset="utf-8">

    <title>Modifica Password</title>


</head>
<body>

<h1> PAGINA DI MODIFICA PASSWORD DI <?php echo $_SESSION['username'];?></h1>

<form method="post" onsubmit="return testpassword(this)">
    <div class="center bred">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Inserisci la nuova password</label>
            <input type="password" class="form-control" id="nome" name="password1">
            <label for="exampleFormControlInput1" class="form-label">Inserisci nuovamente la password</label>
            <input type="password" class="form-control" id="nome"  name="password2">

        </div>


        <input type="submit" class="button1 orange" value="MODIFICA PASSWORD" />


    </div>
</form>

<?php
if($_SERVER['REQUEST_METHOD']=='POST'){
    include_once('functions.php');


    $db = open_pg_connection();
    $new_password = md5($_POST['password1']);
    $params = array ($_SESSION['username'], $new_password);
    $sql = "UPDATE credenziali SET password = $2 WHERE username = $1";
    $result = pg_prepare($db,'modificapwd',$sql);
    $result = pg_execute($db,'modificapwd', $params);
}
?>

<form> <!-- action="../index.php" > -->
    <input type="button" onclick="indietro()"
           class="button1 lightblue" value="indietro" />
</form>
</body>
</html>