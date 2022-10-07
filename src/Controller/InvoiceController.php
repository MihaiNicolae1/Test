<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Entity\ProductInvoice;
use App\Repository\CompanyRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductInvoiceRepository;
use App\Repository\ProductRepository;
use App\Services\FlashService;
use App\Services\InvoiceService;
use App\Services\LoginService;
use App\Services\PaginationService;
use mysql_xdevapi\Exception;
use const App\Services\FLASH_ERROR;
use const App\Services\FLASH_SUCCESS;

class InvoiceController
{
    const RESULTS_PER_PAGE = 10;
    private CompanyRepository $companyRepository;
    private InvoiceRepository $invoiceRepository;
    private Invoice $invoice;
    private InvoiceService $invoiceService;
    private ProductInvoice $productInvoice;
    private ProductInvoiceRepository $productInvoiceRepository;
    private ProductRepository $productRepository;
    
    public function __construct()
    {
        LoginService::handleLogin();
        $this->companyRepository = new CompanyRepository();
        $this->invoiceRepository = new InvoiceRepository();
        $this->invoiceService = new InvoiceService();
        $this->invoice = new Invoice();
        $this->productInvoice = new ProductInvoice();
        $this->productInvoiceRepository = new ProductInvoiceRepository();
        $this->productRepository = new ProductRepository();
    }
    
    public function getIndex()
    {
        
        $company_id = LoginService::getCompanyId();
        $criteriaList = $this->invoiceService->generateCriteria();
        
        if (isset($company_id))
            $criteriaList['issuer_company_id'] = $company_id;
        
        $resultCount = $this->invoiceRepository->count($criteriaList);
        
        $paginationService = new PaginationService($resultCount, self::RESULTS_PER_PAGE);
        $limits = ['first_result' => $paginationService->getPageFirstResult(), 'results_per_page' => self::RESULTS_PER_PAGE];
        
        $invoices = $this->invoiceRepository->find(null, $criteriaList, null, $limits);
        
        $filters = $this->invoiceService->generateFilters();
        $table = $this->invoiceService->createTable($invoices);
        $pagination = $this->invoiceService->createPagination($paginationService->getNumberOfPage());
        
        require_once 'templates/invoice/index.html';
    }
    
