<?php

namespace App\Services;

use App\Entity\Invoice;
use App\Entity\ProductInvoice;
use App\FPDF\FPDF;
use App\FPDF\PDF;
use App\Repository\CompanyRepository;
use App\Repository\InvoiceRepository;
use App\Repository\ProductInvoiceRepository;
use App\Repository\ProductRepository;

class InvoiceService extends BaseService
{
    private ProductRepository $productRepository;
    private PDF $pdf;
    private array $invoiceProducts;
    private CompanyRepository $companyRepository;
    private Invoice $invoice;
    private ProductInvoice $productInvoice;
    private ProductInvoiceRepository $productInvoiceRepository;
    private InvoiceRepository $invoiceRepository;
    
    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->pdf = new PDF();
        $this->invoice = new Invoice();
        $this->invoiceRepository = new InvoiceRepository();
        $this->companyRepository = new CompanyRepository();
        $this->productInvoiceRepository = new ProductInvoiceRepository();
        $this->productInvoice = new ProductInvoice();
        
    }
    
    public function generateTableActions($id)
    {
        $actionsMenu = "<td><div class='dropend'>
                        <button type='button' class='btn ' data-bs-toggle='dropdown' aria-expanded='false'>
                               <i class='fa-solid fa-ellipsis-vertical'></i>
                        </button>
                        <ul class='dropdown-menu'>
                                <li><button class='dropdown-item' onclick=printInvoice('$id')>Print</button></li>
                                <li><button class='dropdown-item' onclick=payInvoice('$id')>Mark as paid</button></li>
                                <li><a class='dropdown-item' href='/invoice/edit/$id'>Edit</a></li>
                                <li><button class='dropdown-item' onclick=cancelInvoice('$id')>Cancel</button></li>
                               <li><button class='dropdown-item' onclick=deleteInvoice('$id')>Delete</button></li>
                        </ul>
                </div></td></tr>";
        return $actionsMenu;
    }
    
    public function getExcludedColumns()
    {
        return [
            'id',
            'issuer_company_id',
            'issuer_identity_card',
            'issuer_company_name',
            'issuer_name',
            'issuer_CNP',
            'issuer_CUI',
            'issuer_register_number',
            'customer_phone',
            'customer_address',
            'customer_register_number',
            'discount_percent_value',
            'is_paid',
            'is_printed',
            'created_at',
            'updated_at'
        ];
    }
    
    public function calculateInvoiceAmount($productsList, $companyId, $discountPercentValue)
    {
        $amount = 0;
       
        foreach ($productsList as $product) {
            $sku = $product['sku'];
            $productInfo = $this->productRepository->find(null, ['sku' => $sku, 'company_id' => $companyId], null)[0];
            $productInfo['quantity'] = $product['quantity'];
            $value = ($productInfo['value_without_vat'] + ($productInfo['value_without_vat'] * $productInfo['vat_percent'] / 100)) * $product['quantity'];
            $amount += $value;
            $this->invoiceProducts[] = $productInfo;
        }
        if ($discountPercentValue != 0) {
            return $amount - ($amount * ($discountPercentValue / 100));
        }
        return $amount;
    }
    
    public function populateInvoice()
    {
        
        $products = array();
        $invoiceFields = array();
        $productFields = array();
        foreach ($_POST as $field_name => $field_value) {
            if (str_contains($field_name, 'invoice_')) {
                $field_name = str_replace('invoice_', '', $field_name);
                $invoiceFields[$field_name] = $field_value;
            } else {
                $field_name = str_replace('product_', '', $field_name);
                $productFields[$field_name] = $field_value;
            }
        }
        foreach ($productFields as $name => $value) {
            list($field_name, $index) = explode("_", $name);
            if ($index == '') {
                $index = 1;
            }
            $products[$index][$field_name] = $value;
        }
        
        foreach ($invoiceFields as $property => $value) {
            $propertyName = explode('_', $property);
            $propertyName = array_map('ucfirst', $propertyName);
            $setter = implode('', $propertyName);
            $toSet = 'set' . $setter;
            $this->invoice->$toSet($value);
        }
        $company_id = $this->companyRepository->find(null, ['CUI' => $invoiceFields['issuer_cui']], 'id')[0]['id'];
        $this->invoice->setIssuerCompanyId($company_id);

        $amount = $this->calculateInvoiceAmount($products, $company_id, $invoiceFields['discount_percent_value']);
        $this->invoice->setAmount($amount);
        $type = 'income';
        if ($this->invoice->getIssuerCui() === $this->invoice->getCustomerCUI()){
            $type = 'expense';
        }
        $this->invoice->setType($type);
        
        return $this->invoice;
    }
    
    public function insertProductInvoice()
    {
        
        $invoiceNumber = $this->invoice->getNumber();
        $invoiceId = $this->invoiceRepository->find(null, ['number' => $invoiceNumber], null)[0]['id'];
        
        foreach ($this->invoiceProducts as $product) {
            $this->productInvoice->setInvoiceProductId($invoiceId);
            $this->productInvoice->setProductId($product['id']);
            $this->productInvoice->setProductQuantity($product['quantity']);
            $this->productInvoiceRepository->save($this->productInvoice);
        }
    }
    
    /**
     * @throws \Exception
     */
    public function generateInvoicePDF()
    {
        try {
            ob_clean();
            $this->pdf->generateInvoiceHeader($this->invoice);
            $this->pdf->generateProductsTable($this->invoiceProducts);
            $this->pdf->generateInvoiceAmount($this->invoice->getAmount());
            $this->pdf->generateIssuerDetails($this->invoice);
            $invoiceNumber = $this->invoice->getNumber();
            $this->pdf->Output('F', 'public/invoices/' . $invoiceNumber . '.pdf');
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
            throw new \Exception($message);
        }
    }
    
    public function generateFilters()
    {
        
        isset($_GET['customer_name']) ? $name = $_GET['customer_name'] : $name = '';
        isset($_GET['customer_CUI']) ? $cui = $_GET['customer_CUI'] : $cui = '';
        isset($_GET['amount_start']) ? $amount_start = $_GET['amount_start'] : $amount_start = '';
        isset($_GET['amount_end']) ? $amount_end = $_GET['amount_end'] : $amount_end = '';
        isset($_GET['is_paid']) ? $is_paid = $_GET['is_paid'] : $is_paid = '';
        isset($_GET['due_date_start']) ? $due_date_start = $_GET['due_date_start'] : $due_date_start = '';
        isset($_GET['due_date_end']) ? $due_date_end = $_GET['due_date_end'] : $due_date_end = '';
        $checked = '';
        if($is_paid != ''){
            $checked = 'checked';
        }
        $filters = "<form>
                <h4>Search by:</h4>
                <div class='form-group'>
                    <label for='customer_name'>Customer Name</label>
                    <input type='text' class='form-control' id='customer_name' name='customer_name'
                           placeholder='Customer Name' value='$name'>
                </div>
                <div class='form-group'>
                    <label for='customer_CUI'>Customer CUI</label>
                    <input type='text' class='form-control' id='customer_CUI' name='customer_CUI' placeholder='CUI' value='$cui'>
                </div>
               <div class='form-check'>
                    <input class='form-check-input' type='checkbox' value='1' id='is_paid' name='is_paid' " . $checked . ">
                    <label class='form-check-label' for='is_paid'>Is paid</label>
               </div>
                <label class='form-check-label' for='is_paid'>Amount between</label>
                <div class='input-group'>
                    <input type='number' class='form-control' id='emmited_date' name='amount_start' value='$amount_start'>
                    <div class='input-group-addon'>and</div>
                    <input type='number' class='form-control' id='emitted_end' name='amount_end' value ='$amount_end'>
                </div>
                <label class='form-check-label' for='is_paid'>Due date of payment between</label>
                <div class='input-group'>
                    <input type='date' class='form-control' id='due_date_start' name='due_date_start'
                           value='$due_date_start'>
                    <div class='input-group-addon'>and</div>
                    <input type='date' class='form-control' id='due_date_end' name='due_date_end'
                           value='$due_date_end'>
                </div>
                <button class ='btn btn-primary mt-3 mr-3'>Search</button>  <a  href='/invoice' class ='btn btn-dark mt-3'> Reset </a>
            </form>";
        
        return $filters;
    }
    public function generateCriteria(){
        $criteriaList = null;
        if(!isset($_GET['due_date_start']) && isset($_GET['due_date_end'])){
            $criteriaList['due_date_of_payment'] = $_GET['due_date_end'];
        } elseif (isset($_GET['due_date_start']) && !isset($_GET['due_date_end'])){
            $criteriaList['due_date_of_payment'] = $_GET['due_date_start'];
        } elseif (isset($_GET['due_date_start']) && isset($_GET['due_date_end'])){
            $criteriaList['due_date_of_payment']['between'][] = $_GET['due_date_start'];
            $criteriaList['due_date_of_payment']['between'][] = $_GET['due_date_end'];
        }
        if(!isset($_GET['amount_start']) && isset($_GET['amount_end'])){
            $criteriaList['amount'] = $_GET['amount_end'];
        } elseif (isset($_GET['amount_start']) && !isset($_GET['amount_end'])){
            $criteriaList['amount'] = $_GET['amount_start'];
        } elseif (isset($_GET['amount_start']) && isset($_GET['amount_end'])){
            $criteriaList['amount']['between'][] = $_GET['amount_start'];
            $criteriaList['amount']['between'][] = $_GET['amount_end'];
        }
        if(isset($_GET['is_paid'])){
            $criteriaList['is_paid'] = 1;
        }
        if($_GET['customer_name'] != ''){
            $criteriaList['customer_name'] = $_GET['customer_name'];
        }
        if($_GET['customer_CUI'] != ''){
            $criteriaList['customer_CUI'] = $_GET['customer_CUI'];
        }
        return $criteriaList;
    }
    
    /**
     * @return Invoice
     */
    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
    
    /**
     * @param Invoice $invoice
     */
    public function setInvoice(Invoice $invoice)
    {
        $productInvoice = null;
        $invoiceId = $this->invoiceRepository->find(null,['number'=>$invoice->getNumber()],'id')[0]['id'];
        $invoiceProducts = $this->productInvoiceRepository->find(null,['invoice_product_id' =>  $invoiceId],null,null,);
        
        foreach ($invoiceProducts as $product){
            $productInfo = $this->productRepository->find($product['product_id'])[0];
            $productInfo['quantity'] = $product['product_quantity'];
            $productInvoice[] = $productInfo;
        }
        $this->invoice = $invoice;
        $this->invoiceProducts = $productInvoice;
    }
    
    /**
     * @return array
     */
    public function getInvoiceProducts(): array
    {
        return $this->invoiceProducts;
    }
    
    
}
