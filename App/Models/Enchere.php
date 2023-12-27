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

    public function selectEnchereParId($value)
    {
        $db = static::getDB();

        $sql=
        "SELECT timbre.id AS timbre_id, 
        timbre.nom AS timbre_nom, 
        timbre.nom_2 AS timbre_nom_2,
        enchere.id AS enchere_id, 
        etat.nom AS etat, 
        dimension.nom AS dimension,
        usager.id AS createur_id,
        usager.alias AS createur,
        pays.nom AS pays,
        date_debut, date_fin, prix_plancher, date_emission, tirage, extrait
        FROM $this->table
        INNER JOIN timbre 
        INNER JOIN etat 
        INNER JOIN dimension 
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


    public function selecEnchereParEtat($data)
    {
        $db = static::getDB();
    
        $sql=
            "SELECT enchere.id FROM $this->table 
            INNER JOIN timbre 
            INNER JOIN etat 
            ON timbre.id = enchere.timbre_id
            AND timbre.etat_id = etat.id
            WHERE etat.nom = '$data'
            GROUP by enchere.id
            ";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function selecEnchereParPays($data)
    {
        $db = static::getDB();
    
        $sql=
            "SELECT enchere.id, pays.nom AS paysNom FROM $this->table 
            INNER JOIN timbre 
            INNER JOIN pays 
            ON timbre.id = enchere.timbre_id
            AND timbre.pays_id = pays.id
            WHERE pays.nom = '$data'
            GROUP by enchere.id
            ";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function selecEnchereParFiltre($data)
    {
        $db = static::getDB();

        $queryField = null;
        $queryEtat = null;
        $queryDimension = null;
        $queryPays = null;

        foreach($data as $key=>$value)
        {
            $queryField .="$key.nom = :$key AND ";
        }
                
        $queryField = rtrim($queryField, "AND ");

        if(isset($data['etat']))
        {
            foreach($data['etat'] as $key=>$value)
            {
                $queryEtat .="etat.nom = '$value' OR ";
            }
            $queryEtat = rtrim($queryEtat, "OR ");
            $queryField = str_replace('etat.nom = :etat', $queryEtat, $queryField);
        }

        if(isset($data['dimension']))
        {
            foreach($data['dimension'] as $key=>$value)
            {
                $queryDimension .="dimension.nom = '$value' OR ";
            }
            $queryDimension = rtrim($queryDimension, "OR ");
            $queryField = str_replace('dimension.nom = :dimension', $queryDimension, $queryField);
        }

        
        if(isset($data['pays']))
        {
            $paysSelect = $data['pays'];
            $queryPays .="pays.nom = '$paysSelect'";
            $queryField = str_replace('pays.nom = :pays', $queryPays, $queryField);
        }


        // echo '<pre>';
        // print_r($queryField);
        // echo '<br>';
        // print_r($queryEtat);
        // echo '<br>';
    
        $sql=
            "SELECT enchere.id FROM $this->table 
            INNER JOIN timbre 
            INNER JOIN etat 
            INNER JOIN pays 
            INNER JOIN dimension 
            INNER JOIN offre 
            ON timbre.id = enchere.timbre_id
            AND timbre.etat_id = etat.id
            AND timbre.dimension_id = dimension.id
            AND timbre.pays_id = pays.id
            AND enchere.id = offre.enchere_id
            WHERE $queryField
            GROUP by enchere.id
            ";

        $stmt = $db->prepare($sql);
        print_r($sql);
        echo '<br>';

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }

}



?>




