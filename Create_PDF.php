<?php
$json = file_get_contents('Sample_Form.json');
$data = json_decode($json);

use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader;
use setasign\FpdiProtection\FpdiProtection;

require_once('fpdf.php');
require_once('src/autoload_fpdiprot.php');
require_once('src/autoload_fpdi.php');

// Set portrait orientation and page size (letter)
// References: https://github.com/Setasign/fpdi-protection
$pdf = new FpdiProtection('P', 'cm', array(21.59, 27.94));
$pdf->setProtection(FpdiProtection::PERM_PRINT | FpdiProtection::PERM_COPY, '', '');
$pdf->setSourceFile('Template.pdf');
$template_pdf = $pdf->importPage(1);

try {
    // Add page from template and write date
    $pdf->AddPage();
    $pdf->useTemplate($template_pdf);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(16.1, 2.1);
    $pdf->SetTextColor(0, 0, 0);                                            // Negro
    $pdf->Cell(3, 0, $data[1]->value, 0, 0, 'L');
    $pdf->SetXY(0, 4.2);
    $pdf->SetFont('Arial', 'B', 13);
    $pdf->SetTextColor(198, 54, 27);                                        // Rojo Peña Colorada
    $pdf->Cell(21.59, 0, 'Form title', 0, 0, 'C');
    $y = 5;
    if(empty($data)) {
        $pdf->SetFont('Arial', '', 13);
        $pdf->SetXY(0, $y);
        $pdf->SetTextColor(93, 115, 136);                                   // Azul Peña Colorada
        $pdf->Cell(21.59, 0, 'No data found', 0, 0, 'C');
    }
    else {
        $pdf->SetFont('Arial', '', 10);
        for($i = 0; $i < 8; $i++) {
            if($data[$i]->type == 'file') {
                $pdf->SetXY(0, $y);
                $pdf->SetDrawColor(93, 115, 136);                           // Azul Peña Colorada
                $pdf->Cell(1);                                              // Margen izquierdo
                $pdf->SetXY(7.2, $y);
                $pdf->Cell(13.39, 5, null, 1);                              // Margen imagen
                $pdf->SetXY(7.4, $y + 0.5);
                $pdf->Image(utf8_decode($data[$i]->value[0]->lrImageURL), null, null, 4, 4);
                $pdf->SetXY(7.4, $y + 0.5);
                $pdf->Image(utf8_decode($data[$i]->value[1]->lrImageURL), null, null, 4, 4);
                $pdf->SetXY(7.4, $y + 0.5);
                $pdf->Image(utf8_decode($data[$i]->value[2]->lrImageURL), null, null, 4, 4);
                $pdf->SetXY(1, $y);
                $pdf->SetTextColor(198, 54, 27);                            // Rojo Peña Colorada
                $pdf->MultiCell(6.2, /*$h*/ 5, utf8_decode($data[$i]->title), 1, 'R');
            }
            else {
                $pdf->SetXY(0, $y);
                $pdf->SetDrawColor(93, 115, 136);                           // Azul Peña Colorada
                $pdf->Cell(1);                                              // Margen izquierdo
                $pdf->SetXY(7.2, $y);
                $pdf->SetTextColor(0, 0, 0);                                // Negro
                // Escribir primero valor del campo para determinar la altura de la celda
                $pdf->MultiCell(13.39, 0.65, utf8_decode($data[$i]->value), 1, 'L');
                // La altura de la celda son múltiplos de 0.65
                $h = $pdf->GetY() - $y;
                $pdf->SetXY(1, $y);
                $pdf->SetTextColor(198, 54, 27);                            // Rojo Peña Colorada
                $pdf->MultiCell(6.2, $h, utf8_decode($data[$i]->title), 1, 'R');
                $y = $y + $h;
            }
        }
    }

    // Download PDF ('i' to open file on another tab)
    $pdf->Output('Ficha SIGEPRO.pdf', 'i');
} // try
catch(PDOException $e) {
    echo 'ERROR: ' . $e->getMessage();
} // catch

?>