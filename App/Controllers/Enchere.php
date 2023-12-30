<?php

namespace App\Controllers;

use \Core\View;
use \Core\Router;
use \App\Config;
use \App\Models\Offre;
use \App\Models\Timbre;
use \App\Models\Image;
use \App\Models\Etat;
use \App\Models\Pays;
use \App\Models\Dimension;
use \App\Library\Apps;
use \App\Library\Validation;

/**
 * Enchere controller
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
        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->select();
        // echo "<pre>";
        // print_r($encheres);

        $image = new Image;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            $timbre = new Timbre;
            $enchereSelect = $enchere->selectId($enchereChaque['id']);
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];

            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
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

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();
    }

    /**
     * Afficher le catalogue d'enchères
     */
    public function filterAction() 
    {
        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEnchereParNom($_POST['rechercher']);

        $image = new Image;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            $timbre = new Timbre;
            $enchereSelect = $enchere->selectId($enchereChaque['id']);
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];

            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
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
  
        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();
    }


    /**
     * Afficher la page d'enchère
     *
     * @return void
     */
    public function showAction()
    {
        $errors = '';
        
        $enchere = new \App\Models\Enchere();

        if($this->route_params);
            $enchere_id = $this->route_params['id'];

        if ($enchere_id)
        {
            $enchereSelect = $enchere->selectEnchereParId($enchere_id);
            // echo "<pre>";
            // print_r($enchereSelect);
    
            if(!$enchereSelect) Apps::url('enchere/index');
    
            $image = new Image;
            $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal');
            $imagePrincipale = $images[0]['nom'];

            $offre = new Offre;
            $offres = $offre->selectOffresParEnchere($enchereSelect['enchere_id']);
    
            if($offres) 
                $prixCourant = $offres[0]['prix'];
            else 
                $prixCourant = $enchereSelect['prix_plancher'];
    
            $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
            
            View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $enchereSelect, 'images'=>$images, 'imagePrincipale'=>$imagePrincipale, 'prixCourant'=> $prixCourant, 'nbOffres'=>$nbOffres]);
    
            exit();
        }
        else
        {
            Apps::url('index.php');
            exit();   
        }
    }


    /**
     * Insérer le timbre au DB
     */
    public function createAction()
    {
        Apps::sessionAuth(FALSE);

        // Valider si le timbre est cree
        $timbre_id = $this->route_params['id'];

        $timbre = new Timbre;
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');

        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        // Afficher les images a choissir
        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id);
        
        View::renderTemplate('Enchere/create.html', ['timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
    }


    /**
     * Insérer l'enchère au DB
     */
    public function storeAction()
    {
        Apps::sessionAuth(FALSE);
        
        extract($_POST);

        $timbre_id = $this->route_params['id'];

        $timbre = new Timbre;
        $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');

        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id);

        $validation = new Validation;
        $errors = '';

        $validation->name('Date de début')->value($date_debut)->required();
        if($date_debut != '' && $date_debut < date("Y-m-d")) 
        $errors .= '<li>'."La date de début ne peut pas être dans la passée".'<li>';
    
        $validation->name('Date de fin')->value($date_fin)->required();
        if($date_fin != '' && $date_debut >= $date_fin) 
        $errors .= '<li>'.'La date de fin ne peut pas être antérieure à la date de début'.'<li>';

        $validation->name('Prix plancher')->value($prix_plancher)->required();

        if(!$validation->isSuccess()) 
        $errors .= $validation->displayErrors();
    
        if(!isset($_POST['imagePrincipale']))
        $errors .= '<li>'.'Une image principale est obligatoire'.'<li>';

        if (!$errors) 
        {
            $enchere = new \App\Models\Enchere;
            $enchereExiste = $enchere->checkDuplicate($timbre_id);

            if (!$enchereExiste)
            {
                $images = $image->updateImage($timbre_id, $_POST['imagePrincipale']);

                $_POST['createur_id'] = $_SESSION['user_id'];
                $_POST['timbre_id'] = $timbre_id;
                $insertEnchere = $enchere->insert($_POST);

                Apps::url('profil/index');
                exit();    
            }
        }

        View::renderTemplate('Enchere/create.html', ['errors'=> $errors, 'enchere'=>$_POST, 'timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        exit();
    }

    /**
     *  Supprimer une enchère et toutes ses offres (si'l y en a) à la fois 
    */
    public function deleteAction()
    {
        Apps::sessionAuth(FALSE);

        $enchere = new \App\Models\Enchere;
        $enchereId = $this->route_params['id'];
        $enchereSelect = $enchere->selectId($enchereId);

        if(!$enchereSelect) Apps::url('profil/index');
        
        Apps::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);
        
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

        Apps::url('profil/index');
        exit();   
    }



    public function editAction()
    {
        Apps::sessionAuth(FALSE);

        $enchere = new \App\Models\Enchere;        
        $enchere_id = $this->route_params['id'];
        $enchereSelect = $enchere->selectId($enchere_id);

        if(!$enchereSelect) Apps::url('profil/index');

        Apps::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);

        $image = new Image;
        $timbre_id = $enchereSelect['timbre_id'];
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        $enchereSelect['imagePrincipale']= $images[0]['nom'];

        
        View::renderTemplate('Enchere/edit.html', ['enchere'=>$enchereSelect, 'enchere_id'=> $enchere_id,'images'=>$images]);
        
    }
    
    
    public function updateAction()
    {
        Apps::sessionAuth(FALSE);
        
        
        extract($_POST);
        
        $enchere_id = $this->route_params['id'];
        
        $enchere = new \App\Models\Enchere;
        $enchereSelect = $enchere->selectId($enchere_id);
        
        if(!$enchereSelect) Apps::url('profil/index');
        
        
        Apps::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);
        
        $timbre_id = $enchereSelect['timbre_id'];
        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        $enchereSelect['imagePrincipale']= $images[0]['nom'];
        
        // Valider les champs
        $validation = new Validation;
        $errors = '';

        $validation->name('Date de début')->value($date_debut)->required();
        if($date_debut != '' && $date_debut < date("Y-m-d")) 
        $errors .= '<li>'."La date de début ne peut pas être dans la passée".'<li>';
    
        $validation->name('Date de fin')->value($date_fin)->required();
        if($date_fin != '' && $date_debut >= $date_fin) 
        $errors .= '<li>'.'La date de fin ne peut pas être antérieure à la date de début'.'<li>';

        $validation->name('Prix plancher')->value($prix_plancher)->required();

        if(!$validation->isSuccess()) 
        $errors .= $validation->displayErrors();
    
        if(!isset($_POST['imagePrincipale']))
        $errors .= '<li>'.'Une image principale est obligatoire'.'<li>';

        if (!$errors) 
        {
            $checkEnchere = $enchere->checkDuplicate($timbre_id);

            if ($checkEnchere)
            {
                $updateImages = $image->updateImage($timbre_id, $_POST['imagePrincipale']);   

                $_POST['id'] = $enchere_id;

                $updateEnchere = $enchere->update($_POST);

                Apps::url('profil/index');
                exit();   
            }
        }
        View::renderTemplate('Enchere/edit.html', ['errors'=> $errors, 'enchere'=>$_POST, 'enchere_id'=> $enchere_id,'timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        exit();
    }


    /**
     * Miser 
     */
    public function createOffreAction()
    {
        Apps::sessionAuth(FALSE);
        
        $enchere = new \App\Models\Enchere();
        $enchere_id = $this->route_params['id'];
        

        if ($enchere_id)
        {
            $enchereSelect = $enchere->selectEnchereParId($enchere_id);
            
            if(!$enchereSelect) Apps::url('enchere/index');
            
            if ($enchereSelect['createur_id'] != $_SESSION['user_id'])
            {
                $image = new Image;
                $images = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal');
                
                $offre = new Offre;
                $offres = $offre->selectOffresParEnchere($enchereSelect['enchere_id']);
                $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
                
                if($offres) 
                    $prixCourant = $offres[0]['prix'];
                else
                    $prixCourant = $enchereSelect['prix_plancher'];
            
                $errors = '';
                extract($_POST);

                if ($_POST)
                {
                    $msg = '';
                    $validation = new Validation;
                    $validation->name('Votre mise')->value($prix)->required();
        
                    if(!$validation->isSuccess()) 
                    {
                        $errors = $validation->displayErrors();
                    } 
                    else
                    {
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
                                $msg = 'Mise réussite.';
                                $offreSelect = $offre->selectId($insertOffre);
                                $prixCourant = $offreSelect['prix'];
                                $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
                            }
                        }
                    }
                    View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'msg'=>$msg, 'enchere'=> $enchereSelect, 'images'=>$images, 'prixCourant'=> $prixCourant, 'nbOffres'=>$nbOffres]);
                }
                else
                {
                    Apps::url('Enchere/index');
                }
            }
            else
            {
                $encheres = $enchere->select();
    
                Apps::url('enchere/index');
            }
        }
    }


    public function selectAction()
    {
        
        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = [];
        $msg = null;
        $nbEncheres = null;

        // echo "<pre>";
        // print_r($_POST);
    
        // si pays n'est pas selectionne
        if($_POST['pays'] == '') unset($_POST['pays']);

        if($_POST)
        {
            $encheresSelect = $enchere->selecEnchereParFiltre($_POST);

            foreach($encheresSelect as $enchereSelect)
            {
                $encheres[] = $enchere->selectId($enchereSelect['id']);
            }
            
            if(!$encheres)
            {
                $nbEncheres = '0 résultat';
            } 
            else 
            {
                $nbEncheres = count($encheres);

                if($nbEncheres == 1) $nbEncheres .= ' résultat';
                else if ($nbEncheres > 1) $nbEncheres .= ' résultats';
            }


            $i = 0;
                    
            foreach($encheres as $enchereChaque)
            {
                $timbre = new Timbre;
                $enchereSelect = $enchere->selectId($enchereChaque['id']);
                $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
                $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
                $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
                
                $image = new Image;
                $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
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

            // echo '<pre>';
            // print_r($encheres);
            View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'msg'=>$msg, 'encheres'=>$encheres, 'filtres'=>$_POST, 'resultats'=>$nbEncheres]);
            exit();

        }     
        Apps::url('Enchere/index');
        exit();
    }


}
