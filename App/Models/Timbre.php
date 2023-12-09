<?php

namespace App\Models;

use PDO;


class Timbre extends CRUD 
{
    protected $table = 'timbre';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'createur_id', 'nom', 'description', 'date_emission', 'couleur', 'tirage', 'extrait', 'certification', 'etat_id', 'dimension_id', 'pays_id'];
}

?>