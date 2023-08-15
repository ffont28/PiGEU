<?php
require('/var/www/pigeu/TCPDF-main/examples/tcpdf_include.php');
include('functions.php');
// extend TCPF with custom functions
class MYPDF extends TCPDF {

    // Load table data from file
    public function LoadData($file) {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach($lines as $line) {
            $data[] = explode(';', chop($line));
        }
        return $data;
    }

    // Colored table
    public function ColoredTable($header,$data) {
        // Colors, line width and bold font
        $this->SetFillColor(0, 143, 57);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(50, 19, 50, 24, 40);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill, '#' , 1);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row[3], 'LR', 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 'LR', 0, 'C', $fill);
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
$contenuto = '';
$mainArray = array();
if (isset($_POST['action']) && isset($_POST['utente']) &&
    ($_POST['action'] == 'carriera_completa' || $_POST['action'] == 'carriera_valida')){

    $studente = $_POST['utente'];
    $nomestudente = "";
    $cognomestudente = "";
    $matricola = "";
    $cdl = "";
    $tipocarriera = $_POST['action'] == 'carriera_completa'? "COMPLETA" : "VALIDA";

    try {

        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
        $query = "SELECT * FROM carriera_valida(:studente)";
        if ($_POST['action'] == 'carriera_completa'){
            $query = "SELECT * FROM carriera_completa(:studente)";
        }
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':studente', $studente);

        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $contenuto .= 'Nessun insegnamento trovato in carriera per '.$studente;

        } else {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $counter = 1;
                    foreach ($results as $row) {
                        $insegnamento = $row['nomins'];
                        $codice = $row['codins'];
                        $docente = $row['cogdoc']." ".$row['nomdoc'];
                        $voto = $row['voto'];
                        $data = $row['data'] == "non sostenuto" ? "non sostenuto" : date("d/m/Y", strtotime($row['data']));
                        $nomestudente = $row['nomstu'];
                        $cognomestudente = $row['cogstu'];
                        $matricola = $row['matr'];
                        $cdl = $row['cdl'];

                        $innerArray = array("0" => $insegnamento, "1" => $codice,
                            "2" => $docente, "3" => $voto, "4" => $data);
                        $mainArray[] = $innerArray;

                        $contenuto .= $insegnamento .';'. $codice. ';'. $docente .';'. $voto .';'. $data ."\n";
                    }
        }
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }

////////////////// scrivo il file di supporto in txt
//$nome_file = 'support.txt';
//
//// Apri il file in modalità scrittura
//    $file = fopen($nome_file, 'w');
//
//    if ($file) {
//        // Scrivi il contenuto nel file
//        fwrite($file, $contenuto);
//
//        // Chiudi il file
//        fclose($file);
//    }
// ///////////////////////////////////////////////////////
//
//// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PiGEU web services');
$pdf->SetTitle('carriera di '.$cognomestudente.' '.$nomestudente);
$pdf->SetSubject('Certificazione');
$pdf->SetKeywords('Carriera, Certificato, Attestato');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,
    PDF_HEADER_TITLE." ".$tipocarriera ,
    "STUDENTE: ".$cognomestudente.' '.$nomestudente. " | MATRICOLA: ".$matricola. " | CDL: ". $cdl ."\n"  .PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

// add a page
$pdf->AddPage();

// column titles
$header = array('Insegnamento', 'Codice', 'Docente', 'Valutazione', 'Data');

// data loading
    $data = $pdf->LoadData('support.txt');
  //  print_r($data);
// print colored table
    $pdf->ColoredTable($header, $mainArray);
// close and output PDF document
$pdf->Output('example_011.pdf', 'I');
}
//============================================================+
// END OF FILE
//============================================================

?>