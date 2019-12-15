<?php
// --------------------------------------------------------------------------------------
// Author:				David Osorio, jdavidosori96@gmail.com
// Upwork profile:		https://www.upwork.com/freelancers/~010be696c9ded003b5
// Date:				November 2019
// PHP version:			7.2.3
// --------------------------------------------------------------------------------------
// SETUP: Change path to include main TCPDF library and customize defined constants on config/tcpdf_config.php
require_once('../tcpdf_include.php');
// Extend TCPDF with a custom function. MultiRow() allows to add field title and value in a single line
require_once('../tcpdf_multirow.php');

// NOTE: Example how to call script and customize input parameters
$json = array("filepath" => "Samples/1st.json");
$config = array("headerLogo" => "other_company.png");
parseJSONPDF($config, $json);


/**
 *
 * The following code has been tested successfully with provided JSON samples
 * Please do not modify it unless you understand what you are doing
 * Recursion reference:
 * https://stackoverflow.com/questions/5524227/php-foreach-with-arrays-within-arrays
 *
**/


/**
 * @param config			Associative array, contains 16 keys:
 	* @param outputName		Output PDF filename
 	* @param outputMode		I: view on browser. D: download directly
 	* @param titleWidth		Title column width. Default: 50. Recommended range value: 30-90. Default unit: mm
	* @param titleColor		Title column background color (RGB array)
	* @param subheaderColor	Subheader row background color (RGB array)
	* @param imageWidth		Image width. Default: 35
	* @param imageHeight	Image height. Default: 25
	* @param lineWidth		Line width. Default: 1
	* @param borderWidth	Border width. Default: 0.1
	* @param borderColor	Border color (RGB array)
	* @param font			Title and body font
	* @param titleFontStyle	Title font style. Default: bold, italic
	* @param titleFontSize	Title font size. Default: 20
	* @param bodyFontStyle	Body font style. Default: normal
	* @param bodyFontSize	Body font size. Default: 10
	* @param headerLogo		Sitefotos logo on "images" folder
 * @param json				Associative array, contains 3 keys:
	* @param filepath		JSON filepath (required)
	* @param headerTitle	Header title (appears on every page)
	* @param formTitle		Form title (appears only at the beginning, before printing JSON data)
**/
function parseJSONPDF($config, $json) {
	if(isset($json['filepath'])) {
		// JSON parameters
		$filepath = $json['filepath'];
		$headerTitle = isset($json['headerTitle']) ? $json['headerTitle'] : 'Default header title';
		$formTitle = isset($json['formTitle']) ? $json['formTitle'] : 'Default form title';
		// Config parameters
		$outputName = isset($config['outputName']) ? $config['outputName'] : 'Custom form.pdf';
		$outputMode = isset($config['outputMode']) ? $config['outputMode'] : 'I';
		$titleWidth = isset($config['titleWidth']) ? $config['titleWidth'] : 50;
		$titleColor = isset($config['titleColor']) ? $config['titleColor'] : array(233, 236, 239);
		$subheaderColor = isset($config['subheaderColor']) ? $config['subheaderColor'] : array(255, 217, 102);
		$imageWidth = isset($config['imageWidth']) ? $config['imageWidth'] : 35;
		$imageHeight = isset($config['imageHeight']) ? $config['imageHeight'] : 25;
		$lineWidth = isset($config['lineWidth']) ? $config['lineWidth'] : 1;
		$borderWidth = isset($config['borderWidth']) ? $config['borderWidth'] : 0.1;
		$borderColor = isset($config['borderColor']) ? $config['borderColor'] : array(80, 80, 80);
		$font = isset($config['font']) ? $config['font'] : 'helvetica';
		$titleFontStyle = isset($config['titleFontStyle']) ? $config['titleFontStyle'] : 'BI';
		$titleFontSize = isset($config['titleFontSize']) ? $config['titleFontSize'] : 20;
		$bodyFontStyle = isset($config['bodyFontStyle']) ? $config['bodyFontStyle'] : '';
		$bodyFontSize = isset($config['bodyFontSize']) ? $config['bodyFontSize'] : 10;
		$headerLogo = isset($config['headerLogo']) ? $config['headerLogo'] : 'sf_logo.png';
		// Fixed constants (non parameters)
		// Switch on printField() only works with these types, unless the object contains a "value" key also
		$knownTypes = array('text', 'comment', 'radiogroup', 'checkbox', 'dropdown', 'dropdownmultiple', 'file', 'signaturepad', 'sketch', 'service', 'material', 'geo', 'url', 'issues', 'segmentInput');
		// Images are created with Image()
		// Field titles are printed with MultiCell()
		// Optional captions with Cell()
		$imageTypes = array('file', 'signaturepad', 'sketch', 'issues');
		// These types contain "title" and "value" keys but are not intended to be visible
		$ignoreTypes = array('crew');

		// Retrieve JSON data
		$json_filepath = $filepath;
		$json_file = file_get_contents($json_filepath);
		$data = json_decode($json_file);

		// Create new PDF document
		$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// Set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(PDF_AUTHOR);
		$pdf->SetTitle(PDF_TITLE);

		// Set default header data
		$pdf->SetHeaderData($headerLogo, PDF_HEADER_LOGO_WIDTH, $headerTitle, PDF_HEADER_STRING);

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
		$pdf->setFormDefaultProp(array('lineWidth' => $lineWidth, 'borderStyle' => 'solid', 'fillColor' => $subheaderColor, 'strokeColor' => $borderColor));
		$pdf->SetFont($font, $titleFontStyle, $titleFontSize);	// Form title font
		$pdf->MultiCell(0, 5, $formTitle, 0, 'C', 0);
		$pdf->Ln(5);
		$pdf->SetFont($font, $bodyFontStyle, $bodyFontSize);	// JSON body font

		// Set border style
		$pdf->SetLineStyle(array('width' => $borderWidth, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $borderColor));

		// Add view-only fields from JSON data. Each "page" contains "elements". Each element contains fields
		foreach($data->pages as $p)
			foreach($p->elements as $e)
				printField($e, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);

		// Add bottom border below the last cell. MultiCell() do have this border but Image() don't
		$pdf->Cell(0, 0.5, '', 'T', false, 'C', 0, '', 0, false, 'T', 'M');

		// Close and output PDF document
		$pdf->Output($outputName, $outputMode);
	}
}


