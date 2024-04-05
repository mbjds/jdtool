<?php

namespace JDCustom\pdf;

class makePDF extends \TCPDF
{
    private string $voucherID;
    private array $bcStyle;

    public function __construct(int|string $voucherID)
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor('tunel.aero');
        $this->SetTitle('Twój voucher');
        $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $this->SetHeaderMargin(0);
        $this->SetFooterMargin(0);
        $this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $this->setFontSubsetting(true);
        $this->setFont('dejavusans', '', 14);
        $this->AddPage();
        $this->bcStyle = [
            'border' => 2,
            'vpadding' => 2,
            'hpadding' => 2,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => [255, 255, 255],
            'module_width' => 1,
            'module_height' => 1,
        ];
        $this->voucherID = $voucherID;
        //    $this->voucherID = '22-9324-22';
    }

    public function Header()
    {
        $this->SetAutoPageBreak(false, 0);
        $img_file = 'https://t.whooooops.com/wp-content/uploads/2021/12/bilet-maxfly-scaled.jpg';
        $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        $this->setPageMark();
    }

    public function Footer()
    {
        $this->SetFont('dejavusans', 'I', 9);
        $this->writeHTMLCell($this->getPageWidth(), 20, 0, 272, '<hr style="height: 2px">', 0, 0, false, true, 'center', false);

        $this->writeHTMLCell(0, 0, 5, 275, '<img width="72px" src="https://t.whooooops.com/wp-content/uploads/2021/08/Eska_odlot.png">', 0, 0, false, true, 'center', false);
        $this->writeHTMLCell(0, 0, 90, 275, '<img width="72px" src="https://t.whooooops.com/wp-content/uploads/2021/02/Logomaxflynowekwadrat.png">', 0, 0, false, true, 'center', false);
        $this->writeHTMLCell(130, 0, 72, 280, '<span style=" text-align: right; "><b>Voucher nr:</b> '.$this->voucherID.' </span>', 0, 0, false, true, 'center', false);
        $this->writeHTMLCell(130, 0, 72, 285, '<span style=" text-align: right; "><b>Ważny do:</b> 01-02-2024</span>', 0, 0, false, true, 'center', false);
        $this->writeHTMLCell(50, 0, 35, 280, '<span style="line-height: 19px; font-size: 12px; text-align: left; font-weight: 700 ">Zarezerwuj na:</span>', 0, 0, false, true, 'center', false);
        $this->writeHTMLCell(50, 0, 35, 287, '<span style="line-height: 19px; font-size: 14px; text-align: left; "><b>www.tunel.aero</b></span>', 0, 0, false, true, 'center', false);
    }

    public function setBC(): void
    {
        $this->setY(220);
        $this->write2DBarcode($this->voucherID, 'QRCODE,L', 15, 183, 35, 35, $this->bcStyle, 'N');
    }

    public function setProductName(): void
    {
        $this->setFontSize(18);
        $name = 'Starter dla dorosłego - 2 Loty w Tunelu Maxfly - od poniedziałku do piątku + Film';
        $ts = <<<EOD
            <div style="display: block; width: 60%; height: 170px !important;  padding: 2px; text-align: center; ">
            <span style="height: 120px;">{$name}</span>
            
            </div>
            EOD;

        $this->writeHTMLCell(139, 0, 69, 192, $ts, 0, 0, false, true, 'right');
    }

    public function setDedication(): void
    {
        $this->setFontSize(16);
        $dedication = 'Na 40 urodziny dla kochanego mężą';
        $t = <<<EOD
<div style="width: 100%; max-height:10px; padding: 2px; text-align: center;">
<span style="height: 20px;">{$dedication}</span>
</div>
EOD;

        $this->writeHTMLCell(0, 20, 15, 235, $t, 0, 0, false, true, 'center');
    }

    public function generatePDF($outputMode = 'S')
    {
        $this->setProductName();
        $this->setDedication();
        $this->setBC();

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="document.pdf"');

        return $this->Output('document.pdf', $outputMode);
    }
}
