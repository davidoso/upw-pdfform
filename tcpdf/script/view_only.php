<?php
// NOTE: This script was made with PHP v7.2.3
// Input (GET):			JSON filepath
// Output:				PDF file
// How to call script:	view_only.php?fp=json_filepath_here.json
// NOTE: Modify defined constants on tcpdf_config.php
// NOTE: Change path to include main TCPDF library
require_once('../tcpdf_include.php');
// Extend TCPF with a custom function: MultiRow() allows to add field title and value in a single line
require_once('../tcpdf_multirow.php');
// Insert generated PDF name here
$outputName = 'PDF name here.pdf';
// I: view on browser. D: download directly
$outputMode = 'I';

/**
 *
 * Please don't change the following code:
 *
**/

// Retrieve JSON data
$json_filepath = $_GET['fp'];
$json_file = file_get_contents($json_filepath);
$data = json_decode($json_file);

// Create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle(PDF_TITLE);

// Set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set default form properties
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));
$pdf->SetFont('helvetica', 'BI', 20);			// Title font
$pdf->Cell(0, 5, PDF_FORM_TITLE, 0, 1, 'C');
$pdf->Ln(10);
$pdf->SetFont('helvetica', '', 10);				// JSON data font

// These values must be formatted before output with MultiRow()
$exceptionTypes = array('geo', 'url');
// Images are created with Image(). Image titles are printed with Cell()
$imageTypes = array('file', 'signaturepad', 'sketch', 'issues');
// Ignore following types. They are valid because contain title and value but are not intended to be visible
$ignoreTypes = array('crew');

// Add view-only fields from JSON data
foreach($data->pages as $p)
foreach($p->elements as $e) {
	if(isset($e->title) && isset($e->type)) {
		$title = $e->title;
		$type = $e->type;
		if((isset($e->value) || in_array($type, $exceptionTypes)) && !in_array($type, $ignoreTypes)) {
			$value = '';
			switch($type) {
				case 'geo':
					if(isset($e->buildingName) && isset($e->lat) && isset($e->lng))
						$value = 'Lat: ' . substr($e->lat, 0, 10) .
							'. Lng: ' . substr($e->lng, 0, 10) .
							'. Building name: ' . $e->buildingName;
					break;
				case 'url':
					if(isset($e->value))
						$value = $e->value;
					if(isset($e->url))
						$value = $e->url;
					break;
				case 'issues':			// There could be +1 named images
					$imageURL = array();
					$imageName = array();
					foreach($e->value as $photo) {
						array_push($imageURL, $photo->value[0]->lrImageURL);
						array_push($imageName, $photo->title);
					}
					break;
				case 'file':			// There could be +1 no-named images
					$imageURL = array();
					foreach($e->value as $photo) {
						array_push($imageURL, $photo->lrImageURL);
					}
					break;
				case 'signaturepad':	// There is only one image
				case 'sketch':
					$imageURL = array();
					array_push($imageURL, $e->value->lrImageURL);
					break;
				default:				// Some values are strings. One-dimensional arrays are stringified
					if(gettype($e->value) == 'array')
						$value = implode(", ", $e->value);
					else
						$value = $e->value;
			}
			// Set field title padding. Cell color is omitted because white cells are added next to issue images
			$pdf->setCellPaddings(0, 0, 2, 0);
			// Add images
			if(in_array($type, $imageTypes)) {
				$w = 40;		// Image width
				$h = 30;		// Image height
				$startX = 57;	// Print image starting from this X value
				for($i = 0; $i < count($imageURL); $i++) {
					$pdf->SetFillColor(233, 236, 239);
					$pdf->Cell(40, $h, $title, 0, 0, 'R', 1);
					$pdf->Image($imageURL[$i], $startX, '', $w, $h);
					$pdf->SetX($startX + $w + 2);
					if($type == 'issues') {
						$pdf->SetFillColor(255, 255, 255);
						$pdf->Cell(100, $h, $imageName[$i], 0, 0, 'L', 1);
					}
					$pdf->Ln($h);
				}
			}
			// Add normal string field values
			else {
				$pdf->SetFillColor(233, 236, 239);
				$pdf->MultiRow($title, $value);
			}
		}
		if(isset($e->elements)) {
			// Set field title cell color and padding
			$pdf->SetFillColor(233, 236, 239);
			$pdf->setCellPaddings(0, 0, 2, 0);
			foreach($e->elements as $e2) {
				// Services
				if(isset($e2->serviceType) && isset($e2->ServiceName) && isset($e2->Options)) {
					$stitle = $e2->serviceType;
					$svalue = '';
					switch($stitle) {
						case 'AsTask':
							$svalue = $e2->ServiceName . '. Options: ' . implode(", ", $e2->Options);
							break;
						case 'AsDetail':
							$info = '';
							if(isset($e2->People) && isset($e2->Hour)) {
								$info = $e2->People . ' People, ' . $e2->Hour . ' Hour(s). ';
							}
							$svalue = $e2->ServiceName . '. ' . $info . 'Options: ' . implode(", ", $e2->Options);
							break;
						case 'AsTimer':
							$info = '';
							if(isset($e2->StartTime) && isset($e2->StopTime)) {
								$timediff = $e2->StopTime - $e2->StartTime;
								$info = intdiv($timediff, 60) . ' Minutes. ';
							}
							$svalue = $e2->ServiceName . '. ' . $info . 'Options: ' . implode(", ", $e2->Options);
							break;
					}
					$stitle = 'Service (' . $stitle . ')';
					$pdf->MultiRow($stitle, $svalue);
				}
				// Materials
				if(isset($e2->title) && isset($e2->quantity) && isset($e2->unit)) {
					$material = $e2->title . ': ' . $e2->quantity . ' ' . $e2->unit;
					$pdf->MultiRow('Material', $material);
				}
			}
		}
	}
}

// Close and output PDF document
$pdf->Output($outputName, $outputMode);