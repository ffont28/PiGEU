<?php
// Start the session
session_start();

echo "utente non trovato per user ".$_SESSION['username']." e password ".$_SESSION['password'];

unset($_SESSION['email']);
session_destroy();
?>