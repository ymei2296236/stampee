<?php


namespace App\Controllers;

use \Core\View;
use \App\Config;
use \App\Models\Timbre;
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
    public function indexAction()
    { 
        CheckSession::sessionAuth(FALSE);
        
        $timbre = new Timbre;
        $timbres = $timbre->selectTimbreParUsager($_SESSION['user_id']);
        
        $image = new Image;
        $i=0;
        
        foreach($timbres as $timbre)
        {
            $selectImage = $image->selectByField('timbre_id', $timbre['timbre_id']);
            if($selectImage)
            {
                $timbres[$i]['image'] = $selectImage[0]['nom'];
            }
            else
            {
                $timbres[$i]['image'] = 'no-image.jpeg';
            }
            $i++;
        }
        
        View::renderTemplate('Profil/index.html', ['timbres'=>$timbres]);

    }


    public function createTimbreAction()
    {
        CheckSession::sessionAuth(FALSE);
            
        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        View::renderTemplate('Profil/createTimbre.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays]);
    }

    public function storeTimbreAction()
    {
        CheckSession::sessionAuth(FALSE);
        extract($_POST);

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

        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        // Si'y a des erreurs
        if(!$validation->isSuccess() || $msg) 
        {
            if (!$validation->isSuccess()) 
                $errors = $validation->displayErrors();
            else
                $errors = '';

            View::renderTemplate('Profil/createTimbre.html', ['errors'=> $errors, 'msg'=> $msg,'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST]);
        } 
        // sinon
        else
        {
            // insère le film à la table timbre
            $timbre = new Timbre;
            $insertTimbre = $timbre->insert($_POST);

            // insère les images à la table image
            if (!empty($_FILES['img']['name'][0])) 
            {
                $img = $_FILES['img'];
                $img_desc = UploadFiles::reArrayFiles($img);
                $name = $_POST['nom'];
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
                }
            }

            View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$insertTimbre]);
            exit();    
        }
    }

    public function createEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);

        if(isset($_POST['timbre_id']))
        {
            $timbre_id = $_POST['timbre_id'];
            View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$timbre_id]);
        }
        else
        {
            RequirePage::url('profil/index');
            exit();    
        }
    }

    public function storeEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);
        extract($_POST);

        // Valider les champs
        $validation = new Validation;
        $msg=[];
        $errors = '';

        $validation->name('Date de début')->value($date_debut)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        }
        else
        {
            if($date_debut != '' && $date_debut < date("Y-m-d")) 
            {
                $msg[]= "La date de début ne peut pas être dans la passée";
            }
        }

        $validation->name('Date de fin')->value($date_fin)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        }
        else
        {
            if($date_fin != '' && $date_debut >= $date_fin) 
            {
                $msg[]= 'La date de fin ne peut pas être antérieure à la date de début';
            }
        }
 
        $validation->name('Prix plancher')->value($prix_plancher)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        }
        
        if ($errors || $msg) 
        {
            View::renderTemplate('Profil/createEnchere.html', ['errors'=> $errors, 'msgs'=>$msg, 'enchere'=>$_POST]);
            exit();
        }
        else
        {
            $enchere = new Enchere;
            $checkEnchere = $enchere->checkDuplicate($_POST['timbre_id']);
            
            if ($checkEnchere)
            {
                View::renderTemplate('Profil/createEnchere.html', ['errors'=>$checkEnchere, 'enchere'=>$_POST]);
            }
            // insère le film à la base de données
            else
            {
                $insertEnchere = $enchere->insert($_POST);
                RequirePage::url('profil/index');
                exit();    
            }
        }
    }

}