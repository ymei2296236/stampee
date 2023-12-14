<?php

namespace App\Controllers;

use \Core\View;
use \App\Config;
use \App\Models\Offre;
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
    public function showAction()
    {
        $errors = '';

        $id = $this->route_params['id'];
        $enchere = new \App\Models\Enchere();
        $selectEnchere = $enchere->selectEnchereParUsager($id);

        $image = new Image;
        $images = $image->selectByField('timbre_id', $selectEnchere[0]['timbre_id']);
        
        $offre = new Offre;
        $selectOffre = $offre->selectOffreParEnchere($selectEnchere[0]['enchere_id']);
        $nbOffres = $offre->countOffres($selectEnchere[0]['enchere_id']);

        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $selectEnchere[0], 'images'=>$images, 'offreDerniere'=> $selectOffre[0], 'nbOffres'=>$nbOffres]);
    }

    public function createOffreAction()
    {
        CheckSession::sessionAuth(FALSE);
        $errors = '';

        extract($_POST);

        $validation = new Validation;
        $validation->name('Votre mise')->value($prix)->required();

        $id = $_POST['enchere_id'];
        $enchere = new \App\Models\Enchere();
        $selectEnchere = $enchere->selectEnchereParUsager($id);

        $image = new Image;
        $images = $image->selectByField('timbre_id', $selectEnchere[0]['timbre_id']);

        $offre = new Offre;
        $selectOffre = $offre->selectOffreParEnchere($selectEnchere[0]['enchere_id']);
        $nbOffres = $offre->countOffres($selectEnchere[0]['enchere_id']);


        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        } 
        else{

            if($prix <= $selectOffre[0]['prix'])
            {
                $errors = 'Votre mise doit être plus grande que la mise courante '. $selectOffre[0]['prix'] .' $.';
            }
            else
            {
                $offre = new Offre;
                $insertOffre = $offre->insert($_POST);
        
                if ($insertOffre) 
                {
                    $errors = 'Mise réussite.';
                }
            }
        }

        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $selectEnchere[0], 'images'=>$images, 'offreDerniere'=> $selectOffre[0], 'nbOffres'=>$nbOffres]);

    }
}
