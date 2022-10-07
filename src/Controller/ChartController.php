<?php

namespace App\Controller;

use App\Repository\InvoiceRepository;
use App\Services\LoginService;

class ChartController
{
    private InvoiceRepository $invoiceRepository;
    public function __construct(){
        $this->invoiceRepository = new InvoiceRepository();
    }
    public function getIndex(){
        require_once 'templates/chart/index.html';
    }
    
    public function getCashflow(){
        require_once 'templates/chart/incomeFlow.html';
    }
    
   public function getPaid(){
        require_once 'templates/chart/paid.html';
   }

}
