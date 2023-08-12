<?php
//require('/var/www/pigeu/TCPDF-main/examples/tcpdf_include.php');
require('/var/www/pigeu/TCPDF-main/tcpdf.php');
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
        $w = array(40, 35, 40, 45);
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
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}
$contenuto = '';

if (isset($_POST['action']) && isset($_POST['utente']) &&
    ($_POST['action'] == 'carriera_completa' || $_POST['action'] == 'carriera_valida')){

    $studente = $_POST['utente'];
    $nome = "";
    $cognome = "";
    $matricola = "";

    try {

        $conn = new PDO("pgsql:host=" . myhost . ";dbname=" . mydbname, myuser, mypassword);
        $query = "SELECT * FROM carriera_completa_esami_sostenuti(:studente)";
        if ($_POST['action'] == 'carriera_completa'){
            $query = "SELECT * FROM carriera_completa_tutti(:studente)";
        }
        $stmt = $conn->prepare($query);

        $stmt->bindParam(':studente', $studente);

        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            ?>
            <div class="alert alert-warning" role="alert">
                Nessun insegnamento trovato in carriera per <?php echo $studente ?>
            </div>
            <?php
        } else {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $contenuto .='<div class="table-container">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col"><strong>#</strong></th>
                        <th scope="col">Insegnamento</th>
                        <th scope="col">Codice</th>
                        <th scope="col">Valutazione</th>
                        <th scope="col">Data</th>
                    </tr>
                    </thead>
                    <tbody>';

                    $counter = 1;
                    foreach ($results as $row) {
                        $insegnamento = $row['nomins'];
                        $codice = $row['codins'];
                        $voto = $row['voto'];
                        $data = $row['data'];


                $contenuto .='<tr>
                            <th scope="row"> '. $counter++.' </th>
                            <td> '. $insegnamento .'</td>
                            <td> '. $codice. '</td>
                            <td> '. $voto .' </td>
                            <td> '. $data .' </td>

                        </tr>';
                     }

            $contenuto .=
                    '</tbody>
                </table>
            </div>';
         }
    } catch
    (PDOException $e) {
        echo "Errore: " . $e->getMessage();
    }

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('PiGEU web services');
$pdf->SetTitle('carriera di Gabriele Dino');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' per lo studente XXXXXXXXX ', PDF_HEADER_STRING . "CARRIERA [TIPOCARRIERA]");

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
$pdf->SetFont('helvetica', '', 12);

// add a page
$pdf->AddPage();

//$table = new TCPDFTable();
//$table->AddPage();

// column titles
$header = array('Insegnamento', 'Codice', 'Docente', 'Valutazione', 'Data');

$pdf->writeHTML($contenuto);
// close and output PDF document
$pdf->Output('example_011.pdf', 'I');
}
//============================================================+
// END OF FILE
//============================================================

?>