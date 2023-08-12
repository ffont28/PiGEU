<?php
$mainArray = array();

for ($i = 0; $i < 5; $i++) {
    $innerArray = array("0" => $i, "1" => $i * 2);
    $mainArray[] = $innerArray;
}

// Stampa il contenuto dell'array di array
echo "<pre>";
print_r($mainArray);
echo "</pre>";
?>