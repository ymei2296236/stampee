<?php

namespace App\Models;

use PDO;


class Privilege extends CRUD 
{
    protected $table = 'privilege';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nom'];
}

?>