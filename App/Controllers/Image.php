<?php
namespace App\Controllers;

use \Core\View;
use \App\Config;
use \App\Models\Timbre;
use \App\Models\Offre;
use \App\Models\Enchere;
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
class Image extends \Core\Controller
{
    public function editAction()
    {
        Apps::sessionAuth(FALSE);

        $timbre = new Timbre;        
        $timbre_id = $this->route_params['id'];
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');
        
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        $image = new \App\Models\Image;
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');

        View::renderTemplate('Image/edit.html', ['timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        exit();    
        
    }

    public function updateAction()
    {
        Apps::sessionAuth(FALSE);

        // Valider si le timbre existe
        $timbre = new Timbre;        
        $timbre_id = $this->route_params['id'];
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');
        
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);
        
        // Recuperer et mettre toutes les images du timbre dans un objet litteral
        $image = new \App\Models\Image;
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        
        $imagesASupprimer = [];
        $i = 0;

        foreach ($images as $image)
        {
            $imagesASupprimer[$i]['id'] = $image['id'];
            $imagesASupprimer[$i]['nom'] = $image['nom'];
            $i++;
        }

        $errors = '';
        
        if ($_POST)
        {
            // Valider les champs
            $validation = new Validation;
            $validation->name('Image')->value($_FILES['img']['name'][0])->required();

            // Valider les images a televerser
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
                        $errors .= '<li>'."Le fichier téléversé dépasse la taille maximale de 5 Mo.".'</li>';
                }
            }
            
            if (!$validation->isSuccess()) 
                $errors .= $validation->displayErrors();
            // Si'y a des erreurs
            if(!$errors) 
            {
                // insère les images à la table image
                if (!empty($_FILES['img']['name'][0])) 
                {
                    $image = new \App\Models\Image;
                    
                    // Supprimer les anciennes images 
                    foreach ($imagesASupprimer as $imageASupprimer)
                    {
                        $realpath = realpath(Config::URL_RACINE); 
                        $folder = "assets/img/jpg/";
                        $nomImage = $imageASupprimer['nom'];
                        $imageUrl = $realpath.$folder.$nomImage;
                        
                        if (file_exists($imageUrl))
                        {
                            unlink($imageUrl);
                            $delete = $image->delete($imageASupprimer['id']); 
                        }
                    }

                    // insere les nouvelles images 
                    $img = $_FILES['img'];
                    $img_desc = Apps::reArrayFiles($img);

                    $name = str_replace(' ', '_', $timbreSelect['nom']);

                    $realpath = realpath(Config::URL_RACINE); 
                    $folder = "assets/img/jpg/";
                    $_POST['timbre_id'] = $timbre_id; 

                    foreach($img_desc as $val)
                    {
                        $newname = $name."_".date('YmdHis',time())."_".mt_rand().".jpg";
                        
                        move_uploaded_file($val['tmp_name'], $realpath.$folder.$newname);
                        
                        $_POST['nom'] = $newname;
                        
                        $insertImage = $image->insert($_POST);
                        $images = $image->selectByField('timbre_id', $timbre_id);
                    }

                    Apps::url('profil/index');
                    exit();    
    
                }
            }
        }
        View::renderTemplate('Image/edit.html', ['errors'=> $errors, 'timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
    }
}


?>