<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Foobar  extends CI_Controller
{

	public function index()
	{
		ob_start();
		$this->load->library('Pdf');
		$pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
		$pdf->SetTitle('Prescription');
		// $pdf->SetHeaderMargin(20);
		// $pdf->SetTopMargin(10);
		// $pdf->setFooterMargin(10);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		$pdf->SetMargins(15, 10, 15, true);
		$pdf->SetAutoPageBreak(true);
		$pdf->SetAuthor('Author');
		$pdf->SetDisplayMode('real', 'default');
		$pdf->AddPage();
		$html = '';
		$pdf->Image('uploads/sample/logo.jpg', 'C', 10, '50', '30', 'JPG', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
		$pdf->SetXY(0, 50);
		$pdf->writeHTML('<hr style="height:2px;border:none;color:#333;background-color:#333;">', true, false, true, false, '');
		$pdf->Ln(8);
		$pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'solid' => 2, 'color' => array(0, 0, 0)));
		$pdf->SetFillColor(255, 255, 255);
		$pdf->SetFont('helvetica', '', 12, '', 'default', true);
		$subtable = '<table border="1" cellspacing="6" cellpadding="4"><tr><td>a</td><td>b</td></tr><tr><td>c</td><td>d</td></tr></table>';
		$square = '<h4>Vaccination Certificate</h4>
		<table border="1" cellspacing="3" cellpadding="4">
		<tr>
        <th style="border:1px solid #fff;"></th>
        <th style="border:1px solid #fff;"></th>
        <th style="border:1px solid #fff;"></th>
        <th style="border:1px solid #fff;"></th>
    </tr>
    <tr>
		<td colspan="4" style="border:1px solid #fff">
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
			<h1></h1>
		</td>
    </tr>
   
		</table>';
		$pdf->writeHTML($square, true, false, true, false, '');
		$pdf->writeHTMLCell(0, 10, '', '', '<label>Name of Pet: </label>', 'LRTB', 1, 0, true, 'L', true);
		$pdf->writeHTMLCell(0, 10, '', '', 'Breed: ', 'LRTB', 1, 1, true, 'L', true);
		$pdf->writeHTMLCell(0, 10, '', '', 'Species: ', 'LRTB', 1, 1, true, 'L', true);
		$pdf->writeHTMLCell(0, 10, '', '', 'Sex: ', 'LRTB', 1, 1, true, 'L', true);
		$pdf->writeHTMLCell(0, 10, '', '', 'Name of Owners: ', 'LRTB', 1, 1, true, 'L', true);
		$pdf->writeHTMLCell(0, 10, '', '', 'Address: ', 'LRTB', 1, 1, true, 'L', true);
		$pdf->writeHTMLCell(0, 10, '', '', 'Contact Number #: ', 'LRTB', 1, 0, true, 'L', true);
		ob_end_clean();
		$pdf->Output('front_page.pdf', 'I');
	}
}
