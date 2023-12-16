<?php

namespace App\Models;

use PDO;


class Enchere extends CRUD 
{
    protected $table = 'enchere';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'date_debut', 'date_fin', 'prix_plancher', 'coup_de_coeur', 'timbre_id', 'createur_id'];

    // Valider si l'enchere existe
    public function checkDuplicate($value) 
    {
        $db = static::getDB();

        $sql = "SELECT * FROM $this->table WHERE timbre_id = '$value'";
        $stmt = $db->query($sql);
        $count = $stmt->rowCount();

        if($count >= 1) {
            $error = "<ul><li>L'enchère déjà existe.</li></ul>";
            return $error;
        }
    }

    public function selectEnchereParId($enchereId)
    {
        $db = static::getDB();

        $sql=
        "SELECT timbre.id AS timbre_id, 
        timbre.nom AS timbre_nom, 
        timbre.nom_2 AS timbre_nom_2,
        enchere.id AS enchere_id, 
        -- image.nom as image_nom, 
        etat.nom as etat, 
        usager.id as createur,
        pays.nom as pays,
        date_debut, date_fin, prix_plancher, date_emission, tirage, extrait
        FROM $this->table
        INNER JOIN timbre 
        -- INNER JOIN image 
        INNER JOIN etat 
        INNER JOIN usager 
        INNER JOIN pays 
        ON timbre.id = enchere.timbre_id  
        -- and timbre.id = image.timbre_id  
        and timbre.etat_id = etat.id  
        and timbre.createur_id = usager.id  
        and timbre.pays_id = pays.id  
        WHERE enchere.id = '$enchereId'
        ";

        $stmt = $db->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }


}



?>