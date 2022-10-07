<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Services\FlashService;
use App\Services\LoginService;
use App\Services\UserService;
use const App\Services\FLASH_ERROR;
use const App\Services\FLASH_SUCCESS;
use const App\Services\FLASH_WARNING;

class UserController extends BaseController
{
    private UserRepository $userRepository;
    private UserService $userService;
    
    public function __construct(){
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
    }
    
    public function getAdd(){
        if($_SESSION['role'] === 'company')
            require_once 'templates/user/add.html';
        else
            return "You can't add users, you must be a company!";
    }
    
    public function getProfile(){
        
        LoginService::handleLogin();
        $loggedUser = $this->userRepository->find($_SESSION['id'])[0];
        $user_picture = '/public/uploads/users/' . $loggedUser['upload_directory'] .  '/' . $loggedUser['profile_image'];
        $user_name = $loggedUser['full_name'];
        $password = $loggedUser['password'];
        require_once 'templates/account/profile.html';
        exit();
    }
    public function postProfile(){
        LoginService::handleLogin();
        $loggedUser = $this->userRepository->find($_SESSION['id'])[0];
        $type = FLASH_SUCCESS;
        $message = 'Profile updated successfully';
        $toUpdate = [];
        if(empty($_POST)){
            $message = "Form can't be empty";
            $type = FLASH_ERROR;
        }
        try{
            if(!empty($_FILES['profile_image']['name'])){
                unlink('public/uploads/users/' . $loggedUser['upload_directory'] . '/' .  $loggedUser['profile_image']);
                $this->userService->saveProfilePicture($loggedUser['upload_directory']);
                $toUpdate['profile_image'] = $_FILES['profile_image']['name'];
            }
            if($_POST['full_name'] !== $loggedUser['full_name']){
                $toUpdate['full_name'] = $_POST['full_name'];
            }
            if($_POST['password'] !== $loggedUser['password']){
                $toUpdate['password'] = md5($_POST['password']);
            }
            if(!empty($toUpdate)){
                $this->userRepository->update($loggedUser['id'], $toUpdate);
            } else {
                $message = 'Nothing to update on your profile!';
                $type = FLASH_WARNING;
            }
        } catch (\Exception $e){
            $message = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('account_delete', $message, $type);
        header('Location: /user/profile', true);
        exit();
    }
}
