<?php


namespace App\Controllers;

use \Core\View;

/**
 * Profil controller
 *
 * PHP version 7.0
 */
class Profil extends \Core\Controller
{
    public function indexAction()
    { 
        \App\Library\CheckSession::sessionAuth(FALSE);

        $timbre = new \App\Models\Timbre;
        $timbres = $timbre->selectTimbreParUsager($_SESSION['user_id']);

        $image = new \App\Models\Image;
        $i=0;
        
        foreach($timbres as $timbre)
        {
            $selectImage = $image->selectByField('timbre_id', $timbre['timbre_id']);

            $timbres[$i]['image'] = $selectImage[0]['nom'];
            $i++;
        }
        
        View::renderTemplate('Profil/index.html', ['timbres'=>$timbres]);
    }


    public function createTimbreAction()
    {
        \App\Library\CheckSession::sessionAuth(FALSE);
            
            $etat = new \App\Models\Etat;
            $etats = $etat->select('nom');

            $dimension = new \App\Models\Dimension;
            $dimensions = $dimension->select();

            $pays = new \App\Models\Pays;
            $tousPays = $pays->select();

            View::renderTemplate('Profil/createTimbre.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre_id'=>$_POST['timbre_id']]);
    }

    public function storeTimbreAction()
    {
        \App\Library\CheckSession::sessionAuth(FALSE);
        extract($_POST);

        $validation = new \App\Library\Validation;
        $validation->name('Nom')->value($nom)->max(100)->min(2);
        $validation->name('Description')->value($nom_2)->max(100);
        $validation->name('État')->value($etat_id)->required();
        $validation->name('Dimension')->value($dimension_id)->required();
        $validation->name('Pays')->value($pays_id)->required();

        $msg = '';

        if (!empty($_FILES['img']['name'][0])) 
        {
            $img = $_FILES['img'];
            $img_desc = \App\Library\UploadFiles::reArrayFiles($img);
            
            foreach($img_desc as $val)
            {
                $checkImg = getimagesize($val["tmp_name"]);
                
                if($checkImg == false)  
                $msg = "Le fichier téléversé n'est pas une image.";
            
            // if($val["size"]> 120000)
            // $msg = "Le fichier téléversé dépasse la taille maximale de 120ko.";
            }
        }

        $etat = new \App\Models\Etat;
        $etats = $etat->select('nom');

        $dimension = new \App\Models\Dimension;
        $dimensions = $dimension->select();

        $pays = new \App\Models\Pays;
        $tousPays = $pays->select();

        
        if(!$validation->isSuccess() || $msg) 
        {
            if (!$validation->isSuccess()) 
                $errors = $validation->displayErrors();
            else
                $errors = '';
            
            View::renderTemplate('Profil/createTimbre.html', ['errors'=> $errors, 'msg'=> $msg,'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST]);
        } 
        else
        {
            // insère le film à la base de données
            $timbre = new \App\Models\Timbre;
            $insertTimbre = $timbre->insert($_POST);

            // téléverse le fichier au dossier uploads s'il y en a;
            if (!empty($_FILES['img']['name'][0])) 
            {
                $img = $_FILES['img'];
                $img_desc = \App\Library\UploadFiles::reArrayFiles($img);
                $name = $_POST['nom'];
                $folder = \App\Config::PATH_DIR. "assets/img/jpg/";
                $_POST['timbre_id'] = $insertTimbre; 
                
                foreach($img_desc as $val)
                {
                    $newname = $name."_".date('YmdHis',time())."_".mt_rand().".jpg";
                    move_uploaded_file($val['tmp_name'], $folder.$newname);
                    
                    $_POST['nom'] = $newname;
                    
                    $image = new \App\Models\Image;
                    $insertImage = $image->insert($_POST);
                }
            }

            View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$insertTimbre]);
            exit();    
        }
    }

    public function createEnchereAction()
    {
        \App\Library\CheckSession::sessionAuth(FALSE);
        print_r($_POST);

        if($_POST['timbre_id'] != '')
            $timbre_id = $_POST['timbre_id'];


        View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$timbre_id]);
    }

    public function storeEnchereAction()
    {
        \App\Library\CheckSession::sessionAuth(FALSE);
        extract($_POST);

        $validation = new \App\Library\Validation;
        $validation->name('Date de début')->value($date_debut)->required();
        $validation->name('Date de fin')->value($date_fin)->required();
        $validation->name('Prix plancher')->value($prix_plancher)->required();
        
        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
            $msg=[];

            if($date_debut != '' && $date_debut < date("Y-m-d")) 
            {
                $msg[]= "La date de début ne peut pas être dans la passée";
            }

            if($date_fin != '' && $date_debut >= $date_fin) 
            {
                $msg[]= 'La date de fin ne peut pas être antérieure à la date de début';
            }
            
            View::renderTemplate('Profil/createEnchere.html', ['errors'=> $errors, 'msgs'=>$msg, 'enchere'=>$_POST]);
            exit();    

        } 
        else
        {
            // insère le film à la base de données

            echo "<pre>";
            print_r($_POST);
            $enchere = new \App\Models\Enchere;
            $checkEnchere = $enchere->checkDuplicate($_POST['timbre_id']);


            if (!$checkEnchere)
                $insertEnchere = $enchere->insert($_POST);

            header("location:/stampee/public/profil/index");
            exit();    
        }
    }

}