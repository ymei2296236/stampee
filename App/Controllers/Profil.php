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
            $images = $image->selectByField('timbre_id', $timbre['timbre_id'], 'principal');

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

            $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal');

            $offres[$i]['image'] = $images[0]['nom'];

            $offresToutes = $offre->selectOffresParEnchere($offreSingle['enchere_id']);
            $offreDerniere = $offresToutes[0];
            $offres[$i]['mise_courante'] = $offreDerniere['prix'];
            // $offres[$i]['offre_id'] = $offreDerniere['offre_id'];
            
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
    public function createEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);

        // Placer un timbre aux enchères après la création du timbre
        if ($_POST)
        {
            extract($_POST);

            $etat = new Etat;
            $etats = $etat->select();

            $dimension = new Dimension;
            $dimensions = $dimension->select();

            $pays = new Pays;
            $tousPays = $pays->select('nom');

            // Valider les champs
            $validation = new Validation;
            $validation->name('Nom')->value($nom)->max(45)->min(2);
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
                // insère le timbre à la DB
                $timbre = new Timbre;
                $_POST['createur_id'] = $_SESSION['user_id'];
                $insertTimbre = $timbre->insert($_POST);

                // insère les images à la table image
                if (!empty($_FILES['img']['name'][0])) 
                {
                    $img = $_FILES['img'];
                    $img_desc = UploadFiles::reArrayFiles($img);

                    $name = str_replace(' ', '_', $_POST['nom']);

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
                    }
                }

                View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$insertTimbre, 'images'=> $images, 'usager_id'=>$_SESSION['user_id']]);
                exit();    
            }
        }
        // Placer un timbre existant aux enchères
        else
        {
            $timbre_id = $this->route_params['id'];

            $timbre = new Timbre;
            $timbreSelect = $timbre->selectId($timbre_id);

            if(!$timbreSelect) RequirePage::url('profil/index');
    
            CheckSession::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);
    
            $image = new Image;
            $images = $image->selectByField('timbre_id', $timbre_id);
            
            View::renderTemplate('Profil/createEnchere.html', ['timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        }
    }


    /**
     * Insérer l'enchère au DB
     */
    public function storeEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);
        
        extract($_POST);

        $timbre_id = $this->route_params['id'];

        $timbre = new Timbre;
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) RequirePage::url('profil/index');

        CheckSession::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id);

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
                $images = $image->updateImage($timbre_id, $_POST['imagePrincipale']);

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
        CheckSession::sessionAuth(FALSE);

        $timbre_id = $this->route_params['id'];

        $timbre = new Timbre;
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) RequirePage::url('profil/index');


        CheckSession::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

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
        $delete = $timbre->delete($timbre_id );

        RequirePage::url('profil/index');
        exit();   
    }

    /**
     *  Supprimer une enchère et toutes ses offres (si'l y en a) à la fois 
    */
    public function deleteEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);

        $enchere = new Enchere;
        $enchereId = $this->route_params['id'];
        $enchereSelect = $enchere->selectId($enchereId);

        if(!$enchereSelect) RequirePage::url('profil/index');
        
        CheckSession::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);
        
        if($enchereSelect) 
        {
            $enchereId = $enchereSelect['id'];
            
            // Supprimer toutes les offres de l'enchère 
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

        RequirePage::url('profil/index');
        exit();   
    }

    /**
     * Supprimer toutes les offres qu'un utilisateur a placé sur une enchère
     */
    public function deleteOffreAction()
    {
        CheckSession::sessionAuth(FALSE);

        $offre = new Offre;
        $enchere_id = $this->route_params['id'];
        $offresParEnchere= $offre->selectOffresParUsagerEnchere($_SESSION['user_id'], $enchere_id);

        if(!$offresParEnchere) RequirePage::url('profil/index');


        CheckSession::usagerAuth($offresParEnchere[0]['usager_id'], $_SESSION['user_id']);
        
        foreach($offresParEnchere as $offreParEnchere)
            $delete = $offre->delete($offreParEnchere['offre_id']);

        RequirePage::url('profil/index');
        exit();   
    }


    public function editEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);

        $enchere = new Enchere;        
        $enchere_id = $this->route_params['id'];
        $enchereSelect = $enchere->selectId($enchere_id);

        if(!$enchereSelect) RequirePage::url('profil/index');

        CheckSession::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);

        $image = new Image;
        $timbre_id = $enchereSelect['timbre_id'];
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        $enchereSelect['imagePrincipale']= $images[0]['nom'];

        
        View::renderTemplate('Profil/editEnchere.html', ['enchere'=>$enchereSelect, 'enchere_id'=> $enchere_id,'images'=>$images]);
        
    }
    
    
    public function updateEnchereAction()
    {
        CheckSession::sessionAuth(FALSE);
        
        extract($_POST);
        
        $enchere_id = $this->route_params['id'];
        
        $enchere = new Enchere;
        $enchereSelect = $enchere->selectId($enchere_id);

        if(!$enchereSelect) RequirePage::url('profil/index');

        
        CheckSession::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);
        
        $timbre_id = $enchereSelect['timbre_id'];
        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        $enchereSelect['imagePrincipale']= $images[0]['nom'];
        
        // Valider les champs
        $validation = new Validation;
        $msg=[];
        $errors = '';
        
        $validation->name('Date de debut')->value($enchereSelect['date_debut'])->required();
        $validation->name('Date de fin')->value($date_fin)->required();
        
        if(!$validation->isSuccess()) 
        $errors = $validation->displayErrors();
    else
    if($date_fin != '' && $enchereSelect['date_debut'] >= $date_fin) 
    $msg[]= 'La date de fin ne peut pas être antérieure à la date de début';


    $validation->name('Prix plancher')->value($prix_plancher)->required();

        if(!$validation->isSuccess()) 
            $errors = $validation->displayErrors();


        if(!isset($_POST['imagePrincipale']))
            $msg[] = 'Vous devez sélectionner une image principale';

            
        if ($errors || $msg) 
        {
            View::renderTemplate('Profil/editEnchere.html', ['errors'=> $errors, 'msgs'=>$msg, 'enchere'=>$_POST, 'enchere_id'=> $enchere_id,'timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
            exit();
        }
        else
        {
            $checkEnchere = $enchere->checkDuplicate($timbre_id);

            if ($checkEnchere)
            {
                $updateImages = $image->updateImage($timbre_id, $_POST['imagePrincipale']);   

                $_POST['id'] = $enchere_id;
 
                $updateEnchere = $enchere->update($_POST);

                RequirePage::url('profil/index');
                exit();   
            }
        }

    }

    public function editTimbreAction()
    {
        CheckSession::sessionAuth(FALSE);
        
        $timbre = new Timbre;        
        $timbre_id = $this->route_params['id'];
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) RequirePage::url('profil/index');

        CheckSession::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

        View::renderTemplate('Profil/editTimbre.html', ['etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$timbreSelect, 'usager_id'=>$_SESSION['user_id']]);
    }
    
    public function updateTimbreAction()
    {
        CheckSession::sessionAuth(FALSE);

        $timbre = new Timbre;        
        $timbre_id = $this->route_params['id'];
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) RequirePage::url('profil/index');
        
        CheckSession::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        extract($_POST);

        $etat = new Etat;
        $etats = $etat->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $pays = new Pays;
        $tousPays = $pays->select('nom');

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
            else
                $errors = '';

            View::renderTemplate('Profil/editTimbre.html', ['errors'=> $errors, 'etats'=>$etats, 'dimensions'=>$dimensions, 'tousPays'=>$tousPays, 'timbre'=>$_POST, 'usager_id'=>$_SESSION['user_id']]);
        } 
        // sinon
        else
        {             
            // insère le timbre à la DB
            $timbre = new Timbre;
            $_POST['id'] = $timbre_id;
            $updateTimbre = $timbre->update($_POST);

            View::renderTemplate('Profil/editImage.html', ['timbre_id'=>$timbre_id, 'usager_id'=>$_SESSION['user_id']]);
            exit();    
        }
    }

    public function editImageAction()
    {
        CheckSession::sessionAuth(FALSE);

        $timbre = new Timbre;        
        $timbre_id = $this->route_params['id'];
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) RequirePage::url('profil/index');
        
        CheckSession::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        
        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        // $images = $image->selectByField('timbre_id', $timbre_id);

        // print_r($timbreSelect);
        View::renderTemplate('Profil/editImage.html', ['timbre'=>$timbreSelect, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        exit();    
    }
}