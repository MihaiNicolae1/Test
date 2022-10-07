<?php

namespace App\Repository;

interface RepositoryInterface
{
    public function getFields();
    public function getTableName();
    public function findBy($query);
    public function find($id, $criteria, $columnProvided);
    public function findAll();
    public function save($object);
    public function softDelete($id);
    public function update($object);
    
}
