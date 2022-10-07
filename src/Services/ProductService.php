<?php

namespace App\Services;

class ProductService extends BaseService
{
  
    public function generateTableActions($id): string
    {
        return "<td><button type='button' class='btn btn-danger' onclick=deleteProduct('$id')>Delete</btn></td></tr>";
    }
    
    public function getExcludedColumns()
    {
        return ['id', 'company_id'];
    }
}
