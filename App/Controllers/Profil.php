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

        View::renderTemplate('Profil/index.html');
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

            View::renderTemplate('Timbre/create.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays]);
    }

    public function storeTimbreAction()
    {
        \App\Library\CheckSession::sessionAuth(FALSE);
        extract($_POST);
        
        // echo "<pre>";
        // print_r($_POST);

        if (isset($_POST['upload']))
        {
            $_POST['image0'] = $_FILES["image0"]["name"];
            $_POST['image1'] = $_FILES["image1"]["name"];
        }

        $validation = new \App\Library\Validation;
 
        $validation->name('Nom')->value($nom)->max(100)->min(2);
        $validation->name('Description')->value($description)->max(100);
        $validation->name('État')->value($etat_id)->required();
        $validation->name('Dimension')->value($dimension_id)->required();
        $validation->name('Pays')->value($pays_id)->required();
        $validation->name('Extrait')->value($extrait)->max(225);
        // $validation->name('Tirage')->value($tirage)->pattern('int');
        // $validation->name('Image')->value($image0)->required();

        $msg = '';

        // validatoin du fichier à téléverser
        // if ($image0) 
        // {
        //     $checkImg = getimagesize($_FILES["image0"]["tmp_name"]);
        //     $imageFileType = strtolower(pathinfo("uploads/" . basename($_POST['image0']), PATHINFO_EXTENSION));

        //     if($checkImg == false)  
        //         $msg = "Le fichier téléversé n'est pas une image.";

        //     if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") 
        //         $msg = "Seuls les fichiers JPG, JPEG, PNG sont autorisés.";

        //     if($_FILES["image0"]["size"]> 120000)
        //         $msg = "Le fichier téléversé dépasse la taille maximale de 120ko.";
        // }

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
            
            View::renderTemplate('Timbre/create.html', ['errors'=> $errors, 'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST]);
        } 
        else
        {
            // téléverse le fichier au dossier uploads
            $tempname = $_FILES["image0"]["tmp_name"];
            $folder = $_SERVER['DOCUMENT_ROOT'] . "/Stampee/uploads/" . $_POST['image0'];
            move_uploaded_file($tempname, $folder);

            // insère le film à la base de données
            $timbre = new \App\Models\Timbre;
            $insertTimbre = $timbre->insert($_POST);

            $_POST['timbre_id'] = $insertTimbre; 
            $_POST['nom'] .= '_'.date('d-m-Y_H-i-s');

            $image = new \App\Models\Image;
            $insertImage = $image->insert($_POST);

            View::renderTemplate('Profil/index.html');
            
            exit();    
        }

        }

    

    
}