<?php

require('fpdf.php');

define('LN', 5);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Times', '', 11);

$pdf->Cell(0, LN, 'Hello World Times', 0, 1);
$pdf->Cell(0, LN, 'Hello World Times 2', 0, 1);
$pdf->Cell(0, LN, 'One more timE', 0, 1);

$pdf->Output();