function printField($e, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize) {
	if(isset($e->title) && isset($e->type)) {
		$title = $e->title;
		$type = $e->type;
		if((isset($e->value) || in_array($type, $knownTypes)) && !in_array($type, $ignoreTypes)) {
			$value = '';
			// Get field title and value based on type
			switch($type) {
				// There could be +1 image URLs witn NO captions
				case 'file':
					$imageURL = array();
					foreach($e->value as $photo) {
						array_push($imageURL, $photo->lrImageURL);
					}
					break;
				// There could be +1 image URLs with captions
				case 'issues':
					$imageURL = array();
					$imageName = array();
					foreach($e->value as $photo) {
						if(gettype($photo->value) == 'array')
							array_push($imageURL, $photo->value[0]->lrImageURL);	// Add image URL
						else
							array_push($imageURL, $photo->value->lrImageURL);		// Add image URL
						array_push($imageName, $photo->title);						// Add caption
					}
					break;
				// There is only one image URL
				case 'signaturepad':
				case 'sketch':
					$imageURL = array();
					if(gettype($e->value) == 'array')
						array_push($imageURL, $e->value[0]->lrImageURL);
					else
						array_push($imageURL, $e->value->lrImageURL);
					break;
				case 'service':
					$title = $e->serviceType;
					$value = '';
					switch($title) {
						case 'AsTask':
							$value = $e->ServiceName . '. Options: ' . implode(", ", $e->Options);
							break;
						case 'AsDetail':
							$info = '';
							if(isset($e->People) && isset($e->Hour)) {
								$info = $e->People . ' People, ' . $e->Hour . ' Hour(s). ';
							}
							$value = $e->ServiceName . '. ' . $info . 'Options: ' . implode(", ", $e->Options);
							break;
						case 'AsTimer':
							$info = '';
							if(isset($e->StartTime) && isset($e->StopTime)) {
								$timediff = $e->StopTime - $e->StartTime;
								$info = intdiv($timediff, 60) . ' Minutes. ';
							}
							$value = $e->ServiceName . '. ' . $info . 'Options: ' . implode(", ", $e->Options);
							break;
					}
					$title = 'Service (' . $title . ')';
					break;
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
				case 'material':
					$title = 'Material';
					$value = $e->title . ': ' . $e->quantity . ' ' . $e->unit;
					break;
				// Most field values are simple strings or one-dimensional arrays
				default:
					if(gettype($e->value) == 'array')
						$value = implode(", ", $e->value);
					else
						$value = $e->value;
			}
			$pdf->setCellPaddings(0, 0, 2, 0);					// Set field title right padding
			// Add image first, then caption if exists, then field title
			if(in_array($type, $imageTypes)) {
				$startX = $titleWidth + 16;						// Print image starting from this X value
				for($i = 0; $i < count($imageURL); $i++) {
					// Disable auto page break if cell size can't fit on current page
					if($pdf->GetY() + $imageHeight > 253) {
						$pdf->SetXY($startX - 1, $pdf->GetY());
						$pdf->SetFillColor(255, 255, 255);		// Add a white cell for border below image
						$pdf->Cell($imageWidth + 4, 0.1, '', 'T', 0, 'L', 1);
						$pdf->AddPage();
					}
					// Image
					$pdf->Image($imageURL[$i], $startX + 2, $pdf->GetY() + 2.5, $imageWidth, $imageHeight);
					// Caption
					$caption = isset($imageName[$i]) ? '   ' . ltrim($imageName[$i]) : '';
					$pdf->SetX($startX + $imageWidth + 2);		// If exists, print caption from this X value
					$pdf->SetFillColor(255, 255, 255);			// Add a white cell for borders next to image
					$pdf->Cell(133 - $imageWidth, $imageHeight + 5, $caption, 'TBR', 0, 'L', 1);
					$y = $pdf->GetY();
					// Top border: empty cell to fill in small left and right spaces above image
					$pdf->SetXY($startX - 1, $pdf->GetY());
					$pdf->SetFillColor(255, 255, 255);			// Add a white cell for border above image
					$pdf->SetFont($font, $bodyFontStyle, $bodyFontSize);
					$pdf->Cell($imageWidth + 4, 0.1, '', 'LT', 0, 'L', 1);
					// Field title
					$pdf->SetXY(PDF_MARGIN_LEFT, $y);			// Print field title from this XY value
					$pdf->SetFillColor($titleColor[0], $titleColor[1], $titleColor[2]);
					$pdf->SetFont('helvetica', '', 10);
					$pdf->setCellPaddings(0, 2, 2, 0);			// Set field title top and right padding
					$pdf->MultiCell($titleWidth, $imageHeight + 5, ltrim($title), 1, 'R', 1);
				}
			}
			// Add normal field values (string or stringied array)
			else {
				$pdf->SetFillColor($titleColor[0], $titleColor[1], $titleColor[2]);
				$pdf->MultiRow($titleWidth, ltrim($title), ltrim($value));
			}
		}
		if($e->type == 'subHeader') {
			$pdf->setCellPaddings(0, 2, 0, 0);					// Set subheader top padding
			$pdf->SetFillColor($subheaderColor[0], $subheaderColor[1], $subheaderColor[2]);
			$pdf->MultiCell(186, 8, 'SECTION: ' . ltrim(strtoupper($e->title)), 1, 'C', 1);
		}
	}
	// Recursion if "elements" are nested
	if(isset($e->elements)) {
		foreach($e->elements as $e2)
			printField($e2, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);
	}
	// Recursion if "elements" are nested in "choices" array and "choiceValue" matchs witch selected "value"
	// When "value" is an array e.g. checkboxes, all "elements" ares printed regardless "choiceValue"
	if(isset($e->value) && isset($e->choices) && gettype($e->choices) == 'array') {
		$choiceValue = $e->value;
		foreach($e->choices as $c) {
			if(isset($c->choiceValue) && isset($c->elements)) {
				if(gettype($e->value) == 'array')
					foreach($c->elements as $e3)
						printField($e3, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);
				elseif($choiceValue == $c->choiceValue)
					foreach($c->elements as $e3)
						printField($e3, $pdf, $knownTypes, $imageTypes, $ignoreTypes, $titleWidth, $titleColor, $subheaderColor, $imageWidth, $imageHeight, $font, $bodyFontStyle, $bodyFontSize);
			}
		}
	}
}