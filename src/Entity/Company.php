<?php
namespace App\Entity;


class Company
{
    private ?int $id=null;
    private string $name;
    private string $register_number;
    private int $capital;
    private string $created_at;
    private string $address;
    private int $phone;
    private string $email;
    private string $password;
    private string $IBAN;
    private string $CUI;
    private string $bank_name;
    
    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * @param int|null $id
     */
    public function setId(int $id=null): void
    {
        $this->id = $id;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getRegisterNumber(): string
    {
        return $this->register_number;
    }
    
    /**
     * @param string $register_number
     */
    public function setRegisterNumber(string $register_number): void
    {
        $this->register_number = $register_number;
    }
    
    /**
     * @return int
     */
    public function getCapital(): int
    {
        return $this->capital;
    }
    
    /**
     * @param int $capital
     */
    public function setCapital(int $capital): void
    {
        $this->capital = $capital;
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
    public function getAddress(): string
    {
        return $this->address;
    }
    
    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
    
    /**
     * @return int
     */
    public function getPhone(): int
    {
        return $this->phone;
    }
    
    /**
     * @param int $phone
     */
    public function setPhone(int $phone): void
    {
        $this->phone = $phone;
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
    public function getIBAN(): string
    {
        return $this->IBAN;
    }
    
    /**
     * @param string $IBAN
     */
    public function setIBAN(string $IBAN): void
    {
        $this->IBAN = $IBAN;
    }
    
    /**
     * @return string
     */
    public function getCUI(): string
    {
        return $this->CUI;
    }
    
    /**
     * @param string $CUI
     */
    public function setCUI(string $CUI): void
    {
        $this->CUI = $CUI;
    }
    
    /**
     * @return string
     */
    public function getBankName(): string
    {
        return $this->bank_name;
    }
    
    /**
     * @param string $bank_name
     */
    public function setBankName(string $bank_name): void
    {
        $this->bank_name = $bank_name;
    }
    
}
