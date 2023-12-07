<?php

namespace App\Models;

use PDO;


/**
 * Example user model
 *
 * PHP version 7.0
 */
class Contact extends CRUD
{
    protected $table = 'contact';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nom', 'message'];




}
