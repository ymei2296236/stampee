<?php

namespace App\Models;

use PDO;


class Enchere extends CRUD 
{
    protected $table = 'enchere';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'date_debut', 'date_fin', 'prix_plancher', 'coup_de_coeur', 'timbre_id', 'createur_id'];


    public function selectId($value)
    {
        $db = static::getDB();

        $sql=
        "SELECT timbre.nom AS timbre_nom, 
        timbre.nom_2 AS timbre_nom_2,
        enchere.id AS enchere_id, 
        prix_plancher, offre.prix
        FROM $this->table
        INNER JOIN timbre 
        LEFT JOIN offre 
        ON timbre.id = enchere.timbre_id  
        AND enchere.id = offre.enchere_id  
        WHERE enchere.id = '$value'
        ORDER BY offre.prix DESC
        ";

        $stmt = $db->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    public function selectEnchereParId($value)
    {
        $db = static::getDB();

        $sql=
        "SELECT timbre.id AS timbre_id, 
        timbre.nom AS timbre_nom, 
        timbre.nom_2 AS timbre_nom_2,
        enchere.id AS enchere_id, 
        etat.nom AS etat, 
        usager.id AS createur_id,
        usager.alias AS createur,
        pays.nom AS pays,
        date_debut, date_fin, prix_plancher, date_emission, tirage, extrait
        FROM $this->table
        INNER JOIN timbre 
        INNER JOIN etat 
        INNER JOIN usager 
        INNER JOIN pays 
        ON timbre.id = enchere.timbre_id  
        and timbre.etat_id = etat.id  
        and timbre.createur_id = usager.id  
        and timbre.pays_id = pays.id  
        WHERE enchere.id = '$value'
        ";

        $stmt = $db->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}



?>