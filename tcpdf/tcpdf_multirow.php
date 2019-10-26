<?php
class MYPDF extends TCPDF {
    public function MultiRow($left, $right) {
        $page_start = $this->getPage();
        $y_start = $this->GetY();
		// Write the left cell
		$this->setCellPaddings(0, 2, 2, 0);
		$this->MultiCell(40, 10, $left, 0, 'R', 1, 2, '', '', true, 0);
        $page_end_1 = $this->getPage();
        $y_end_1 = $this->GetY();
        $this->setPage($page_start);
		// Write the right cell
		$this->setCellPaddings(2, 2, 0, 0);
		$this->MultiCell(0, 0, $right, 0, 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
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
        $this->setPage(max($page_end_1,$page_end_2));
        $this->SetXY($this->GetX(),$ynew);
    }
}
?>