<?php

class Privilege extends CRUD 
{
    protected $table = 'privilege';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'nom'];
}

?>