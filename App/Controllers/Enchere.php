<?php

namespace App\Controllers;

use \Core\View;
use \App\Config;
use \App\Models\Timbre;
use \App\Models\Image;
use \App\Models\Etat;
use \App\Models\Dimension;
use \App\Models\Pays;
use \App\Library\CheckSession;
use \App\Library\RequirePage;
use \App\Library\UploadFiles;
use \App\Library\Validation;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Enchere extends \Core\Controller
{


    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {

        $id = $this->route_params['id'];
        $enchere = new \App\Models\Enchere();
        $selectEnchere = $enchere->selectEnchereParUsager($id);

        $image = new Image;
        $images = $image->selectByField('timbre_id', $selectEnchere[0]['timbre_id']);

        View::renderTemplate('Enchere/index.html', ['enchere'=> $selectEnchere[0], 'images'=>$images]);
    }
}
