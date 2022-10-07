<?php

namespace App\Repository;

class ProductRepository extends BaseRepository
{
    public function insertBySku($company_id, $sku){
        $product = $this->find(null, ['sku' => $sku, 'company_id'=>$company_id], null);
        if(!empty($product)) {
            throw new \Exception('Sorry! A product with this sku already exists');
        }
    }
}
