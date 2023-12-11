<?php

namespace App\Models;

use PDO;


class Enchere extends CRUD 
{
    protected $table = 'enchere';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'date_debut', 'date_fin', 'prix_plancher', 'coup_de_coeur', 'timbre_id', 'createur_id'];
}

?>