<?php

namespace App\Controller;

use App\FPDF\PDF;
use App\Repository\UserRepository;
use App\Services\LoginService;

class HomeController
{
    private UserRepository $userRepository;
    
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    
    public function anyIndex()
    {
        require_once 'templates/home/index.html';
    }

}
