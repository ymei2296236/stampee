<?php

namespace App\Models;

use PDO;


class Image extends CRUD 
{
    protected $table = 'image';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'timbre_id', 'nom'];
}

?>