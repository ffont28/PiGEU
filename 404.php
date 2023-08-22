<?php
// Start the session
session_start();
//
//echo "utente non trovato per user ".$_SESSION['username']." e password ".$_SESSION['password'];
//
//unset($_SESSION['email']);
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/404.css">
    <title>Pagina non trovata</title>
</head>
<body>
<div class="container">
    <h1>Errore 404</h1>
    <p>La pagina che stai cercando non esiste.</p>
    <a href="/">Torna alla pagina di autenticazione</a>
</div>
</body>
</html>