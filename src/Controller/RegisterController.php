<?php

namespace App\Controller;

use App\Entity\Company;

class RegisterController extends BaseController
{
    
    public $accountController;
    public $authController;
    
    public function __construct()
    {
        $this->accountController = new AccountController();
        $this->authController = new AuthController();
    }
    
    public function getIndex()
    {
        if($_SESSION['class'] === 'company')
            require_once 'templates/user/add.html';
        else {
            require_once 'templates/company/register.html';
        }
    }
    
    public function postIndex()
    {
        if(empty($_POST))
            return 0;
        elseif(isset($_POST['IBAN']))
            $_POST['class'] = 'Company';
        try{
            $this->accountController->postIndex();
            $this->authController->postLogin();
            exit();
            
        } catch (\Exception $e){
            echo '<div class="alert alert-danger" role="alert">'. $e->getMessage(). '</div>';
            $this->getIndex();
        }
    }
}
