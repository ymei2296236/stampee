<?php

namespace App\Models;

use PDO;


class Timbre extends CRUD 
{
    protected $table = 'timbre';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'createur_id', 'nom', 'nom_2', 'date_emission', 'couleur', 'tirage', 'extrait', 'certification', 'etat_id', 'dimension_id', 'pays_id']; 

    public function selectTimbreParUsager($userId)
    {
        $db = static::getDB();

        $sql = 
        "SELECT timbre.id AS timbre_id, timbre.nom AS timbre_nom, enchere.id AS enchere_id, date_debut, date_fin, prix_plancher
        FROM $this->table 
        LEFT JOIN enchere 
        ON timbre.id = enchere.timbre_id  
        WHERE timbre.createur_id = '$userId'
        ORDER by enchere_id";     

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
}

?>