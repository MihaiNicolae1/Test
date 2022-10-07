<?php

namespace App;

use \PDO;
use Symfony\Component\Yaml\Yaml;

class Database
{
    protected static PDO $instance;
    
    public static function getInstance(): PDO
    {
        if(empty(self::$instance)) {
            try{
                $conf = Yaml::parseFile(__DIR__ . '/../src/config/config.yaml');
                self::$instance = new PDO("mysql:host=".$conf['db']['db_host'].';port='.$conf['db']['db_port'].';dbname='.$conf['db']['db_name'], $conf['db']['db_user'], $conf['db']['db_pass']);
            }catch (\PDOException $e){
                throw $e;
            }
        }
        
        return self::$instance;
    }
}
