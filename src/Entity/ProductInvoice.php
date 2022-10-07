<?php

namespace App\Entity;

class ProductInvoice
{
    private int $product_id;
    private int $invoice_product_id;
    private int $product_quantity;
    
    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->product_id;
    }
    
    /**
     * @param int $product_id
     */
    public function setProductId(int $product_id): void
    {
        $this->product_id = $product_id;
    }
    

    /**
     * @return int
     */
    public function getInvoiceProductId(): int
    {
        return $this->invoice_product_id;
    }
    
    /**
     * @param int $invoice_product_id
     */
    public function setInvoiceProductId(int $invoice_product_id): void
    {
        $this->invoice_product_id = $invoice_product_id;
    }
    
    /**
     * @return int
     */
    public function getProductQuantity(): int
    {
        return $this->product_quantity;
    }
    
    /**
     * @param int $product_quantity
     */
    public function setProductQuantity(int $product_quantity): void
    {
        $this->product_quantity = $product_quantity;
    }
    
    
    
}
