<?php

namespace App\Models;

use PDO;


class Pays extends CRUD 
{
    protected $table = 'pays';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nom'];
}

?>