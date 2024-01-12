<?php

namespace App\Models;

use PDO;


class Image extends CRUD 
{
    protected $table = 'image';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'timbre_id', 'nom', 'principal'];

    
    public function updateImage($timbre_id, $imagePrincipale)
    {
        $db = static::getDB();
    
        $sql = 
        "UPDATE $this->table 
        SET principal = 0
        WHERE timbre_id = '$timbre_id'";

        $stmt = $db->prepare($sql);
        
        if($stmt->execute())
        {
            $sql = 
            "UPDATE $this->table 
            SET principal = 1
            WHERE nom = '$imagePrincipale'";    
        
            $stmt = $db->query($sql);

            if($stmt->execute()) return true;
            else return $stmt->errorInfo();
        }
        else return $stmt->errorInfo();

    }

    public function selectImageByField($table, $column, $value, $field='id')
    {
        $db = static::getDB();

        $sql="SELECT * FROM $table WHERE $column = '$value' ORDER BY $field DESC" ;
        $stmt = $db->query($sql);

        return $stmt->errorInfo();
    }

}

?>