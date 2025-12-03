<?php


namespace App\Model\Pdf;
use Fpdf;

class ListadoPdf extends Fpdf\Fpdf
{
    function title(string $string = ''): void
    {
        $this->SetFont('Arial', '', 20);
        $this->Cell(strlen($string), 30, $string, 0, 0, 'L');
        $this->resetFont();
        $this->Ln(30);
    }

    function resetFont($font = 'Arial', $style = '', $size = 8): void
    {
        $this->SetFont($font, $style, $size);
    }


    // Better table
    function ImprovedTable($header, $data): void
    {
        // Column widths
        $w = array(15, 17, 12, 80, 20, 15, 15, 15);

        $this->resetFont();
        // Header
        $this->Cell($w[0], 7, $header[0], 'B', 0, 'L');
        $this->Cell($w[1], 7, $header[1], 'B', 0, 'L');
        $this->Cell($w[2], 7, $header[2], 'B', 0, 'L');
        $this->Cell($w[3], 7, $header[3], 'B', 0, 'L');
        $this->Cell($w[4], 7, $header[4], 'B', 0, 'L');
        $this->Cell($w[5], 7, $header[5], 'B', 0, 'R');
        $this->Cell($w[6], 7, $header[6], 'B', 0, 'R');
        $this->Cell($w[7], 7, $header[7], 'B', 0, 'R');

        $this->resetFont();

        $this->Ln(10);

        // Data
        foreach ($data as $key => $row) {
            if ($key && $key % 36 == 0) {
                $this->AddPage();
                $this->Cell($w[0], 7, $header[0], 'B', 0, 'L');
                $this->Cell($w[1], 7, $header[1], 'B', 0, 'L');
                $this->Cell($w[2], 7, $header[2], 'B', 0, 'L');
                $this->Cell($w[3], 7, $header[3], 'B', 0, 'L');
                $this->Cell($w[4], 7, $header[4], 'B', 0, 'L');
                $this->Cell($w[5], 7, $header[5], 'B', 0, 'R');
                $this->Cell($w[6], 7, $header[6], 'B', 0, 'R');
                $this->Cell($w[7], 7, $header[7], 'B', 0, 'R');
                $this->Ln(10);
            }
            if ($key === array_key_last($data)) {
                $this->Ln(10);

            }
            $this->Cell($w[0], 6, $row[0]);
            $this->Cell($w[1], 6, $row[1]);
            $this->Cell($w[2], 6, $row[2]);
            $this->Cell($w[3], 6, $row[3]);
            if ($key === array_key_last($data))
                $this->SetFont('Arial', 'B', 8);

            $this->Cell($w[4], 6, $row[4]);

            if ($key === array_key_last($data))
                $this->resetFont();

            $this->Cell($w[5], 6, number_format($row[5], 2, ",", "."), 0, 0, 'R');
            $this->Cell($w[6], 6, number_format($row[6], 2, ",", "."), 0, 0, 'R');
            $this->Cell($w[7], 6, number_format($row[7], 2, ",", "."), 0, 0, 'R');
            $this->Ln();
        }

        // Closing line
        //$this->Cell(array_sum($w), 0, '', 'T');
    }

    function Footer(): void
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Print centered page number
        setlocale(LC_TIME, "es_ES");
        // TODO: DEPRECATED Cambiar strftime?
        $date = strftime("%A, %d de %B de %Y");
        $this->Cell(0, 10, $date, 0, 0, 'L');
        $this->Cell(0, 10, mb_convert_encoding('Página ' . $this->PageNo() . ' de {nb}', 'ISO-8859-1', 'UTF-8'), 0, 0, 'R');
    }

}
