<?php
$json = file_get_contents('../../Sample_Form.json');
$data = json_decode($json);

// Include the main TCPDF library (search for installation path)
require_once('../tcpdf_include.php');

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle('Your file title here');
$pdf->SetSubject('Your file subject here');

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

// IMPORTANT: disable font subsetting to allow users editing the document
$pdf->setFontSubsetting(false);

// Set font
$pdf->SetFont('helvetica', '', 10, '', false);

// Add a page
$pdf->AddPage();

/*
It is possible to create text fields, combo boxes, check boxes and buttons.
Fields are created at the current position and are given a name.
This name allows to manipulate them via JavaScript in order to perform some validation for instance.
*/

// Set default form properties
$pdf->setFormDefaultProp(array('lineWidth'=>1, 'borderStyle'=>'solid', 'fillColor'=>array(255, 255, 200), 'strokeColor'=>array(255, 128, 128)));

$pdf->SetFont('helvetica', 'BI', 20);
$pdf->Cell(0, 5, 'Your form title here', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('helvetica', '', 12);



// Create FILLABLE / VIEW-ONLY fields


// PENDIENTE: COMENTAR NOMBRES A CADA CAMPO
// First name
$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 10, 'First name ', 0, 0, 'R', 1);
$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->TextField('firstname', 120, 10, array(), array('v'=>$data[0]->value, 'dv'=>$data[0]->value));
$pdf->Ln(10);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 10, 'Date ', 0, 0, 'R', 1);
$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->Cell(120, 10, $data[1]->value, 0, 0, 'L', 0);
$pdf->Ln(10);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 10, 'Geo Location ', 0, 0, 'R', 1);
$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->TextField('geolocation', 120, 10, array(), array('v'=>$data[2]->value, 'dv'=>$data[2]->value));
$pdf->Ln(10);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 10, 'Email ', 0, 0, 'R', 1);
$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->TextField('email', 120, 10, array(), array('v'=>$data[3]->value, 'dv'=>$data[3]->value));
$pdf->Ln(10);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 10, 'Password ', 0, 0, 'R', 1);
$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->TextField('password', 120, 10, array(), array('v'=>$data[4]->value, 'dv'=>$data[4]->value));
$pdf->Ln(10);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 10, 'Number ', 0, 0, 'R', 1);
$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->TextField('number', 120, 10, array(), array('v'=>$data[5]->value, 'dv'=>$data[5]->value));
$pdf->Ln(10);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 40, 'Photo upload ', 0, 0, 'R', 1);
$x = 60;
$pdf->Image('https://19b5bf454221a3009503-837ce6cc4941d82f7e9704a4735379b2.ssl.cf1.rackcdn.com/ffc3279b663d.jpg', $x, '', 30, 40);
$x += 40;
$pdf->Image('https://19b5bf454221a3009503-837ce6cc4941d82f7e9704a4735379b2.ssl.cf1.rackcdn.com/695dd5058348.jpg', $x, '', 30, 40);
$x += 40;
$pdf->Image('https://19b5bf454221a3009503-837ce6cc4941d82f7e9704a4735379b2.ssl.cf1.rackcdn.com/833cd79c2f53.jpg', $x, '', 30, 40);
$pdf->Ln(40);

$pdf->SetFillColor(233, 236, 239);
$pdf->Cell(40, 30, 'Select Multiple ', 0, 0, 'R', 1);
//$pdf->setCellPaddings(2, 2, 0, 0);
$pdf->ListBox('listbox', 120, 30, array('Blue', 'Green'), array('multipleSelection'=>'true'));
$pdf->Ln(60);

$pdf->SetX(50);

// Button to validate and print
$pdf->Button('print', 30, 10, 'Print', 'Print()', array('lineWidth'=>2, 'borderStyle'=>'beveled', 'fillColor'=>array(128, 196, 255), 'strokeColor'=>array(64, 64, 64)));

// Reset button
$pdf->Button('reset', 30, 10, 'Reset', array('S'=>'ResetForm'), array('lineWidth'=>2, 'borderStyle'=>'beveled', 'fillColor'=>array(128, 196, 255), 'strokeColor'=>array(64, 64, 64)));

// Submit button
$pdf->Button('submit', 30, 10, 'Submit', array('S'=>'SubmitForm', 'F'=>'http://localhost/printvars.php', 'Flags'=>array('ExportFormat')), array('lineWidth'=>2, 'borderStyle'=>'beveled', 'fillColor'=>array(128, 196, 255), 'strokeColor'=>array(64, 64, 64)));

// Form validation functions
$js = <<<EOD
function CheckField(name,message) {
	var f = getField(name);
	if(f.value == '') {
	    app.alert(message);
	    f.setFocus();
	    return false;
	}
	return true;
}
function Print() {
	if(!CheckField('firstname','First name is mandatory')) {return;}
	if(!CheckField('lastname','Last name is mandatory')) {return;}
	if(!CheckField('gender','Gender is mandatory')) {return;}
	if(!CheckField('address','Address is mandatory')) {return;}
	print();
}
EOD;

// Add Javascript code
$pdf->IncludeJS($js);

// Close and output PDF document
$pdf->Output('Your filename here.pdf', 'I');