<?php

namespace App\Controller;

use App\Services\FlashService;
use App\Services\LoggerService;
use http\Exception;
use const App\Services\FLASH_ERROR;

class AuthController extends BaseController
{
    
    public function getLogin()
    {
        require_once 'templates/account/login.html';
        return;
    }
    
    //This will handle the login
    
    /**
     * @throws \Exception
     */
    public function postLogin()
    {
        if (empty($_POST)) {
            exit(0);
        }
        $repo = self::getRepositoryByClass($_POST['class']);
        $account = $repo->find(null, ['email' => $_POST['email'], 'password' => hash('md5', $_POST['password'])])[0];
        
        if (empty($account)) {
            $message = 'Account does not exist!';
            FlashService::flash('account_not_exist', $message, FLASH_ERROR);
            LoggerService::Logger($message);
            header('Location: /auth/login', true);
            exit();
        }
        if($account['status'] === 'inactive'){
            $message = 'Your account is inactive! Please talk with an admin!';
            FlashService::flash('account_not_exist',$message, FLASH_ERROR);
            LoggerService::Logger($message);
            header('Location: /auth/login', true);
            exit();
        }
        
        $_SESSION['id'] = $account['id'];
        $_SESSION['email'] = $account['email'];
        $_SESSION['name'] = $account['name'] ?? $account['full_name'];
        isset($account['role']) ? $_SESSION['role'] = $account['role'] : $_SESSION['role'] = 'company';
        $_SESSION['class'] = basename($_POST['class']);
        
        header("Location: / ", true);
        exit();
        
    }
    
    //This will handle the logout
    public function getLogout()
    {
        if (!empty($_SESSION)) {
            session_destroy();
            header("Location: / ", true);
        } else {
            return "You are already logged out";
        }
    }
    
    
}
