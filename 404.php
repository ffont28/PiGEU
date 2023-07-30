<?php
// Start the session
session_start();

echo "utente non trovato per user ".$_SESSION['username'];

unset($_SESSION['email']);
session_destroy();
?>