<?php

namespace App\Models;

use PDO;


class Dimension extends CRUD 
{
    protected $table = 'dimension';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nom'];
}

?>