<?php

namespace App\Models;

use PDO;

use \Core\View;

/**
 * Example user model
 *
 * PHP version 7.0
 */
abstract class CRUD extends \Core\Model
{

    public function select($field='id', $order='ASC')
    {
        $db = static::getDB();
        $sql="SELECT * FROM $this->table ORDER BY $field $order";
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectId($value)
    {
        $db = static::getDB();

        $sql="SELECT * FROM $this->table WHERE $this->primaryKey = '$value'";
        $stmt = $db->query($sql);
        $count = $stmt->rowCount();

        if($count == 1) return $stmt->fetch(PDO::FETCH_ASSOC);
        else View::renderTemplate('404.html');
    }

    public function selectByField($column, $value, $field='id', $order='DESC')
    {
        $db = static::getDB();

        $sql="SELECT * FROM $this->table WHERE $column = '$value' ORDER BY $field $order" ;
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $db = static::getDB();

        $data_keys = array_fill_keys($this->fillable, '');
        $data = array_intersect_key($data, $data_keys);

        $nomChamp = implode(", ",array_keys($data));
        $valeurChamp = ":".implode(", :", array_keys($data));

        $sql = "INSERT INTO $this->table ($nomChamp) VALUES ($valeurChamp)";

        $stmt = $db->prepare($sql);
        foreach($data as $key => $value)
        {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();

        return $db->lastInsertId();
    }

    public function delete($value)
    {
        $db = static::getDB();

        $sql = "DELETE FROM $this->table WHERE $this->primaryKey = :$this->primaryKey";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":$this->primaryKey", $value);

        if($stmt->execute()) return true;
        else return $stmt->errorInfo();

    }

    public function update($data)
    { 
        $db = static::getDB();

        $data_keys = array_fill_keys($this->fillable, '');
        $data = array_intersect_key($data, $data_keys);
        
        $queryField = null;
        
        foreach($data as $key=>$value)
        {
            $queryField .="$key =:$key, ";
        }

        $queryField = rtrim($queryField, ", ");
        
        $sql = "UPDATE $this->table SET $queryField WHERE $this->primaryKey = :$this->primaryKey";

        $stmt = $db->prepare($sql);
        
        foreach($data as $key => $value)
        {
            $stmt->bindValue(":$key", $value);
        }

        if($stmt->execute()) return true;
        else return $stmt->errorInfo();

    }
}
