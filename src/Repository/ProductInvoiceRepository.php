<?php

namespace App\Repository;

class ProductInvoiceRepository extends BaseRepository
{
    public function softDelete($invoice_id)
    {
        $query = "DELETE FROM `" . $this->getTableName() . "` WHERE invoice_product_id=" . $invoice_id;
        $this->execute($query);
    }
}
