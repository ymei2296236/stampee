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
     * Afficher le catalogue d'enchères
     */
    public function indexAction() 
    {
        $enchere = new \App\Models\Enchere();
        $encheres = $enchere->select();

        $image = new Image;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            $timbre = new Timbre;
            $enchereSelect = $enchere->selectId($enchereChaque['id']);
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];

            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal', 'DESC');
            $encheres[$i]['image'] = $images[0]['nom'];

            $offre = new Offre;
            $offresToutes = $offre->selectOffresParEnchere($enchereChaque['id']);

            if ($offresToutes)
            {
                $offreDerniere = $offresToutes[0];
                $encheres[$i]['mise_courante'] = $offreDerniere['prix'];
            }
            else 
            {
                $encheres[$i]['mise_courante'] = $encheres[$i]['prix_plancher'];
            }
            $i++;
        }

        if($_SESSION) 
            $usager_id = $_SESSION['user_id'];
        else 
            $usager_id = '';

        View::renderTemplate('Enchere/index.html', ['usager_id'=>$usager_id, 'encheres'=>$encheres]);
    }


    /**
     * Afficher la page d'enchère
     *
     * @return void
     */
    public function showAction()
    {
        $errors = '';

        if($_SESSION) 
            $usager_id = $_SESSION['user_id'];
        else 
            $usager_id = '';
        
        $enchere = new \App\Models\Enchere();
        $enchere_id = $this->route_params['id'];
        $enchereSelect = $enchere->selectEnchereParId($enchere_id);
        
        $image = new Image;
        $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal', 'DESC');
        
        $offre = new Offre;
        $offres = $offre->selectOffresParEnchere($enchereSelect['enchere_id']);

        if($offres) 
            $prixCourant = $offres[0]['prix'];
        else 
            $prixCourant = $enchereSelect['prix_plancher'];

        $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
        
        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $enchereSelect, 'images'=>$images, 'prixCourant'=> $prixCourant, 'nbOffres'=>$nbOffres,'usager_id'=>$usager_id]);
    }


    /**
     * Miser 
     */
    public function createOffreAction()
    {
        CheckSession::sessionAuth(FALSE);

        extract($_POST);

        $enchere = new \App\Models\Enchere();
        $enchere_id = $this->route_params['id'];
        $enchereSelect = $enchere->selectEnchereParId($enchere_id);
        
        $image = new Image;
        $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal', 'DESC');
        
        $offre = new Offre;
        $offres = $offre->selectOffresParEnchere($enchereSelect['enchere_id']);
        $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
        
        if($offres) 
            $prixCourant = $offres[0]['prix'];
        else
            $prixCourant = $enchereSelect['prix_plancher'];

        $errors = '';
        $validation = new Validation;
        $validation->name('Votre mise')->value($prix)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        } 
        else{

            if($prix <= $prixCourant)
            {
                $errors = 'Votre mise doit être plus grande que la mise courante '. $prixCourant .' $.';
            }
            else
            {
                $_POST['usager_id'] = $_SESSION['user_id'];
                $_POST['enchere_id'] = $enchere_id;
                $insertOffre = $offre->insert($_POST);
        
                if ($insertOffre) 
                {
                    $errors = 'Mise réussite.';
                    $offreSelect = $offre->selectId($insertOffre);
                    $prixCourant = $offreSelect['prix'];
                }
            }
        }
        View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $enchereSelect, 'images'=>$images, 'prixCourant'=> $prixCourant, 'nbOffres'=>$nbOffres, 'usager_id'=>$_SESSION['user_id']]);
    }
}
