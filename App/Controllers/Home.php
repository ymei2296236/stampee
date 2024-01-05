<?php

namespace App\Controllers;

use \Core\View;
use \Core\Router;
use \App\Config;
use \App\Models\Offre;
use \App\Models\Timbre;
use \App\Models\Enchere;
use \App\Models\Image;
use \App\Models\Etat;
use \App\Models\Pays;
use \App\Models\Dimension;
use \App\Models\Favori;
use \App\Library\Apps;
use \App\Library\Validation;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $offre = new Offre;
        $encheresSelect = $offre->selectEnchereIdParOffre();

        $enchere = new Enchere;
        $image = new Image;

        $i = 0;

        foreach ($encheresSelect as $enchereSelect) 
        {
            $enchere_id = $enchereSelect['enchere_id'];

            $nbOffresParEnchere = $offre->countOffres($enchere_id);
            $encheresSelect[$i]['nbOffres'] = $nbOffresParEnchere;

            $enchereInfo = $enchere->selectId($enchere_id);
            $encheresSelect[$i]['timbre_nom'] = $enchereInfo['timbre_nom'];
            $encheresSelect[$i]['timbre_nom_2'] = $enchereInfo['timbre_nom_2'];
            $encheresSelect[$i]['timbre_id'] = $enchereInfo['timbre_id'];
            $encheresSelect[$i]['date_fin'] = $enchereInfo['date_fin'];

            $images = $image->selectByField('timbre_id', $enchereInfo['timbre_id'], 'principal');
            $encheresSelect[$i]['image'] = $images[0]['nom'];

            $offresToutes = $offre->selectOffresParEnchere($enchere_id);

            if ($offresToutes)
            {
                $offreDerniere = $offresToutes[0];
                $encheresSelect[$i]['mise_courante'] = $offreDerniere['prix'];
            }
            else 
            {
                $encheresSelect[$i]['mise_courante'] = $encheresSelect[$i]['prix_plancher'];
            }

            if($encheresSelect[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheresSelect[$i]['archivee'] = true; 
            };

            $i++;
        }
        
        View::renderTemplate('Home/index.html', ['encheres'=>$encheresSelect]);
        exit();
    }
}
