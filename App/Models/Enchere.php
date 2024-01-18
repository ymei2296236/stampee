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

    public function selectId($value)
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
        usager.alias AS createur_nom,
        pays.nom AS pays,
        -- dimension.id AS dimension_id
        date_debut, date_fin, prix_plancher, date_emission, tirage, extrait, coup_de_coeur
        FROM $this->table
        INNER JOIN timbre 
        INNER JOIN etat 
        INNER JOIN dimension 
        INNER JOIN usager 
        INNER JOIN pays 
        ON timbre.id = enchere.timbre_id  
        and timbre.etat_id = etat.id  
        and timbre.dimension_id = dimension.id  
        and timbre.createur_id = usager.id  
        and timbre.pays_id = pays.id  
        WHERE enchere.id = '$value'
        ";

        $stmt = $db->query($sql);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function selecEnchereParFiltre($data)
    {
        $db = static::getDB();

        $queryField = null;
        $queryEtat = null;
        $queryDimension = null;
        $queryPrix = null;
        $queryPays = null;
        $queryData = null;

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
            $queryEtat = "(".$queryEtat.")";
            $queryField = str_replace('etat.nom = :etat', $queryEtat, $queryField);
        }

        if(isset($data['dimension']))
        {
            foreach($data['dimension'] as $key=>$value)
            {
                $queryDimension .="dimension.nom = '$value' OR ";
            }
            $queryDimension = rtrim($queryDimension, "OR ");
            $queryDimension = "(".$queryDimension.")";
            $queryField = str_replace('dimension.nom = :dimension', $queryDimension, $queryField);
        }
        
        if(isset($data['pays']))
        {
            $paysSelect = $data['pays'];
            $queryPays .="pays.nom = '$paysSelect'";
            $queryField = str_replace('pays.nom = :pays', $queryPays, $queryField);
        }

        if(isset($data['prix']))
        {
            foreach($data['prix'] as $key=>$value)
            {
                $prix = explode(" and ", $value);

                if (count($prix) == 2)
                {
                    // $queryPrix .="($prix[0] offre.prix $prix[1] OR $prix[0] enchere.prix_plancher $prix[1])";
                    $queryPrix .="offre.prix $prix[0] AND offre.prix $prix[1] OR enchere.prix_plancher $prix[0] AND enchere.prix_plancher $prix[1] OR ";
                }
                else if (count($prix) == 1)
                {
                    $queryPrix .="offre.prix $prix[0] OR enchere.prix_plancher $prix[0]";
                }
            }
            $queryPrix = rtrim($queryPrix, "OR ");
            $queryPrix = "(".$queryPrix.")";
            $queryField = str_replace('prix.nom = :prix', $queryPrix, $queryField);
        }
    
        $sql=
        "SELECT enchere.id FROM $this->table
            LEFT JOIN timbre 
            ON timbre.id = enchere.timbre_id
            LEFT JOIN offre
            ON offre.enchere_id = enchere.id
            INNER JOIN etat 
            ON timbre.etat_id = etat.id
            INNER JOIN pays 
            ON timbre.pays_id = pays.id
            INNER JOIN dimension 
            ON timbre.dimension_id = dimension.id
            WHERE $queryField
            GROUP by enchere.id
            ";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function selectEnchereParNom($data)
    {
        $db = static::getDB();
      
        $sql=
            "SELECT enchere.id AS id, timbre.createur_id AS createur_id,
            date_debut, date_fin, prix_plancher, coup_de_coeur, timbre_id 
            FROM $this->table 
            JOIN timbre
            ON timbre.id = enchere.timbre_id
            WHERE timbre.nom LIKE '%$data%' OR timbre.nom_2 LIKE '%$data%'";

        $stmt = $db->query($sql);
        // print_r($stmt);
        // $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectEncheresEnCours()
    {   
		$db = static::getDB();

        $sql=
            "SELECT enchere.id AS id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
            FROM enchere 
			JOIN timbre
			on enchere.timbre_id = timbre.id
            WHERE date_fin > NOW()";

		$stmt = $db->query($sql);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}


    public function selectEncheresArchivees()
    {
        $db = static::getDB();
      
        $sql=
            "SELECT enchere.id AS id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
            FROM enchere 
			JOIN timbre
			on enchere.timbre_id = timbre.id
            WHERE date_fin < NOW()";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
       
    }

    
    public function selectEncheresNouveautes($limit=false)
    {
        $db = static::getDB();

        if($limit == true) $limitSql = 'LIMIT 5';
        else $limitSql = null;

		$sql=
		"SELECT enchere.id AS id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
		FROM enchere 
		JOIN timbre
		on enchere.timbre_id = timbre.id
		ORDER by enchere.id DESC
        $limitSql";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function selectEncheresPrixEleve()
    {
        $db = static::getDB();
      
        $sql=
            "SELECT enchere.id AS id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
            FROM enchere 
            JOIN timbre
            on enchere.timbre_id = timbre.id
            ORDER by enchere.id DESC";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
       
    }



}







?>




