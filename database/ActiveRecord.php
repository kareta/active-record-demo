<?php

namespace database;

use Pixie\Connection;
use helpers\Inflector;
use Pixie\QueryBuilder\QueryBuilderHandler;

abstract class ActiveRecord {
    protected $config;
    protected $table;
    protected $attributes = [];
    protected $primaryKey = 'id';

    function __construct()
    {
        $this->config = require('../config/database.php');
        $className = (new \ReflectionClass($this))->getShortName();
        $this->table = Inflector::pluralize(strtolower($className));

    }

    protected function getConnection()
    {
        return new Connection('mysql', $this->config, 'QB');
    }

    public static function all()
    {
        $instance = new static;
        $instance->getConnection();
        $resultSet = \QB::table($instance->table)->select('*')->get();

        $entities = [];
        foreach ($resultSet as $entityAttributes) {
            $entity = new static;
            $entity->attributes = get_object_vars($entityAttributes);
            $entities[] = $entity;
        }
        return $entities;
    }

    public static function get($id)
    {
        $instance = new static;
        $instance->getConnection();
        $entityAttributes = \QB::table($instance->table)->where($instance->primaryKey, $id)->first();
        if (!$entityAttributes) {
            return null;
        }
        $entity = new static;
        $entity->attributes = get_object_vars($entityAttributes);
        return $entity;
    }
    
    public function save()
    {
        $queryBuilder = new QueryBuilderHandler($this->getConnection());
        $rowExists = isset($this->attributes[$this->primaryKey])
            && $queryBuilder->table($this->table)->get($this->attributes[$this->primaryKey]);

        if ($rowExists) {
            $queryBuilder
                ->table($this->table)
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->update($this->attributes);
        } else {
            $queryBuilder->table($this->table)->insert($this->attributes);
        }
    }
    
    public function delete()
    {
        $queryBuilder = new QueryBuilderHandler($this->getConnection());

        $rowExists = isset($this->attributes[$this->primaryKey])
            && $queryBuilder->table($this->table)->get($this->attributes[$this->primaryKey]);
        
        if ($rowExists) {
            \QB::table($this->table)
                ->where($this->primaryKey, $this->attributes[$this->primaryKey])
                ->delete();
        }
    }

    public static function destroy($id)
    {
        $instance = new static;
        $queryBuilder = new QueryBuilderHandler($instance->getConnection());
        $rowExists = $queryBuilder->table($instance->table)->get($id);

        if ($rowExists) {
            \QB::table($instance->table)
                ->where($instance->primaryKey, $id)
                ->delete();
        }
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } else {
            throw new \Exception('No such property: ' . $name);
        }
    }

    function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    function __isset($name) {
        return array_key_exists($name, $this->attributes);
    }
}
