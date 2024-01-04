<?php

namespace App\Models;

use PDO;


class Favori extends CRUD 
{
    protected $table = 'favori';
    protected $primaryKey = 'enchere_id'.'usager_id';
    protected $fillable = ['usager_id', 'enchere_id', 'timbre_id', 'createur_id'];


    public function selectFavori($enchere_id, $usager_id)
    {
        $db = static::getDB();

        $sql="SELECT * FROM $this->table WHERE enchere_id = '$enchere_id' AND usager_id = '$usager_id'" ;
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectFavoriParUsager($usager_id)
    {
        $db = static::getDB();

        $sql="SELECT *, timbre.nom AS timbre_nom
        FROM $this->table 
        JOIN timbre
        ON timbre.id = favori.timbre_id
        JOIN enchere
        ON enchere.id = favori.enchere_id
        WHERE usager_id = '$usager_id'" ;
        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFavori($enchere_id, $usager_id)
    {
        $db = static::getDB();

        $sql = "DELETE FROM $this->table WHERE enchere_id = '$enchere_id' AND usager_id = '$usager_id'";
        $stmt = $db->query($sql);
        print_r($stmt);

        if($stmt->execute()) return true;
        else return $stmt->errorInfo();
    }

    public function selectFavoriParEnchereId($enchere_id)
    {
        $db = static::getDB();

        $sql="SELECT usager_id
        FROM $this->table 
        WHERE enchere_id = $enchere_id";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>