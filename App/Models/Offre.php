<?php

namespace App\Models;

use PDO;


class Offre extends CRUD 
{
    protected $table = 'offre';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'prix', 'enchere_id', 'usager_id'];


    public function selectOffresParEnchere($enchereId)
    {
        $db = static::getDB();
    
        $sql = 
        "SELECT offre.id AS offre_id, prix, usager_id
        FROM $this->table 
        LEFT JOIN enchere 
        ON enchere.id = offre.enchere_id  
        WHERE enchere.id = '$enchereId'
        ORDER BY prix DESC";    
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countOffres($enchereId)
    {
        $db = static::getDB();
    
        $sql = 
        "SELECT offre.id AS offre_id, prix, usager_id
        FROM $this->table 
        LEFT JOIN enchere 
        ON enchere.id = offre.enchere_id  
        WHERE enchere.id = '$enchereId'";    
        
        $stmt = $db->query($sql);
        $count = $stmt->rowCount();

        return $count;
    }

    public function selectOffresParUsager($usagerId)
    {
        $db = static::getDB();
    
        $sql = 
        "SELECT max(prix) AS prix, usager_id, enchere_id
        FROM $this->table 
        LEFT JOIN usager 
        ON offre.usager_id = usager.id 
        WHERE offre.usager_id = '$usagerId'
        GROUP BY enchere_id
        ";   
        
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectOffresParUsagerEnchere($usagerId, $enchereId)
    {
        $db = static::getDB();
    
        $sql = 
        "SELECT offre.id AS offre_id, usager_id
        FROM $this->table 
        INNER JOIN usager 
        LEFT JOIN enchere 
        ON offre.usager_id = usager.id 
        AND offre.enchere_id = enchere.id 
        WHERE usager.id = '$usagerId'
        AND enchere.id = $enchereId
        ";   

        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function selectEnchereIdParOffre()
    {
        $db = static::getDB();
      
        $sql=
            "SELECT enchere_id
            FROM $this->table 
            GROUP by enchere_id
            LIMIT 5";

        $stmt = $db->query($sql);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}



?>