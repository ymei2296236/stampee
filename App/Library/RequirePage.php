<?php
namespace App\Library;

use \App\Config;

class RequirePage 
{
    static public function model($model)
    {
        return require_once('model/'.$model.'.php');
    }

    static public function library($library) 
    {
        return require_once('library/'.$library.'.php');
    }

    static public function url($url)
    {
        header('location:'. Config::URL_RACINE.$url);
        exit();
    }
}


?>