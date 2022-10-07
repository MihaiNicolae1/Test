<?php

namespace App\Repository;

use App\Database;
use PDO;
use ReflectionClass;
use ReflectionException;

class BaseRepository implements RepositoryInterface
{
    
    public $fields;
    public $object;
    
    
    /**
     * @throws ReflectionException
     */
    public function getFields()
    {
        $tableName = ucfirst($this->getTableName());
        $tableName = 'App\\Entity\\' . $tableName;
        $this->object = new $tableName;
        $reflect = new ReflectionClass($this->object);
        return $reflect->getProperties();
    }
    
    public function getTableName()
    {
        $currentClass = get_class($this);
        $currentClassArray = explode('\\', $currentClass);
        $currentClass = $currentClassArray[count($currentClassArray) - 1];
        $currentClass = lcfirst(str_replace('Repository', '', $currentClass));
        return $currentClass;
    }
    
    public function findBy($query)
    {
        try {
            $db = Database::getInstance();
            $state = $db->prepare($query);
            $state->execute();
            $data = $state->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            echo $e->getMessage();
            // TODO: Add logs.
        }
        return $data;
    }
    
    protected function execute($query)
    {
        try {
            $db = Database::getInstance();
            
            $state = $db->prepare($query);
            $state->execute();
        } catch (\PDOException $e) {
            echo "Error while connecting to database" . $e;
        }
    }
    
    public function find($id = null, $criteria = null, $columnProvided = null, $limits = null)
    {
        if ($criteria != null) {
            $criteriaList = [];
            foreach ($criteria as $column => $value) {
                if(is_array($value)){
                    foreach ($value as $column_values){
                        $criteriaList[] = $column . " BETWEEN '" . $column_values[0] . "' AND '" .  $column_values[1] . "'";
                    }
                    continue;
                }
                $criteriaList[] = $column . '="' . $value . '"';
            }
            if ($columnProvided != null) {
                $query = 'SELECT ' . $columnProvided . ' FROM `' . $this->getTableName() . '` WHERE ' . implode(' AND ', $criteriaList);
            } else {
                $query = 'SELECT * FROM `' . $this->getTableName() . '` WHERE ' . implode(' AND ', $criteriaList);
            }
        } elseif($id != null) {
            $query = 'SELECT * FROM `' . $this->getTableName() . '` WHERE id = ' . $id;
        }
        if($criteria == null && $id == null){
            return $this->findAll($limits);
        }
        if ($limits != null) {
            $query .= ' LIMIT ' . $limits['first_result'] . ',' . $limits['results_per_page'];
        }
        return $this->findBy($query);
    }
    
    public function findAll($limits = null)
    {
        $query = 'SELECT * FROM `' . $this->getTableName() . '`';
        if ($limits != null) {
            $query .= ' LIMIT ' . $limits['first_result'] . ',' . $limits['results_per_page'];
        }
        return $this->findBy($query);
    }
    
    public function save($object)
    {
        $excludes = ['id', 'created_at', 'updated_at'];
        $properties = $this->getFields();
        $query = "INSERT INTO `" . $this->getTableName() . "` (";
        $query2 = " VALUES (";
        if (is_array($properties)) {
            
            foreach ($properties as $key => $property) {
                $property = $property->getName();
                if (in_array($property, $excludes))
                    continue;
                $query .= " `$property`,";
                $keyNameArray = explode('_', $property);
                $keyNameArray = array_map('ucfirst', $keyNameArray);
                $property = implode('', $keyNameArray);
                $toGet = "get" . $property;
                $val = $object->$toGet();
                $query2 .= " '$val',";
            }
            $query = rtrim($query, ",");
            $query .= ")";
            $query2 = rtrim($query2, ",");
            $query2 .= ")";
        }
        $query .= $query2;
        $this->execute($query);
    }
    
    public function softDelete($id)
    {
        $query = "DELETE FROM `" . $this->getTableName() . "` WHERE id=" . $id;
        $this->execute($query);
    }
    
    public function update($id, $columnProvided = null)
    {
        if ($columnProvided != null && $id != null) {
            $valuesToSet = [];
            foreach ($columnProvided as $column => $value) {
                $valuesToSet[] = $column . '="' . $value. '"';
            }
            $query = "UPDATE `" . $this->getTableName() . "` SET " . implode(' , ', $valuesToSet) . " WHERE id =" . $id;
            $this->execute($query);
        }
    }
    
    public function count($criteria = null)
    {
        if ($criteria != null) {
            $criteriaList = [];
            foreach ($criteria as $column => $value) {
                if(is_array($value)){
                    foreach ($value as $column_values){
                        $criteriaList[] = $column . " BETWEEN '" . $column_values[0] . "' AND '" .  $column_values[1] . "'";
                    }
                    continue;
                }
                $criteriaList[] = $column . '="' . $value . '"';
            }
            $query = 'SELECT COUNT(*) as count FROM `' . $this->getTableName() . '` WHERE ' . implode(' AND ', $criteriaList);
            
        } else {
            $query = 'SELECT COUNT(*) as count FROM `' . $this->getTableName() . '`';
        }
        return $this->findBy($query)[0]['count'];
    }
}
