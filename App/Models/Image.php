<?php

namespace App\Models;

use PDO;


class Image extends CRUD 
{
    protected $table = 'image';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'timbre_id', 'nom', 'principal', 'supplementaire'];

    public function updateImage($nomImage)
    {
        $db = static::getDB();
    
        $sql = 
        "UPDATE $this->table 
        SET principal = 1
        WHERE nom = '$nomImage'";    
 
    $stmt = $db->query($sql);

        if($stmt->execute()) return true;
        else return $stmt->errorInfo();
    }
}

?>