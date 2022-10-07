<?php

namespace App\Services;

use App\Repository\UserRepository;

class LoginService
{
    
    
    private static function checkLogin()
    {
        if (!isset($_SESSION['id'])) {
            throw new \Exception('You must be logged in to access this page!',);
        }
    }
    
    public static function handleLogin()
    {
        try {
            self::checkLogin();
        } catch (\Exception $e) {
            FlashService::flash('invoice_error', $e->getMessage(), FLASH_ERROR);
            header('Location: /', true);
            exit();
        }
    }
    
    public static function getCompanyId(){
        
        $userRepository = new UserRepository();
        switch ($_SESSION['role']){
            case 'company':
                $company_id = $_SESSION['id'];
                break;
            case 'regular':
                $user =  $userRepository->find(null,['email'=>$_SESSION['email']],'company_id')[0];
                $company_id = $user['company_id'];
                break;
            default:
                $company_id = null;
        }
        return $company_id;
    }
    
}
