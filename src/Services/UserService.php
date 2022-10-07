<?php

namespace App\Services;

class UserService extends BaseService
{
    public function saveProfilePicture($userDirectory)
    {
        // Where the file is going to be stored
        $target_dir = 'public/uploads/users/' . $userDirectory;
        if(!is_dir($target_dir))
            mkdir($target_dir, 777);
        $file = $_FILES['profile_image']['name'];
	    $path = pathinfo($file);
	    $filename = $path['filename'];
	    $ext = $path['extension'];
	    $temp_name = $_FILES['profile_image']['tmp_name'];
	    $path_filename_ext = $target_dir . '/' . $filename . "." . $ext;
        if (!move_uploaded_file($temp_name, $path_filename_ext)) {
            throw new \ErrorException('Error while uploading the profile picture');
        }
    }
    public function setTableActions(){
    
    }
    
    public function generateTableActions($id)
    {
        $actionsMenu = "<td><div class='dropend'>
                        <button type='button' class='btn ' data-bs-toggle='dropdown' aria-expanded='false'>
                               <i class='fa-solid fa-ellipsis-vertical'></i>
                        </button>
                        <ul class='dropdown-menu'>";
        if(LoginService::getCompanyId() == null){
            $actionsMenu .= "<li><button class='dropdown-item' onclick=activateUser('$id')>Activate user</button></li>";
            $actionsMenu .= "<li><button class='dropdown-item' onclick=deactivateUser('$id')>Deactivate user</button></li>";
        }
        $actionsMenu.="<li><button class='dropdown-item' onclick=deleteUserAccount('$id')>Delete</button></li>
                        </ul></div></td></tr>";
        return $actionsMenu;
    }
    
    public function getExcludedColumns()
    {
        return ['id'];
    }
    public function generateFilters()
    {
        
        $activeChecked = '';
        $inactiveChecked = '';
        if(isset($_GET['active'])){
            $activeChecked = 'checked';
        }
        if(isset($_GET['inactive'])){
            $inactiveChecked = 'checked';
        }
        
        $filters = "<form class='row filter_box'>
                <h4>Search users by:</h4>
                <div class='form-check'>
                    <input class='form-check-input' type='radio' name='active' id='active' ". $activeChecked ." >
                    <label class='form-check-label' for='active'>
                        Active
                    </label>
                </div>
                <div class='form-check'>
                <input class='form-check-input' type='radio' name='inactive' id='inactive' ". $inactiveChecked ." >
                <label class='form-check-label' for='inactive'>
                    Inactive
                </label>
                </div>
                <button class ='btn btn-primary mt-3 mr-3'> Search </button><a href = '/account' class ='btn btn-dark ml-3 mt-3'> Reset </a>
            </form>";
        
        return $filters;
    }
    public function generateCriteria(){
        $criteriaList = null;
        if(isset($_GET['active']) && !isset($_GET['inactive'])){
            $criteriaList['status'] = 'active';
        } elseif (!isset($_GET['active']) && isset($_GET['inactive'])){
            $criteriaList['status'] = 'inactive';
        }
        return $criteriaList;
    }
}

