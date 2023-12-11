<?php

namespace App\Models;

use PDO;


class Enchere extends CRUD 
{
    protected $table = 'enchere';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'date_debut', 'date_fin', 'prix_plancher', 'coup_de_coeur', 'timbre_id', 'createur_id'];


    public function checkDuplicate($value) 
    {
        $db = static::getDB();

        $sql = "SELECT * FROM $this->table WHERE timbre_id = '$value'";
        // print_r($sql);
        // die();
        $stmt = $db->prepare($sql);
        $stmt->execute(array($value));
        $count = $stmt->rowCount();

        print_r($count);

        if($count >= 1) {
            $error = "<ul><li>L'enchère déjà existe.</li></ul>";
            return $error;
        }
    }
}

?>