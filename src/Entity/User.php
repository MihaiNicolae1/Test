<?php

namespace App\Entity;

use DateTime;

class User
{
    private int $id;
    private int $company_id;
    private string $full_name;
    private string $email;
    private string $password;
    private string $profile_image;
    private string $role;
    private string $created_at;
    private string $updated_at;
    private string $status;
    private string $upload_directory;
    
    public function __construct(){
        $this->status = 'active';
        $this->role = 'regular';
        $this->setUploadDirectory(uniqid('user_'));
    }
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function getUploadDirectory(): string
    {
        return $this->upload_directory;
    }
    
    /**
     * @param string $upload_directory
     */
    public function setUploadDirectory(string $upload_directory): void
    {
        $this->upload_directory = $upload_directory;
    }
    
    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->full_name;
    }
    
    /**
     * @param string $full_name
     */
    public function setFullName(string $full_name): void
    {
        $this->full_name = $full_name;
    }
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = hash('md5',$password);
    }
    
    /**
     * @return string
     */
    public function getProfileImage(): string
    {
        return $this->profile_image;
    }
    
    /**
     * @param string $profile_image
     */
    public function setProfileImage(string $profile_image): void
    {
        $this->profile_image = $profile_image;
    }
    
    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }
    
    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }
    
    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }
    
    /**
     * @param string $created_at
     */
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }
    
    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
    
    /**
     * @param string $updated_at
     */
    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
    
    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
    
    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
    
    /**
     * @return int
     */
    public function getCompany(): int
    {
        return $this->company_id;
    }
    
    /**
     * @param int $company_id
     */
    public function setCompany(int $company_id): void
    {
        $this->company_id = $company_id;
    }
    
    /**
     * @return int
     */
    public function getCompanyId(): int
    {
        return $this->company_id;
    }
    
    /**
     * @param int $company_id
     */
    public function setCompanyId(int $company_id): void
    {
        $this->company_id = $company_id;
    }
}
