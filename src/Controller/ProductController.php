<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\CompanyRepository;
use App\Repository\ProductRepository;
use App\Services\FlashService;
use App\Services\LoginService;
use App\Services\PaginationService;
use App\Services\ProductService;
use const App\Services\FLASH_ERROR;
use const App\Services\FLASH_SUCCESS;

class ProductController
{
    private Product $product;
    private ProductRepository $productRepository;
    private ProductService $productService;
    private CompanyRepository $companyRepository;
    const RESULTS_PER_PAGE = 10;
    
    public function __construct(){
        $this->product = new Product();
        $this->productRepository = new ProductRepository();
        $this->productService = new ProductService();
        $this->companyRepository = new CompanyRepository();
    }
    
    public function getIndex(){
    
        $this->checkCompany();
        $company_id = LoginService::getCompanyId();
        if(isset($company_id)){
            $productsCount = $this->productRepository->count(['company_id' => $company_id]);
        } else {
            $productsCount =  $this->productRepository->count();
        }
      
        $paginationService = new PaginationService($productsCount, self::RESULTS_PER_PAGE);
        $first_result = $paginationService->getPageFirstResult();
        $page_number = $paginationService->getNumberOfPage();
        
        $limits = ['first_result'=>$first_result, 'results_per_page'=>self::RESULTS_PER_PAGE];
    
        if(isset($company_id)){
            $companyProducts = $this->productRepository->find(null,['company_id' => $company_id], null, $limits);
        } else {
            $companyProducts = $this->productRepository->findAll($limits);
        }
       
        $table = $this->productService->createTable($companyProducts);
        $pagination = $this->productService->createPagination($page_number);
        require_once 'templates/product/index.html';
        
    }
    
    public function getAdd(){
        $this->checkCompany();
        require_once 'templates/product/add.html';
    }
    
    public function postIndex(){
        $this->checkCompany();
        if(empty($_POST))
            return 'Please fill in the form';
        
        $message = 'Product added successfully';
        $type = FLASH_SUCCESS;
        try {
            $this->productRepository->insertBySku($_SESSION['id'],$_POST['sku']);
            foreach ($_POST as $property => $value) {
                $propertyName = explode('_', $property);
                $propertyName = array_map('ucfirst', $propertyName);
                $setter = implode('', $propertyName);
                $toSet = 'set' . $setter;
                $this->product->$toSet($value);
            }
            $this->product->setCompanyId($_SESSION['id']);
            $this->productRepository->save($this->product);
           
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('product_added',$message, $type);
        header('Location: /product', true);
        exit();
    }
    
    public function deleteIndex($productId){
        $this->checkCompany();
        $message = 'Record deleted';
        $type = FLASH_SUCCESS;
        $response = 200;
        try {
            $product = $this->productRepository->find(null, ['id'=> $productId, 'company_id' => $_SESSION['id']],null);
            if($product){
                $this->productRepository->softDelete($productId);
            } else {
                $message = 'Something went wrong';
                $type = FLASH_ERROR;
                $response = 404;
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $type = FLASH_ERROR;
            $response = 404;
            $this->getIndex();
        }
        FlashService::flash('product-delete',$message, $type);
        header('Location: /product', true);
        http_response_code($response);
        
    }
    
    public function getList($companyCui){
        
        $company_id = $this->companyRepository->find(null, ['CUI'=>$companyCui], null)[0]['id'];
        $companyProducts = $this->productRepository->find(null,['company_id'=>$company_id],null);
        foreach ($companyProducts as $companyProduct){
            $sku = $companyProduct['sku'];
            unset($companyProduct['sku']);
            $response[$sku] = $companyProduct;
        }
        ob_clean();
        exit(json_encode($response));
    }
    
    private function checkCompany(){
        if ($_SESSION['role'] === 'regular') {
            exit();
        }
    }
}
