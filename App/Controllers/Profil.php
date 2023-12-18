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
use \App\Library\CheckSession;
use \App\Library\RequirePage;
use \App\Library\UploadFiles;
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
        CheckSession::sessionAuth(FALSE);
        
        // Afficher la liste de timbres et enchères
        $timbre = new Timbre;
        $timbres = $timbre->selectTimbreParUsager($_SESSION['user_id']);

        $image = new Image;

        $i=0;
        
        foreach($timbres as $timbre)
        {
            $images = $image->selectByField('timbre_id', $timbre['timbre_id'], 'principal', 'DESC');

            if($images)
                $timbres[$i]['image'] = $images[0]['nom'];
            else
                $timbres[$i]['image'] = 'no-image.jpeg';

            $i++;
        }
        
        // Afficher la liste d'offres
        $enchere = new Enchere;

        $offre = new Offre;
        $offres = $offre->selectOffresParUsager($_SESSION['user_id']);

        $i=0;
        
        foreach($offres as $offreSingle)
        {    
            $enchereSelect = $enchere->selectEnchereParId($offreSingle['enchere_id']);  
            $offres[$i]['timbre_id'] = $enchereSelect['timbre_id'];
            $offres[$i]['timbre_nom'] = $enchereSelect['timbre_nom'];
            $offres[$i]['date_fin'] = $enchereSelect['date_fin'];

            $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal', 'DESC');
            $offres[$i]['image'] = $images[0]['nom'];

            $offresToutes = $offre->selectOffresParEnchere($offreSingle['enchere_id']);
            $offreDerniere = $offresToutes[0];
            $offres[$i]['mise_courante'] = $offreDerniere['prix'];
            $offres[$i]['offre_id'] = $offreDerniere['offre_id'];
            
            $i++;
        }      
        View::renderTemplate('Profil/index.html', ['timbres'=>$timbres, 'offres'=>$offres, 'usager_id'=>$_SESSION['user_id']]);
    }

    
    /**
     * Ajouter un nouveau timbre au compte
     */
    public function createTimbreAction()
    {
        CheckSession::sessionAuth(FALSE);
            
        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        View::renderTemplate('Profil/createTimbre.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'usager_id'=>$_SESSION['user_id']]);
    }


    /**
     * Insérer le timbre au DB
     */
    public function storeTimbreAction()
    {
        CheckSession::sessionAuth(FALSE);

        extract($_POST);

        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        // Valider les champs
        $validation = new Validation;
        $validation->name('Nom')->value($nom)->max(100)->min(2);
        $validation->name('Description')->value($nom_2)->max(100);
        $validation->name('État')->value($etat_id)->required();
        $validation->name('Dimension')->value($dimension_id)->required();
        $validation->name('Pays')->value($pays_id)->required();
        $validation->name('Image')->value($_FILES['img']['name'][0])->required();

        // Valider les images
        $msg = '';
        if (!empty($_FILES['img']['name'][0])) 
        {
            $img = $_FILES['img'];
            $img_desc = UploadFiles::reArrayFiles($img);
            
            foreach($img_desc as $val)
            {
                $checkImg = getimagesize($val["tmp_name"]);
                
                if($checkImg == false)  
                    $msg = "Le fichier téléversé n'est pas une image.";
            
            // if($val["size"]> 120000)
            // $msg = "Le fichier téléversé dépasse la taille maximale de 120ko.";
            }
        }

        // Si'y a des erreurs
        if(!$validation->isSuccess() || $msg) 
        {
            if (!$validation->isSuccess()) 
                $errors = $validation->displayErrors();
            else
                $errors = '';

            View::renderTemplate('Profil/createTimbre.html', ['errors'=> $errors, 'msg'=> $msg,'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST, 'usager_id'=>$_SESSION['user_id']]);
        } 
        // sinon
        else
        {
            // insère le film à la table timbre
            $timbre = new Timbre;
            $_POST['createur_id'] = $_SESSION['user_id'];
            $insertTimbre = $timbre->insert($_POST);

            // insère les images à la table image
            if (!empty($_FILES['img']['name'][0])) 
            {
                $img = $_FILES['img'];
                $img_desc = UploadFiles::reArrayFiles($img);

                $name = str_replace(' ', '_', $_POST['nom']);
                define ('SITE_ROOT', realpath(Config::URL_RACINE));
                $folder = "assets/img/jpg/";
                $_POST['timbre_id'] = $insertTimbre; 
  
                foreach($img_desc as $val)
                {
                    $newname = $name."_".date('YmdHis',time())."_".mt_rand().".jpg";
                    
                    move_uploaded_file($val['tmp_name'], SITE_ROOT.$folder.$newname);
                    
                    $_POST['nom'] = $newname;
                    
                    $image = new Image;
                    $insertImage = $image->insert($_POST);
                    $images = $image->selectByField('timbre_id', $insertTimbre);
                }
            }

            View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$insertTimbre, 'images'=> $images, 'usager_id'=>$_SESSION['user_id']]);
            exit();    
        }
    }


    /**
     * Créer une enchère
     */
    public function createEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);

        if(isset($_POST['timbre_id']))
        {
            $image = new Image;
            // $timbre_id = $_POST['timbre_id'];
            $timbre_id = $this->route_params['id'];

            print_r($timbre_id);
            $images = $image->selectByField('timbre_id', $timbre_id);
            
            View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        }
        else
        {
            RequirePage::url('profil/index');
            exit();    
        }
    }


    /**
     * Insérer l'enchère au DB
     */
    public function storeEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);
        
        extract($_POST);
        
        $image = new Image;
        $timbre_id = $this->route_params['id'];
        $images = $image->selectByField('timbre_id', $timbre_id);

        // Valider les champs
        $validation = new Validation;
        $msg=[];
        $errors = '';
        
        $validation->name('Date de début')->value($date_debut)->required();

        if(!$validation->isSuccess()) 
            $errors = $validation->displayErrors();
        else
            if($date_debut != '' && $date_debut < date("Y-m-d")) 
                $msg[]= "La date de début ne peut pas être dans la passée";


        $validation->name('Date de fin')->value($date_fin)->required();

        if(!$validation->isSuccess()) 
            $errors = $validation->displayErrors();
        else
            if($date_fin != '' && $date_debut >= $date_fin) 
                $msg[]= 'La date de fin ne peut pas être antérieure à la date de début';


        $validation->name('Prix plancher')->value($prix_plancher)->required();

        if(!$validation->isSuccess()) 
            $errors = $validation->displayErrors();


        if(!isset($_POST['imagePrincipale']))
            $msg[] = 'Vous devez sélectionner une image principale';
    
        
        if ($errors || $msg) 
        {
            View::renderTemplate('Profil/createEnchere.html', ['errors'=> $errors, 'msgs'=>$msg, 'enchere'=>$_POST, 'timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
            exit();
        }
        else
        {
            $enchere = new Enchere;
            $checkEnchere = $enchere->checkDuplicate($timbre_id);
            
            if ($checkEnchere)
            {
                View::renderTemplate('Profil/createEnchere.html', ['errors'=>$checkEnchere, 'enchere'=>$_POST, 'timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
            }
            // insère le film à la base de données
            else
            {
                $images = $image->updateImage($_POST['imagePrincipale'], $timbre_id);

                $_POST['createur_id'] = $_SESSION['user_id'];
                $_POST['timbre_id'] = $timbre_id;
                $insertEnchere = $enchere->insert($_POST);

                RequirePage::url('profil/index');
                exit();    
            }
        }
    }


    /**
     * Supprimer tout à la fois un timbre, ses images, l'enchère (si'l y en a une), toutes les offres (si'l y en a)
     */
    public function deleteTimbreAction()
    {
        $timbre = new Timbre;
        $timbre_id = $this->route_params['id'];
        $timbreSelect = $timbre->selectId($timbre_id);
        
        if ($timbreSelect)
        {
            $enchere = new Enchere;
            $enchereSelect = $enchere->selectByField('timbre_id', $timbre_id);
            
            if($enchereSelect) 
            {
                $enchereId = $enchereSelect[0]['id'];
                
                // Supprimer toutes les offres du timbres
                $offre = new Offre;
                $offres = $offre->selectByField('enchere_id', $enchereId, 'prix', 'DESC');
                
                if($offres) 
                {
                    $i = 0; 

                    foreach($offres as $offreSelect)
                    {
                        $delete = $offre->delete($offres[$i]['id']);
                        $i++;
                    }
                }
                // Supprimer l'enchère du timbres
                $delete = $enchere->delete($enchereId);
            }

            // Supprimer toutes les images du timbres
            $image = new Image;
            $images = $image->selectByField('timbre_id', $timbre_id);
            
            foreach($images as $imageSelect)
                $delete = $image->delete($imageSelect['id']);
        }
        // Supprimer le timbres
        $delete = $timbre->delete($timbre_id );

        RequirePage::url('profil/index');
        exit();   
    }


    /**
     * Supprimer toutes les offres qu'un utilisateur a placé sur une enchère
     */
    public function deleteOffreAction()
    {
        $offre = new Offre;
        $enchere_id = $this->route_params['id'];
        $offresParEnchere= $offre->selectOffresParUsagerEnchere($_SESSION['user_id'], $enchere_id);

        foreach($offresParEnchere as $offreParEnchere)
            $delete = $offre->delete($offreParEnchere['offre_id']);

        RequirePage::url('profil/index');
        exit();   
    }


}