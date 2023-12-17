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


    public function indexAction() 
    {
        $enchere = new \App\Models\Enchere();
        $encheres = $enchere->select();

        $image = new Image;

        $i = 0;

        foreach($encheres as $enchereSelect)
        {
            $enchereChaque = $enchere->selectId($enchereSelect['id']);
            $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal', 'DESC');

            // echo "<pre>";
            // print_r($images);
            
            $encheres[$i]['timbre_nom'] = $enchereChaque['timbre_nom'];
            $encheres[$i]['timbre_nom_2'] = $enchereChaque['timbre_nom_2'];
            $encheres[$i]['image'] = $images[0]['nom'];

            if($enchereChaque['prix']) $encheres[$i]['prix'] = $enchereChaque['prix'];
            else $encheres[$i]['prix'] = $encheres[$i]['prix_plancher'];

            $i++;
        }
        // print_r($encheres);

        if($_SESSION) $usager_id = $_SESSION['user_id'];
        else $usager_id = '';

        View::renderTemplate('Enchere/index.html', ['usager_id'=>$usager_id, 'encheres'=>$encheres]);

    }

    /**
     * Show the index page
     *
     * @return void
     */
    public function showAction()
    {
        $errors = '';

        if($_SESSION) $usager_id = $_SESSION['user_id'];
        else $usager_id = '';

        
        $id = $this->route_params['id'];
        $enchere = new \App\Models\Enchere();
        $selectEnchere = $enchere->selectEnchereParId($id);
        
        $image = new Image;
        $images = $image->selectByField('timbre_id', $selectEnchere['timbre_id'], 'principal', 'DESC');
        
        $offre = new Offre;
        $selectOffre = $offre->selectOffreParEnchere($selectEnchere['enchere_id']);

        if($selectOffre)
        {
            $offreDerniere = $selectOffre[0]['prix'];
        }
        else
        {
            $offreDerniere = $selectEnchere['prix_plancher'];
        }
        $nbOffres = $offre->countOffres($selectEnchere['enchere_id']);
        
        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $selectEnchere, 'images'=>$images, 'offreDerniere'=> $offreDerniere, 'nbOffres'=>$nbOffres,'usager_id'=>$usager_id]);
    }

    public function createOffreAction()
    {
        CheckSession::sessionAuth(FALSE);
        $errors = '';

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
            $offreDerniere = $selectOffre[0]['prix'];
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

                $_POST['usager_id'] = $_SESSION['user_id'];
                $insertOffre = $offre->insert($_POST);
        
                if ($insertOffre) 
                {
                    $errors = 'Mise réussite.';
                    $selectOffre = $offre->selectId($insertOffre);
                    $offreDerniere = $selectOffre['prix'];
                }
            }
        }

        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $selectEnchere, 'images'=>$images, 'offreDerniere'=> $offreDerniere, 'nbOffres'=>$nbOffres, 'usager_id'=>$_SESSION['user_id']]);
    }
}
