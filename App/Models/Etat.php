<?php

namespace App\Models;

use PDO;


class Etat extends CRUD 
{
    protected $table = 'etat';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nom'];
}

?>