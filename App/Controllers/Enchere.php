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
        $selectEnchere = $enchere->selectEnchereParId($id);

        $image = new Image;
        $images = $image->selectByField('timbre_id', $selectEnchere['timbre_id'], 'principal', 'DESC');
        
        $offre = new Offre;
        $selectOffre = $offre->selectOffreParEnchere($selectEnchere['enchere_id']);

        // echo "<pre> <br>";
        // print_r($selectOffre);
        // echo "<br>";

        if($selectOffre)
        {
            $offreDerniere = $selectOffre[0]['prix'];
        }
        else
        {
            $offreDerniere = $selectEnchere['prix_plancher'];
        }
        $nbOffres = $offre->countOffres($selectEnchere['enchere_id']);
        
        // print_r($offreDerniere);

        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $selectEnchere, 'images'=>$images, 'offreDerniere'=> $offreDerniere, 'nbOffres'=>$nbOffres]);
    }

    public function createOffreAction()
    {
        CheckSession::sessionAuth(FALSE);
        $errors = '';
        echo "<pre>";

        extract($_POST);

        $id = $_POST['enchere_id'];
        $enchere = new \App\Models\Enchere();
        $selectEnchere = $enchere->selectEnchereParId($id);
        
        $image = new Image;
        $images = $image->selectByField('timbre_id', $selectEnchere['timbre_id'], 'principal', 'DESC');
        
        $offre = new Offre;
        $selectOffre = $offre->selectOffreParEnchere($selectEnchere['enchere_id']);
        
        if($selectOffre)
        {
            $offreDerniere = $selectOffre;
        }
        else
        {
            $offreDerniere = $selectEnchere['prix_plancher'];
        }
        
        $nbOffres = $offre->countOffres($selectEnchere['enchere_id']);

        $validation = new Validation;
        $validation->name('Votre mise')->value($prix)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        } 
        else{

            if($prix <= $offreDerniere)
            {
                $errors = 'Votre mise doit être plus grande que la mise courante '. $offreDerniere .' $.';
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

        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $selectEnchere, 'images'=>$images, 'offreDerniere'=> $offreDerniere, 'nbOffres'=>$nbOffres]);

    }
}
