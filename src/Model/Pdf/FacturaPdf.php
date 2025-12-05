<?php

namespace App\Model\Pdf;

class FacturaPdf extends PlantillaPdf
{

    public function Header(): void
    {
        if ($this->pageNo() === 1) {
            if (isset($this->logo) and !empty($this->logo)) {
                $this->Image($this->logo, $this->margins['l'], $this->margins['t'], $this->dimensions[0],
                    $this->dimensions[1]);
            }

            //Title
            $this->SetTextColor(0, 0, 0);
            $this->SetFont($this->font, 'B', 20);
            if (isset($this->title) and !empty($this->title)) {
                $this->Cell(0, 5, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($this->title, self::ICONV_CHARSET_INPUT)), 0, 1, 'R');
            }
            $this->SetFont($this->font, '', 9);
            $this->Ln(5);

            $lineHeight = 5;
            //Calculate position of strings
            $this->SetFont($this->font, 'B', 9);

            $positionX = $this->document['w'] - $this->margins['l'] - $this->margins['r']
                - max($this->GetStringWidth(mb_strtoupper("number", self::ICONV_CHARSET_INPUT)),
                    $this->GetStringWidth(mb_strtoupper("date", self::ICONV_CHARSET_INPUT)),
                    $this->GetStringWidth(mb_strtoupper("due", self::ICONV_CHARSET_INPUT)))
                - max($this->GetStringWidth(mb_strtoupper($this->reference, self::ICONV_CHARSET_INPUT)),
                    $this->GetStringWidth(mb_strtoupper($this->date, self::ICONV_CHARSET_INPUT)));

            //Number
            if (!empty($this->reference)) {
                $this->Cell($positionX, $lineHeight);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("number", self::ICONV_CHARSET_INPUT) . ':'), 0, 0,
                    'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineHeight, $this->reference, 0, 1, 'R');
            }
            //Date
            $this->Cell($positionX, $lineHeight);
            $this->SetFont($this->font, 'B', 9);
            $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
            $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("date", self::ICONV_CHARSET_INPUT)) . ':', 0, 0, 'L');
            $this->SetTextColor(50, 50, 50);
            $this->SetFont($this->font, '', 9);
            $this->Cell(0, $lineHeight, $this->date, 0, 1, 'R');
            //Pedido
            if (!empty($this->pedido)) {
                $this->Cell($positionX, $lineHeight);
                $this->SetFont($this->font, 'B', 9);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("pedido", self::ICONV_CHARSET_INPUT) . ':'), 0, 0,
                    'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineHeight, $this->pedido, 0, 1, 'R');
            } else
                $this->ln(5);

            //Time
            if (!empty($this->time)) {
                $this->Cell($positionX, $lineHeight);
                $this->SetFont($this->font, 'B', 9);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("time", self::ICONV_CHARSET_INPUT)) . ':', 0, 0,
                    'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineHeight, $this->time, 0, 1, 'R');
            }
            //Due date
            if (!empty($this->due)) {
                $this->Cell($positionX, $lineHeight);
                $this->SetFont($this->font, 'B', 9);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("due", self::ICONV_CHARSET_INPUT)) . ':', 0, 0, 'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineHeight, $this->due, 0, 1, 'R');
            }
            //Numero abonado
            if (!empty($this->abonado)) {
                $this->Cell($positionX, $lineHeight);
                $this->SetFont($this->font, 'B', 9);
                $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("Abonado", self::ICONV_CHARSET_INPUT)) . ':', 0, 0, 'L');
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 9);
                $this->Cell(0, $lineHeight, $this->abonado, 0, 1, 'R');
            } else
                $this->ln(5);

            if ($this->PageNo() == 1) {
                $width = ($this->document['w'] - $this->margins['l'] - $this->margins['r']) / 2;
                if (isset($this->flipflop)) {
                    $to = $this->to;
                    $from = $this->from;
                    $this->to = $from;
                    $this->from = $to;
                }

                if ($this->displayToFrom === true) {
                    //Information
                    $this->Ln(5);
                    $this->SetTextColor(50, 50, 50);
                    $this->SetFont($this->font, 'B', 9);
                    $this->Cell($width, $lineHeight, $this->from[0] ?? 0, 0, 0, 'L');
                    $this->SetFont($this->font, '', 9);
                    $this->Cell(0, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $this->to[0] ?? 0), 0, 1, 'L');
                    for ($i = 1, $iMax = max(count($this->from), count($this->to)); $i < $iMax; $i++) {
                        // avoid undefined error if TO and FROM array lengths are different
                        if (!empty($this->from[$i]) || !empty($this->to[$i])) {
                            if ($i == 1) {
                                $this->SetTextColor(50, 50, 50);
                                $this->SetFont($this->font, 'B', 9);
                                $this->Cell($width, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, empty($this->from[$i]) ? '' : $this->from[$i]), 0, 0, 'L');
                                $this->SetFont($this->font, '', 9);
                                $this->SetTextColor(50, 50, 50);
                            } else
                                $this->Cell($width, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, empty($this->from[$i]) ? '' : $this->from[$i]), 0, 0, 'L');

                            $this->Cell(0, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, empty($this->to[$i]) ? '' : $this->to[$i]), 0, 0, 'L');
                        }
                        $this->Ln(5);
                    }
                    $this->Ln(-6);
                    $this->Ln(5);
                } else
                    $this->Ln(-10);

            }
            //Custom Headers
            if (count($this->customHeaders) > 0) {
                foreach ($this->customHeaders as $customHeader) {
                    $this->Cell($positionX, $lineHeight);
                    $this->SetFont($this->font, 'B', 9);
                    $this->SetTextColor($this->color[0], $this->color[1], $this->color[2]);
                    $this->Cell(32, $lineHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($customHeader['title'], self::ICONV_CHARSET_INPUT)) . ':', 0, 0, 'L');
                    $this->SetTextColor(50, 50, 50);
                    $this->SetFont($this->font, '', 9);
                    $this->Cell(0, $lineHeight, $customHeader['content'], 0, 1, 'R');
                }
            }
        }

        //Table header
        if (!isset($this->productsEnded)) {
            $width_other = ($this->document['w'] - $this->margins['l'] - $this->margins['r'] - $this->firstColumnWidth - ($this->columns * $this->columnSpacing)) / ($this->columns - 1);
            $this->SetTextColor(50, 50, 50);
            $this->Ln(5);
            $this->SetFont($this->font, 'B', 9);

            // Title Albarán
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("Albarán", self::ICONV_CHARSET_INPUT)), 0, 0, 'L', 0);

            // Title fecha
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("Fecha", self::ICONV_CHARSET_INPUT)), 0, 0, 'L', 0);

            // Title description
            $this->Cell(1, 10, '', 0, 0, 'L', 0);
            $this->Cell($this->firstColumnWidth, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("Concepto", self::ICONV_CHARSET_INPUT)),
                0, 0, 'L', 0);
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);

            // Title Total
            $this->Cell($this->columnSpacing, 10, '', 0, 0, 'L', 0);
            $this->Cell($width_other, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper("total", self::ICONV_CHARSET_INPUT)), 0, 0, 'C', 0);

            $this->Ln();
            $this->SetLineWidth(0.3);
            $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
            $this->Line($this->margins['l'], $this->GetY(), $this->document['w'] - $this->margins['r'], $this->GetY());
            $this->Ln(2);
        } else {
            $this->Ln(12);
        }
    }

    /**
     * Línea de producto
     * @param [type] $item        [description]
     * @param [type] $description [description]
     * @param [type] $quantity    [description]
     * @param [type] $vat         [description]
     * @param [type] $price       [description]
     * @param [type] $discount    [description]
     * @param [type] $total       [description]
     */
    public function addItem($item, $description, $quantity, $vat, $price, $discount, $total, $albaran = null, $date = null): void
    {
        $p['item'] = $item;
        $p['description'] = $this->br2nl($description);
        $p['albaran'] = $albaran;
        $p['date'] = $date;
        $p['total'] = $total;

        $this->items[] = $p;
    }

    private function br2nl($string): array|string|null
    {
        $string = $string ?? '';
        return preg_replace('/<br(\s*)?\/?>/i', "\n", $string);
    }

    private function recalculateColumns(): void
    {
        $this->columns = 4;

        if (isset($this->vatField))
            $this->columns += 1;

        if (isset($this->discountField))
            $this->columns += 1;
    }

    public function Body(): void
    {
        $width_other = ($this->document['w'] - $this->margins['l'] - $this->margins['r'] - $this->firstColumnWidth - ($this->columns * $this->columnSpacing)) / ($this->columns - 1);
        $cellHeight = 8;
        $bgColor = (1 - $this->columnOpacity) * 255;
        if ($this->items) {
            foreach ($this->items as $item) {
                if ((empty($item['item'])) || (empty($item['description']))) {
                    $this->Ln($this->columnSpacing);
                }

                $cHeight = $cellHeight;
                $this->SetTextColor(50, 50, 50);
                $this->SetFont($this->font, '', 8);
                $this->SetFillColor($bgColor, $bgColor, $bgColor);
                $this->Cell(1, $cHeight, '', 0, 0, 'L', 1);

                // Albarán
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,
                    $item['albaran']), 0, 0, 'L', 1);

                // Fecha
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,
                    $item['date']), 0, 0, 'L', 1);

                // Concepto
                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($this->firstColumnWidth, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,
                    $item['description']), 0, 0, 'L', 1);

                $this->Cell($this->columnSpacing, $cHeight, '', 0, 0, 'L', 0);
                $this->Cell($width_other, $cHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B,
                    $this->price($item['total'])), 0, 0, 'C', 1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $badgeX = $this->getX();
        $badgeY = $this->getY();

        //Add totals
        if ($this->totals) {
            foreach ($this->totals as $total) {
                $this->SetTextColor(50, 50, 50);
                $this->SetFillColor($bgColor, $bgColor, $bgColor);
                $this->Cell(1 + $this->firstColumnWidth, $cellHeight, '', 0, 0, 'L', 0);
                for ($i = 0; $i < $this->columns - 3; $i++) {
                    $this->Cell($width_other, $cellHeight, '', 0, 0, 'L', 0);
                    $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                }
                $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                if ($total['colored']) {
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
                }
                $this->SetFont($this->font, 'b', 8);
                $this->Cell(1, $cellHeight, '', 0, 0, 'L', 1);
                $this->Cell($width_other - 1, $cellHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $total['name']), 0, 0, 'L',
                    1);
                $this->Cell($this->columnSpacing, $cellHeight, '', 0, 0, 'L', 0);
                $this->SetFont($this->font, 'b', 8);
                $this->SetFillColor($bgColor, $bgColor, $bgColor);
                if ($total['colored']) {
                    $this->SetTextColor(255, 255, 255);
                    $this->SetFillColor($this->color[0], $this->color[1], $this->color[2]);
                }
                $this->Cell($width_other, $cellHeight, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, $total['value']), 0, 0, 'C', 1);
                $this->Ln();
                $this->Ln($this->columnSpacing);
            }
        }
        $this->productsEnded = true;
        $this->Ln();

        //Badge
        if ($this->badge) {
            $badge = ' ' . mb_strtoupper($this->badge, self::ICONV_CHARSET_INPUT) . ' ';
            $resetX = $this->getX();
            $resetY = $this->getY();
            $this->setXY($badgeX, $badgeY + 15);
            $this->SetLineWidth(0.4);
            $this->SetDrawColor($this->badgeColor[0], $this->badgeColor[1], $this->badgeColor[2]);
            $this->setTextColor($this->badgeColor[0], $this->badgeColor[1], $this->badgeColor[2]);
            $this->SetFont($this->font, 'b', 15);
            $this->Rotate(10, $this->getX(), $this->getY());
            $this->Rect($this->GetX(), $this->GetY(), $this->GetStringWidth($badge) + 2, 10);
            $this->Write(10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_B, mb_strtoupper($badge, self::ICONV_CHARSET_INPUT)));
            $this->Rotate(0);
            if ($resetY > $this->getY() + 20) {
                $this->setXY($resetX, $resetY);
            } else {
                $this->Ln(18);
            }
        }
        $this->setXY(15, 230);
        //Add information
        foreach ($this->addText as $text) {
            if ($text[0] == 'title') {
                $this->SetFont($this->font, 'b', 9);
                $this->SetTextColor(50, 50, 50);
                $this->Cell(0, 10, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, mb_strtoupper($text[1], self::ICONV_CHARSET_INPUT)), 0, 0, 'L', 0);
                $this->Ln();
                $this->SetLineWidth(0.3);
                $this->SetDrawColor($this->color[0], $this->color[1], $this->color[2]);
                $this->Line($this->margins['l'], $this->GetY(), $this->document['w'] - $this->margins['r'],
                    $this->GetY());
                $this->Ln(4);
            }
            if ($text[0] == 'paragraph') {
                $this->SetTextColor(80, 80, 80);
                $this->SetFont($this->font, '', 8);
                $this->MultiCell(0, 4, iconv(self::ICONV_CHARSET_INPUT, self::ICONV_CHARSET_OUTPUT_A, $text[1]), 0, 'L', 0);
                $this->Ln(4);
            }
        }
    }
}
