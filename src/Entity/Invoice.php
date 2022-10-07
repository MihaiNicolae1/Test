<?php
namespace App\Entity;

class Invoice
{
    private int $id;
    private string $number;
    private string $issuer_company_name;
    private string $issuer_cui;
    private int $issuer_company_id;
    private string $issuer_register_number;
    private string $issuer_name;
    private int $issuer_CNP;
    private string $issuer_identity_card;
    private string $emmited_date;
    private string $due_date_of_payment;
    private string $customer_name;
    private string $customer_CUI;
    private string $customer_register_number;
    private string $customer_address;
    private int $customer_phone;
    private float $amount;
    private float $discount_percent_value;
    private int $is_paid;
    private int $is_printed;
    private string $type;
    
    /**
     * @param int $id
     */
    public function __construct()
    {
        $this->number = strtoupper(uniqid('INV_'));
        $this->is_paid = 0;
        $this->is_printed = 0;
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
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    
    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }
    
    /**
     * @param string $number
     */
    public function setNumber(string $number): void
    {
        $this->number = $number;
    }
    
    /**
     * @return string
     */
    public function getIssuerName(): string
    {
        return $this->issuer_name;
    }
    
    /**
     * @param string $issuer_name
     */
    public function setIssuerName(string $issuer_name): void
    {
        $this->issuer_name = $issuer_name;
    }
    
    /**
     * @return string
     */
    public function getIssuerCompanyName(): string
    {
        return $this->issuer_company_name;
    }
    
    /**
     * @param string $issuer_company_name
     */
    public function setIssuerCompanyName(string $issuer_company_name): void
    {
        $this->issuer_company_name = $issuer_company_name;
    }
    
    /**
     * @return string
     */
    public function getIssuerCui(): string
    {
        return $this->issuer_cui;
    }
    
    /**
     * @param string $issuer_cui
     */
    public function setIssuerCui(string $issuer_cui): void
    {
        $this->issuer_cui = $issuer_cui;
    }
    
    /**
     * @return string
     */
    public function getIssuerRegisterNumber(): string
    {
        return $this->issuer_register_number;
    }
    
    /**
     * @param string $issuer_register_number
     */
    public function setIssuerRegisterNumber(string $issuer_register_number): void
    {
        $this->issuer_register_number = $issuer_register_number;
    }
    
    /**
     * @return int
     */
    public function getIssuerCNP(): int
    {
        return $this->issuer_CNP;
    }
    
    /**
     * @param int $issuer_CNP
     */
    public function setIssuerCNP(int $issuer_CNP): void
    {
        $this->issuer_CNP = $issuer_CNP;
    }
    
    /**
     * @return string
     */
    public function getIssuerIdentityCard(): string
    {
        return $this->issuer_identity_card;
    }
    
    /**
     * @param string $issuer_identity_card
     */
    public function setIssuerIdentityCard(string $issuer_identity_card): void
    {
        $this->issuer_identity_card = $issuer_identity_card;
    }
    
    /**
     * @return string
     */
    public function getEmmitedDate(): string
    {
        return $this->emmited_date;
    }
    
    /**
     * @param string $emmited_date
     */
    public function setEmmitedDate(string $emmited_date): void
    {
        $time = strtotime($emmited_date);
        $newFormat = date('Y-m-d H:i:s', $time);
        $this->emmited_date = $newFormat;
    }
    
    /**
     * @return string
     */
    public function getDueDateOfPayment(): string
    {
        return $this->due_date_of_payment;
    }
    
    /**
     * @param string $due_date_of_payment
     */
    public function setDueDateOfPayment(string $due_date_of_payment): void
    {
        $time = strtotime($due_date_of_payment);
        $newFormat = date('Y-m-d H:i:s', $time);
        $this->due_date_of_payment = $newFormat;
    }
    
    /**
     * @return string
     */
    public function getCustomerName(): string
    {
        return $this->customer_name;
    }
    
    /**
     * @param string $customer_name
     */
    public function setCustomerName(string $customer_name): void
    {
        $this->customer_name = $customer_name;
    }
    
    /**
     * @return string
     */
    public function getCustomerCUI(): string
    {
        return $this->customer_CUI;
    }
    
    /**
     * @param string $customer_CUI
     */
    public function setCustomerCUI(string $customer_CUI): void
    {
        $this->customer_CUI = $customer_CUI;
    }
    
    /**
     * @return string
     */
    public function getCustomerRegisterNumber(): string
    {
        return $this->customer_register_number;
    }
    
    /**
     * @return int
     */
    public function getIssuerCompanyId(): int
    {
        return $this->issuer_company_id;
    }
    
    /**
     * @param int $issuer_company_id
     */
    public function setIssuerCompanyId(int $issuer_company_id): void
    {
        $this->issuer_company_id = $issuer_company_id;
    }
    
    /**
     * @param string $customer_register_number
     */
    public function setCustomerRegisterNumber(string $customer_register_number): void
    {
        $this->customer_register_number = $customer_register_number;
    }
    
    /**
     * @return string
     */
    public function getCustomerAddress(): string
    {
        return $this->customer_address;
    }
    
    /**
     * @param string $customer_address
     */
    public function setCustomerAddress(string $customer_address): void
    {
        $this->customer_address = $customer_address;
    }
    
    /**
     * @return int
     */
    public function getCustomerPhone(): int
    {
        return $this->customer_phone;
    }
    
    /**
     * @param int $customer_phone
     */
    public function setCustomerPhone(int $customer_phone): void
    {
        $this->customer_phone = $customer_phone;
    }
    
    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
    
    /**
     * @return float
     */
    public function getDiscountPercentValue(): float
    {
        return $this->discount_percent_value;
    }
    
    /**
     * @param float $discount_percent_value
     */
    public function setDiscountPercentValue(float $discount_percent_value): void
    {
        $this->discount_percent_value = $discount_percent_value;
    }
    
    /**
     * @return int
     */
    public function getIsPaid(): int
    {
        return $this->is_paid;
    }
    
    /**
     * @param string $is_paid
     */
    public function setIsPaid(string $is_paid): void
    {
        if($is_paid ==='on')
            $is_paid = 1;
        elseif($is_paid === 'off'){
            $is_paid = 0;
        }
        $this->is_paid = $is_paid;
    }
    
    /**
     * @return int
     */
    public function getIsPrinted(): int
    {
        return $this->is_printed;
    }
    
    /**
     * @param int $is_printed
     */
    public function setIsPrinted(int $is_printed): void
    {
        $this->is_printed = $is_printed;
    }
    
}
