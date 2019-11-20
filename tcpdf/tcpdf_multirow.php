<?php
class MYPDF extends TCPDF {

    public function MultiRow($leftWidth, $left, $right) {
        // Disable auto page break if cell size can't fit on current page
        if($this->GetY() + 10 > 264.5) {
            $this->AddPage();
        }
        $page_start = $this->getPage();
        $y_start = $this->GetY();
		// Write the left cell
		$this->setCellPaddings(0, 2, 2, 0);
		$this->MultiCell($leftWidth, 10, $left, 1, 'R', 1, 2, '', '', true, 0);
        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();
        $this->setPage($page_start);
		// Write the right cell
		$this->setCellPaddings(2, 2, 0, 0);
		$this->MultiCell(0, $y_end_1 - $y_start, $right, 'TBR', 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
        $page_end_2 = $this->getPage();
        $y_end_2 = $this->GetY();
        // Set the new row position by case
        if(max($page_end_1,$page_end_2) == $page_start) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif($page_end_1 == $page_end_2) {
            $ynew = max($y_end_1, $y_end_2);
        } elseif($page_end_1 > $page_end_2) {
            $ynew = $y_end_1;
        } else {
            $ynew = $y_end_2;
        }
        $this->setPage(max($page_end_1, $page_end_2));
        $this->SetXY($this->GetX(), $ynew);
    }

    public function Footer() {
        $image_file = K_PATH_IMAGES . 'sf_logo.png';
        // Set Sitefotos image
        $this->Image($image_file, 20, 268, 15, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set footer font, position and style
        $this->SetFont('helvetica', 'BI', 8);
        $this->SetY(264.5);
		$this->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(80, 80, 80)));
        // Set page number
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 'T', false, 'C', 0, '', 0, false, 'T', 'M');
    }
}
?>