<?php
namespace App\Entity;

class Product
{
    private int $sku;
    private string $name;
    private string $description;
    private int $company_id;
    private float $value_without_vat;
    private float $vat_percent;
    private string $measure_unit;
    
    /**
     * @return int
     */
    public function getSku(): int
    {
        return $this->sku;
    }
    
    /**
     * @param int $sku
     */
    public function setSku(int $sku): void
    {
        $this->sku = $sku;
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
    public function getDescription(): string
    {
        return $this->description;
    }
    
    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    
    /**
     * @return float
     */
    public function getValueWithoutVat(): float
    {
        return $this->value_without_vat;
    }
    
    /**
     * @param float $value_without_vat
     */
    public function setValueWithoutVat(float $value_without_vat): void
    {
        $this->value_without_vat = $value_without_vat;
    }
    
    /**
     * @return float
     */
    public function getVatPercent(): float
    {
        return $this->vat_percent;
    }
    
    /**
     * @param float $vat_percent
     */
    public function setVatPercent(float $vat_percent): void
    {
        $this->vat_percent = $vat_percent;
    }
    
    /**
     * @return string
     */
    public function getMeasureUnit(): string
    {
        return $this->measure_unit;
    }
    
    /**
     * @param string $measure_unit
     */
    public function setMeasureUnit(string $measure_unit): void
    {
        $this->measure_unit = $measure_unit;
    }
    
    
    
}