    public function deleteIndex($invoiceId)
    {
        try {
            $invoiceNumber = $this->invoiceRepository->find($invoiceId)[0]['number'];
            $invoiceFile = 'public/invoices/' . $invoiceNumber . '.pdf';
            if (file_exists($invoiceFile)) {
                unlink($invoiceFile);
            }
            $this->invoiceRepository->softDelete($invoiceId);
            $this->productInvoiceRepository->softDelete($invoiceId);
            $message = 'Record was deleted successfully!';
            $type = FLASH_SUCCESS;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('account_delete', $message, $type);
        header('/invoice');
        exit();
    }
    
    public function getNew()
    {
        
        $companyName = '';
        $companyCUI = '';
        $companyRegisterNumber = '';
        
        $company_id = LoginService::getCompanyId();
        
        if ($company_id !== null) {
            $company = $this->companyRepository->find($company_id)[0];
            $companyName = $company['name'];
            $companyCUI = $company['CUI'];
            $companyRegisterNumber = $company['register_number'];
        }
        
        require_once 'templates/invoice/new.html';
        
    }
    
    public function postNew()
    {
        if (empty($_POST)) {
            FlashService::flash('invoice_empty', 'Form can not be empty!!', FLASH_ERROR);
            header('Location: /invoice/new', true);
            exit(0);
        }
        try {
            $this->invoice = $this->invoiceService->populateInvoice();
            $this->invoiceRepository->save($this->invoice);
            $this->invoiceService->insertProductInvoice();
            $this->invoiceService->generateInvoicePDF();
            $message = 'Invoice created!';
            $type = FLASH_SUCCESS;
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('invoice_flash', $message, $type);
        header('Location: /invoice', true);
        exit(0);
    }
    
    public function putPrint($invoiceId)
    {
        ob_clean();
        try {
            $invoice = $this->invoiceRepository->find($invoiceId)[0];
            if ($invoice['is_printed'] == 0) {
                $this->invoiceRepository->update($invoiceId, ['is_printed' => 1]);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $invoice['number'];
    }
    
    public function putPay($invoiceId)
    {
        ob_clean();
        try {
            $invoice = $this->invoiceRepository->find($invoiceId)[0];
            $response = 0;
            if ($invoice['is_paid'] == 0) {
                $this->invoiceRepository->update($invoiceId, ['is_paid' => 1]);
                $response = 1;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        return $response;
    }
    
    public function postCancel($invoiceId)
    {
        try {
            $invoiceFields = $this->invoiceRepository->find($invoiceId)[0];
            $cancelInvoice = new Invoice();
            $excludeProperties = ['id', 'created_at', 'updated_at', 'number', 'amount', 'is_paid', 'is_printed'];
            $cancelAmount = 0 - $invoiceFields['amount'];
            $cancelInvoice->setAmount($cancelAmount);
            
            foreach ($invoiceFields as $property => $value) {
                if (in_array($property, $excludeProperties))
                    continue;
                $propertyName = explode('_', $property);
                $propertyName = array_map('ucfirst', $propertyName);
                $setter = implode('', $propertyName);
                $toSet = 'set' . $setter;
                $cancelInvoice->$toSet($value);
            }
            
            $this->invoiceRepository->save($cancelInvoice);
            $products = $this->productInvoiceRepository->find(null, ['invoice_product_id' => $invoiceFields['id']]);
            $cancelId = $this->invoiceRepository->find(null, ['number' => $cancelInvoice->getNumber()], 'id')[0]['id'];
            
            foreach ($products as $index => $product) {
                $this->productInvoice->setProductQuantity($product['product_quantity']);
                $this->productInvoice->setProductId($product['product_id']);
                $this->productInvoice->setInvoiceProductId($cancelId);
                $this->productInvoiceRepository->save($this->productInvoice);
            }
            $this->invoiceService->setInvoice($cancelInvoice);
            $this->invoiceService->generateInvoicePDF();
            $response = 'Invoice canceled successfully!';
            $type = FLASH_SUCCESS;
        } catch (\Exception $e) {
            $response = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('invoice_canceled', $response, $type);
        header('Location: /invoice', true);
        exit(0);
    }
    
    public function getEdit($invoiceId)
    {
        $invoiceInfo = $this->invoiceRepository->find($invoiceId)[0];
        if (!empty($invoiceInfo))
            require_once 'templates/invoice/edit.html';
        
        FlashService::flash('invoice_does_not_exit', 'Invoice does not exist!',FLASH_ERROR);
        header('Location: /invoice', true);
        exit(0);
    }
    
    public  function postEdit($invoiceId){
        if (empty($_POST)) {
            FlashService::flash('edit_invoice_empty', 'Form can not be empty!!', FLASH_ERROR);
            header('Location: /invoice/new', true);
            exit(0);
        }
        try {
            $excludeProperties = ['id', 'created_at', 'updated_at', 'number', 'is_paid', 'is_printed'];
            $changedValues = null;
            $oldInvoice = $this->invoiceRepository->find($invoiceId)[0];
            $this->invoice = $this->invoiceService->populateInvoice();
            $this->invoice->setNumber($oldInvoice['number']);
            $this->productInvoiceRepository->softDelete($invoiceId);
            $this->invoiceService->insertProductInvoice();
            
            foreach ($oldInvoice as $field => $value){
                if(in_array($field, $excludeProperties))
                    continue;
                $toGet = explode('_', $field);
                $toGet = array_map('ucfirst', $toGet);
                $toGet = implode('', $toGet);
                $toGet = 'get' . $toGet;
                $newInvoiceValue = $this->invoice->$toGet();
                if($newInvoiceValue != $value){
                    $changedValues[$field] = $newInvoiceValue;
                }
            }
            $this->invoiceRepository->update($invoiceId, $changedValues);
            $this->invoiceService->generateInvoicePDF();
            
            $message = 'Invoice updated!';
            $type = FLASH_SUCCESS;
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('invoice_flash', $message, $type);
        header('Location: /invoice', true);
        exit(0);
    }
    
    public function getInfo($invoiceId)
    {
        ob_clean();
        $productList = null;
        $invoiceInfo = $this->invoiceRepository->find($invoiceId)[0];
        $productsInfo = $this->productInvoiceRepository->find(null,['invoice_product_id' => $invoiceId]);
        foreach ($productsInfo as $productInfo){
            $product = $this->productRepository->find($productInfo['product_id'])[0];
            $product['quantity'] = $productInfo['product_quantity'];
            $productList[] = $product;
        }
        if (!empty($invoiceInfo)) {
            $invoiceInfo['products'] = $productList;
            return json_encode($invoiceInfo);
        }
        http_response_code(404);
    }
    
    public function getIncome(){
        $company_id = LoginService::getCompanyId();
        $income = ['type' => 'income'];
        $expense = ['type' => 'expense'];
        
        if($company_id != null){
            $income = array_merge($income, ['issuer_company_id' => $company_id]);
            $expense = array_merge($expense, ['issuer_company_id' => $company_id]);
        }
        $dateIntervals['emmited_date']['between'][] = date('Y-m-01 H:i:s');
        $dateIntervals['emmited_date']['between'][] = date('Y-m-t H:i:s');
        
        $income = array_merge($dateIntervals, $income);
        $expense = array_merge($dateIntervals, $expense);
        
        $incomes = $this->invoiceRepository->count($income);
        $expenses = $this->invoiceRepository->count($expense);
        ob_clean();
        $response = ['incomes' => $incomes, 'expenses' => $expenses];
        return json_encode($response);
    }
    
    public function getPaid(){
        
        $company_id = LoginService::getCompanyId();
        $paid = ['is_paid' => 1];
        $unpaid = ['is_paid' => 0];
    
        if($company_id != null){
            $paid = array_merge($paid, ['issuer_company_id' => $company_id]);
            $unpaid = array_merge($unpaid, ['issuer_company_id' => $company_id]);
        }
        $dateIntervals['emmited_date']['between'][] = date('Y-m-01 H:i:s');
        $dateIntervals['emmited_date']['between'][] = date('Y-m-t H:i:s');
    
        $paid = array_merge($dateIntervals, $paid);
        $unpaid = array_merge($dateIntervals, $unpaid);
    
        $paids = $this->invoiceRepository->count($paid);
        $unpaids = $this->invoiceRepository->count($unpaid);
        ob_clean();
        $response = ['paid' => $paids, 'unpaid' => $unpaids];
        return json_encode($response);
    }
}
