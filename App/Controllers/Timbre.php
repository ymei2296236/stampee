<?php

namespace App\Controllers;

use \Core\View;
use \App\Config;
use \App\Models\Offre;
use \App\Models\Enchere;
use \App\Models\Image;
use \App\Models\Etat;
use \App\Models\Dimension;
use \App\Models\Pays;
use \App\Library\Apps;
use \App\Library\Validation;


/**
 * Enchere controller
 *
 * PHP version 7.0
 */
class Timbre extends \Core\Controller
{
    /**
     * Ajouter un nouveau timbre au compte
     */
    public function createAction()
    {
        Apps::sessionAuth(FALSE);
        
        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        View::renderTemplate('Timbre/create.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'usager_id'=>$_SESSION['user_id']]);
        exit();    
    }

    /**
     * Enregistrer l'info du timbre
     */
    public function storeAction()
    {
        Apps::sessionAuth(FALSE);
        
        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        // l'action Store
        // initialiser les variables de fonction renderTemplate
        $errors = '';
        $timbre = '';
        $images = '';

        if ($_POST)
        {
            extract($_POST);

            // Valider les champs
            $validation = new Validation;
            $validation->name('Nom')->value($nom)->max(45)->min(2);
            $validation->name('Description')->value($nom_2)->max(100);
            $validation->name('État')->value($etat_id)->required();
            $validation->name('Dimension')->value($dimension_id)->required();
            $validation->name('Pays')->value($pays_id)->required();
            $validation->name('Image')->value($_FILES['img']['name'][0])->required();

            // Valider les champs
            if (!$validation->isSuccess()) 
            {
                $errors = $validation->displayErrors();
            }

            // Valider les images
            if (!empty($_FILES['img']['name'][0])) 
            {
                $img = $_FILES['img'];
                $img_desc = Apps::reArrayFiles($img);
                
                foreach($img_desc as $val)
                {
                    $checkImg = getimagesize($val["tmp_name"]);
                    
                    if($checkImg == false)  
                        $errors .= '<li>'."Le fichier téléversé n'est pas une image.".'</li>';
                    
                    if($val["size"]> 5000000)
                    $errors .= '<li>'."Le fichier téléversé dépasse la taille maximale de 5 Mo".'</li>';
                }
            }

            // Si tous est valide
            if(!$errors)
            {
                // insère le timbre à la DB
                $timbre = new \App\Models\Timbre;
                $_POST['createur_id'] = $_SESSION['user_id'];
                $insertTimbre = $timbre->insert($_POST);

                // insère les images à la table image
                if (!empty($_FILES['img']['name'][0])) 
                {
                    $img = $_FILES['img'];
                    $img_desc = Apps::reArrayFiles($img);

                    $name = str_replace(' ', '_', $_POST['nom']);
                    // $name = str_replace(':', '', $name);
                    // $name = str_replace('__', '_', $name);

                    $realpath = realpath(Config::URL_RACINE); 
                    $folder = "assets/img/jpg/";
                    $_POST['timbre_id'] = $insertTimbre; 
    
                    foreach($img_desc as $val)
                    {
                        $newname = $name."_".date('YmdHis',time())."_".mt_rand().".jpg";
                        
                        move_uploaded_file($val['tmp_name'], $realpath.$folder.$newname);
                        
                        $_POST['nom'] = $newname;
                        
                        $image = new Image;
                        $insertImage = $image->insert($_POST);
                        $images = $image->selectByField('timbre_id', $insertTimbre);
                        $timbre_id = $insertTimbre;
                    }
                }
                View::renderTemplate('Enchere/create.html', ['timbre_id'=>$insertTimbre, 'images'=> $images, 'usager_id'=>$_SESSION['user_id']]);
                exit();    
            }
        }

        View::renderTemplate('Timbre/create.html', ['errors'=> $errors, 'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST, 'images'=> $images, 'usager_id'=>$_SESSION['user_id']]);
        exit();    

    }


    /**
     * Supprimer tout à la fois un timbre, ses images, l'enchère (si'l y en a une), toutes les offres (si'l y en a)
     */
    public function deleteAction()
    {
        Apps::sessionAuth(FALSE);

        // Valider si le timbre existe
        $timbre = new \App\Models\Timbre;
        $timbre_id = $this->route_params['id'];
        if($timbre_id) $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');

        // Valider si l'usager est le createur
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

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
            {
                $realpath = realpath(Config::URL_RACINE); 
                $folder = "assets/img/jpg/";
                $nomImage = $imageSelect['nom'];
                $imageUrl = $realpath.$folder.$nomImage;
                
                if (file_exists($imageUrl))
                {
                    unlink($imageUrl);
                    $delete = $image->delete($imageSelect['id']);
                }
            }
        }
        // Supprimer le timbres
        $timbre = new \App\Models\Timbre;
        $delete = $timbre->delete($timbre_id );

        Apps::url('profil/index');
        exit();   
    }

    /**
     * Modifier un timbre
     */
    public function editAction()
    {
        Apps::sessionAuth(FALSE);
        
        // Valider si le timbre existe
        $timbre = new \App\Models\Timbre;        
        $timbre_id = $this->route_params['id'];
        if($timbre_id) $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');
        
        // Valider si usager est le createur
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        // Afficher les infos existantes du timbre
        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        View::renderTemplate('Timbre/edit.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$timbreSelect, 'timbre_id'=>$timbre_id, 'usager_id'=>$_SESSION['user_id']]);
    }

    /**
     * Enregistrer la modification
     */
    public function updateAction()
    {
        Apps::sessionAuth(FALSE);

        // Valider si le timbre existe
        $timbre = new \App\Models\Timbre;        
        $timbre_id = $this->route_params['id'];
        if($timbre_id) $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');

        // Valider si usager est le createur
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        // Afficher les infos existantes du timbre
        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        if ($_POST)
        {
            extract($_POST);
            // l'action Update
            // initialiser les variables de fonction renderTemplate
            $errors = '';
            $timbre = '';
            $images = '';

            // Valider les champs
            $validation = new Validation;
            $validation->name('Nom')->value($nom)->max(45)->min(2);
            $validation->name('Description')->value($nom_2)->max(100);
            $validation->name('État')->value($etat_id)->required();
            $validation->name('Dimension')->value($dimension_id)->required();
            $validation->name('Pays')->value($pays_id)->required();
        
            // Si'y a des erreurs
            if(!$validation->isSuccess()) 
            {
                if (!$validation->isSuccess()) 
                    $errors = $validation->displayErrors();

                View::renderTemplate('Timbre/edit.html', ['errors'=> $errors, 'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST, 'timbre_id'=>$timbre_id, 'usager_id'=>$_SESSION['user_id']]);
                exit(); 
            } 
            else
            {          
                // insère le timbre à la DB
                $timbre = new \App\Models\Timbre;
                $_POST['id'] = $timbre_id;
                $updateTimbre = $timbre->update($_POST);

                $image = new Image;
                $images = $image->selectByField('timbre_id', $timbre_id, 'principal');

                // Diriger ver la page de modification d'image
                View::renderTemplate('Image/edit.html', ['timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
                exit();    
            }
        }
        else
        {
            Apps::url('profil/index');
            exit(); 
        }
    }

}

?>