<!doctype html>
<html lang="it">
<head><title>Segreteria</title></head>
<?php
// Start the session
session_start();
?>
<body>
<h1>Accesso segreteria</h1>
<p>---> accesso eseguito come <?php echo $_SESSION['username']  ?></p>
</body>
</html>