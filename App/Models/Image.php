<?php

namespace App\Models;

use PDO;


class Image extends CRUD 
{
    protected $table = 'image';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'timbre_id', 'nom', 'principal', 'supplementaire'];

    public function updateImage($value)
    {
        $db = static::getDB();
    
        $sql = 
        "UPDATE $this->table 
        SET principal = 1
        WHERE nom = '$value'";    

        $stmt = $db->prepare($sql);
        $stmt->bindValue(":$this->primaryKey", $value);

        if($stmt->execute()) return true;
        else return $stmt->errorInfo();
    }
}

?>