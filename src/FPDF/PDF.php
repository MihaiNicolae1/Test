<?php

namespace App\FPDF;

use App\Entity\Invoice;

class PDF extends FPDF
{
    var $widths;
    var $aligns;
    
    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths = $w;
    }
    
    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns = $a;
    }
    
    function Row($data)
    {
        //Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++)
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        $h = 5 * $nb;
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        //Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x = $this->GetX();
            $y = $this->GetY();
            //Draw the border
            $this->Rect($x, $y, $w, $h);
            //Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);
            //Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }
        //Go to the next line
        $this->Ln($h);
    }
    
    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }
    
    function NbLines($w, $txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw =& $this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
    
    public function generateProductsTable($products)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetWidths(array(15, 25, 95, 15, 15, 15, 15));
        $header = ['No.','Sku', 'Name', 'Qty', 'Unit', 'Value no VAT', 'VAT percent'];
        $this->Row($header);
        
        $this->SetFont('Arial', '', 10);
        foreach ($products as $index => $product) {
            $counter = $index + 1;
            $this->Row([
                $counter,
                $product['sku'],
                $product['name'],
                $product['quantity'],
                $product['measure_unit'],
                $product['value_without_vat'],
                $product['vat_percent']
            ]);
        }
    }
    
    public function generateInvoiceHeader(Invoice $invoice)
    {
        
        $this->AddPage();
        $this->SetFont('Arial', 'B', 20);
        
        $this->Cell(71, 10, '', 0, 0);
        $this->Cell(65, 5, 'Invoice', 0, 0);
        $this->Cell(65, 10, '', 0, 1);
        
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(71, 5, 'From:', 0, 0);
        $this->Cell(59, 5, '', 0, 0);
        $this->Cell(59, 5, 'To:', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        
        $this->Cell(130, 5, 'Name: ' .  $invoice->getIssuerCompanyName(), 0, 0);
        $this->Cell(25, 5, 'Name: ' .  $invoice->getCustomerName(), 0, 1);
        
        $this->Cell(130, 5, 'CUI: ' . $invoice->getIssuerCui(), 0, 0);
        $this->Cell(25, 5, 'CUI: ' .  $invoice->getCustomerCUI(), 0, 1);
        
        $this->Cell(130, 5, 'Register Number: ' .  $invoice->getIssuerRegisterNumber(), 0, 0);
        $this->Cell(25, 5, 'Register Number: ' .  $invoice->getCustomerRegisterNumber(), 0, 1);
        
        $this->Cell(130, 5, 'Emitted on: ' .  $invoice->getEmmitedDate(), 0, 0);
        $this->Cell(25, 5, 'Address: ' .  $invoice->getCustomerAddress(), 0, 1);
        
        $this->Cell(130, 5, 'Due date of payment: ' .  $invoice->getDueDateOfPayment(), 0, 0);
        $this->Cell(25, 5, 'Phone: ' .  $invoice->getCustomerPhone(), 0, 1);
        
        $this->Cell(50, 10, '', 0, 1);
    }
    
    public function generateInvoiceAmount($amount)
    {
        $this->Cell(150 ,6,'',0,0);
        $this->Cell(15 ,6,'Amount',0,0);
        $this->Cell(30 ,6, $amount,1,1,'R');
       
    }
    
    public function Footer()
    {
        // Go to 1.5 cm from bottom
        $this->SetY(-15);
        // Select Arial italic 8
        $this->SetFont('Arial','I',8);
        // Print centered page number
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
    
    public function generateIssuerDetails(Invoice $invoice){
        $this->SetY(-45);
        $this->SetFont('Arial','B',10);
        $this->Cell(25, 5, 'Issued by: ', 0, 1);
        $this->SetFont('Arial','',10);
    
        $this->Cell(25, 5, 'Name: ' . $invoice->getIssuerName(), 0, 1);
        $this->Cell(25, 5, 'CNP: ' . $invoice->getIssuerCNP(), 0, 1);
        $this->Cell(25, 5, 'ID Card: ' . $invoice->getIssuerIdentityCard(), 0, 1);
    
    }
}
