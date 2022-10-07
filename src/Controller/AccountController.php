<?php

namespace App\Controller;


use App\Entity\Company;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\FlashService;
use App\Services\LoginService;
use App\Services\PaginationService;
use App\Services\UserService;
use const App\Services\FLASH_ERROR;
use const App\Services\FLASH_SUCCESS;
use const App\Services\FLASH_WARNING;

class AccountController extends BaseController
{
    private $accountObject;
    public $userRepository;
    public $userService;
    const RESULTS_PER_PAGE=30;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
    }
    
    public function getIndex()
    {
        $criteriaList = null;
        $columnProvided = null;
        
        if (!empty($_SESSION)) {
            if ($_SESSION['role'] === 'regular') {
                require_once 'templates/user/index.html';
                exit(1);
            } elseif ($_SESSION['role'] === 'company') {
                $company_id = $_SESSION['id'];
                $criteriaList['company_id'] = $company_id;
                $columnProvided = 'full_name,email,status';
            } elseif ($_SESSION['role'] === 'root') {
                $criteriaList = $this->userService->generateCriteria();
                $filters = $this->userService->generateFilters();
            }
            $accountsCount = $this->userRepository->count($criteriaList);
            $paginationService = new PaginationService($accountsCount, self::RESULTS_PER_PAGE);
            $limits = ['first_result'=>$paginationService->getPageFirstResult(), 'results_per_page'=>self::RESULTS_PER_PAGE];
            
            $accounts = $this->userRepository->find(null, $criteriaList, $columnProvided, $limits);
            $table = $this->userService->createTable($accounts);
            $pagination = $this->userService->createPagination($paginationService->getNumberOfPage());
            
            require_once 'templates/company/index.html';
            exit();
        } else {
            require_once 'templates/home/index.html';
        }
        exit(1);
    }
    
    //This route will create the account
    public function postIndex()
    {
        
        if (!empty($_POST['role'])) {
            // it is a user account
            $this->accountObject = new User();
            $this->accountObject->setCompanyId($_SESSION['id']);
            $this->accountObject->setProfileImage($_FILES['profile_image']['name']);
            $redirectRoute = 'Location: /account';
            
        } else {
            //it is a company account
            $redirectRoute = 'Location: /';
            $this->accountObject = new Company();
        }
        $message = 'Account created successfully!';
        $type = FLASH_SUCCESS;
        
        $email = $_POST['email'];
        $objectRepository = self::getRepositoryByClass(get_class($this->accountObject));
        try {
            $objectRepository->insertByEmail($email);
            foreach ($_POST as $property => $value) {
                if ($property === 'class')
                    continue;
                $propertyName = explode('_', $property);
                $propertyName = array_map('ucfirst', $propertyName);
                $setter = implode('', $propertyName);
                $toSet = 'set' . $setter;
                $this->accountObject->$toSet($value);
            }
            $objectRepository->save($this->accountObject);

            if (isset($_POST['role']))
                $this->userService->saveProfilePicture($this->accountObject->getUploadDirectory());
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('account_create', $message, $type);
        header($redirectRoute, true);
        exit();
    }
    
    //This route will update the account
    public function putIndex()
    {
        return 'Account updated!';
    }
    
    //This route will delete the account
    public function deleteIndex($accountId, $accountType)
    {
        $message = 'Account created successfully!';
        $type = FLASH_SUCCESS;
        
        try {
            $repositoryName = 'App\\Repository\\' . $accountType . 'Repository';
            $repository = new $repositoryName;
            $repository->softDelete($accountId);
        } catch (\Exception $e) {
            $message =  $e->getMessage();
            $type = FLASH_ERROR;
        }
        FlashService::flash('account_delete', $message, $type);
        header('Location: /account', true);
        exit();
    }
    

    
    public function putStatus($userId, $change){
        ob_clean();
        
        if($change === 'active'){
            $status = 'active';
        }elseif ($change === 'inactive'){
            $status = 'inactive';
        }else {
            return 'Please select type';
        }
        try{
            $user = $this->userRepository->find($userId)[0];
            if($user['status'] == $status){
                return 0;
            }
            $this->userRepository->update($userId,['status'=>$status]);
            $response = 1;
        } catch (\Exception $e){
            $response = $e->getMessage();
        }
        return $response;
    }
}
