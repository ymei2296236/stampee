<?php


namespace App\Controllers;

use \Core\View;
use \App\Config;
use \App\Models\Timbre;
use \App\Models\Offre;
use \App\Models\Enchere;
use \App\Models\Image;
use \App\Models\Etat;
use \App\Models\Dimension;
use \App\Models\Pays;
use \App\Models\Favori;
use \App\Library\Apps;
use \App\Library\Validation;

/**
 * Profil controller
 *
 * PHP version 7.0
 */
class Profil extends \Core\Controller
{
    /**
     * Afficher la page de profil
     */
    public function indexAction()
    { 
        Apps::sessionAuth(FALSE);
        
        // Afficher la liste de timbres et enchères

        
        $timbre = new Timbre;
        $timbres = $timbre->selectTimbreParUsager($_SESSION['user_id']);

        $image = new Image;
        $i=0;
        
        foreach($timbres as $timbre)
        {
            $images = $image->selectByField('timbre_id', $timbre['timbre_id'], 'principal');

            if($images)
                $timbres[$i]['image'] = $images[0]['nom'];
            else
                $timbres[$i]['image'] = 'no-image.jpeg';

            $i++;
        }
        
        $enchere = new Enchere;

        $offre = new Offre;
        $offres = $offre->selectOffresParUsager($_SESSION['user_id']);
        $i=0;
        
        foreach($offres as $offreSingle)
        {    
            $enchereSelect = $enchere->selectId($offreSingle['enchere_id']);  
            $offres[$i]['timbre_id'] = $enchereSelect['timbre_id'];
            $offres[$i]['timbre_nom'] = $enchereSelect['timbre_nom'];
            $offres[$i]['date_fin'] = $enchereSelect['date_fin'];

            $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal');

            $offres[$i]['image'] = $images[0]['nom'];

            $offresToutes = $offre->selectOffresParEnchere($offreSingle['enchere_id']);
            $offreDerniere = $offresToutes[0];
            $offres[$i]['mise_courante'] = $offreDerniere['prix'];

            $i++;
        }    

        $favori = new Favori;
        $favoris = $favori->selectFavoriParUsager($_SESSION['user_id']);

        $image = new Image;
        $i=0;

        foreach ($favoris as $favori) 
        {
            $images = $image->selectByField('timbre_id', $favori['timbre_id'], 'principal');

            if($images)
                $favoris[$i]['image'] = $images[0]['nom'];
            else
                $favoris[$i]['image'] = 'no-image.jpeg';
            
            $i++;
        }

        View::renderTemplate('Profil/index.html', ['timbres'=>$timbres, 'offres'=>$offres, 'favoris'=>$favoris, 'usager_id'=>$_SESSION['user_id']]);
    }

    public function deleteFavoriAction()
    {
        Apps::sessionAuth(FALSE);
        
        $favori = new Favori;
        $_POST['enchere_id'] = $this->route_params['id'];
        $_POST['usager_id'] = $_SESSION['user_id'];
        $delete = $favori->deleteFavori($_POST['enchere_id'], $_POST['usager_id']);

        Apps::url('profil/index');
    }

}

