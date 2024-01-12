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
use \App\Models\Favori;
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

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            // Recuperer chaque enchere
            $enchereSelect = $enchere->selectId($enchereChaque['id']);

            // Recuperer les infos de l'enchere
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

            // Recuperer les offres de l'enchere
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

            if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheres[$i]['archivee'] = true; 
            };
            
            $i++;
        }

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();
    }


    /**
     * Afficher la page d'enchère
     *
     */
    public function showAction()
    {
        $errors = '';
        $image = new Image;
        $offre = new Offre;
        $favori = new Favori;

        $enchere = new \App\Models\Enchere;
        $enchere_id = $this->route_params['id'];
        if($enchere_id) $enchereSelect = $enchere->selectId($enchere_id);

        if($enchereSelect) 
        {
            // Recuperer les images de l'enchere
            $imagesProduit = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal');
            $imagePrincipale = $imagesProduit[0]['nom'];

            // Recuperer les offres de l'enchere
            $offres = $offre->selectOffresParEnchere($enchereSelect['enchere_id']);
    
            if($offres) $prixCourant = $offres[0]['prix'];
            else $prixCourant = $enchereSelect['prix_plancher'];
    
            $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
            
            // Recuperer les favoris de l'enchere
            $favoriSelect = false
            ;
            if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != '')
            {
                $favoriSelect = $favori->selectFavori($enchereSelect["enchere_id"], $_SESSION['user_id']);
    
                if($favoriSelect) $favoriSelect = true;
            }

            if($enchereSelect['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $enchereSelect['archive'] = true; 
            };
            
            /**
             *  Afficher la section Dernieres nouveautes
             */ 
            $encheres = $enchere->selectEncheresNouveautes(true);
    
            $i = 0;

            foreach ($encheres as $enchereChaque) 
            {
                $enchere_id = $enchereChaque['id'];

                // Recupere les infos de l'enchere
                $nbOffresParEnchere = $offre->countOffres($enchere_id);
                $encheres[$i]['nbOffres'] = $nbOffresParEnchere;

                $enchereInfo = $enchere->selectId($enchere_id);
                $encheres[$i]['timbre_nom'] = $enchereInfo['timbre_nom'];
                $encheres[$i]['timbre_nom_2'] = $enchereInfo['timbre_nom_2'];
                $encheres[$i]['timbre_id'] = $enchereInfo['timbre_id'];
                $encheres[$i]['date_fin'] = $enchereInfo['date_fin'];

                // Recuperer les images de l'enchere
                $images = $image->selectByField('timbre_id', $enchereInfo['timbre_id'], 'principal');
                $encheres[$i]['image'] = $images[0]['nom'];

                // Recuperer les offres de l'enchere
                $offresToutes = $offre->selectOffresParEnchere($enchere_id);

                if ($offresToutes)
                {
                    $offreDerniere = $offresToutes[0];
                    $encheres[$i]['mise_courante'] = $offreDerniere['prix'];
                }
                else 
                {
                    $encheres[$i]['mise_courante'] = $encheres[$i]['prix_plancher'];
                }

                if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $encheres[$i]['archivee'] = true; 
                };

                $i++;
            }

            View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'enchere'=> $enchereSelect, 'encheresNouveautes'=>$encheres, 'images'=>$imagesProduit, 'imagePrincipale'=>$imagePrincipale, 'favoriSelect'=>$favoriSelect, 'prixCourant'=> $prixCourant, 'nbOffres'=>$nbOffres]);
            exit();
        }
        else
        {
            Apps::url('enchere/index');
            exit();   
        }
    }


    /**
     * Insérer le timbre au DB
     */
    public function createAction()
    {
        Apps::sessionAuth(FALSE);

        // Valider si le timbre existe
        $timbre = new Timbre;
        $timbre_id = $this->route_params['id'];
        if($timbre_id) $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');

        // Valider si usager est le createur
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);

        // Afficher les images a choissir
        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id);
        
        View::renderTemplate('Enchere/create.html', ['timbre_id'=>$timbre_id, 'images'=>$images, 'usager_id'=>$_SESSION['user_id']]);
        exit();
    }


    /**
     * Insérer l'enchère au DB
     */
    public function storeAction()
    {
        Apps::sessionAuth(FALSE);

        // Valider si le timbre existe
        $timbre = new Timbre;
        $timbre_id = $this->route_params['id'];
        if($timbre_id) $timbreSelect = $timbre->selectId($timbre_id);

        if(!$timbreSelect) Apps::url('profil/index');

        // Valider si usager est le createur
        Apps::usagerAuth($timbreSelect['createur_id'], $_SESSION['user_id']);
        
        // Afficher les images a choissir
        $image = new Image;
        $images = $image->selectByField('timbre_id', $timbre_id);
        
        // Valider les champs
        $validation = new Validation;
        $errors = '';
        extract($_POST);

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

        // Valider si l'enchere existe
        $enchere = new \App\Models\Enchere;
        $enchere_id = $this->route_params['id'];
        if($enchere_id) $enchereSelect = $enchere->selectId($enchere_id);

        if($enchereSelect)  
        {
            // Valider si l'usager est le createur
            Apps::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);

            // Supprimer toutes les offres de l'enchère 
            $offre = new Offre;
            $offres = $offre->selectByField('enchere_id', $enchere_id, 'prix', 'DESC');
            
            if($offres) 
            {
                $i = 0; 
                
                foreach($offres as $offreSelect)
                {
                    $delete = $offre->delete($offres[$i]['id']);
                    $i++;
                }
            }
            
            // Supprimer favoris du timbres
            $favori = new Favori;
            $favorisSelect = $favori->selectFavoriParEnchereId($enchere_id);
            
            if($favorisSelect)
            {
                $i = 0;
                
                foreach ($favorisSelect as $favoriSelect) 
                {
                    $delete = $favori->deleteFavori($enchere_id, $favoriSelect['usager_id']);
                    $i++;
                }
            }
            
            // Supprimer l'enchère du timbres
            $delete = $enchere->delete($enchere_id);  
        }

        Apps::url('profil/index');
        exit();   
    }


    /**
     * Modifier les infos d'enchere
     */
    public function editAction()
    {
        Apps::sessionAuth(FALSE);

        $enchere = new \App\Models\Enchere;        
        $enchere_id = $this->route_params['id'];
        if($enchere_id) $enchereSelect = $enchere->selectId($enchere_id);

        if(!$enchereSelect) Apps::url('profil/index');

        Apps::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id']);

        $image = new Image;
        $timbre_id = $enchereSelect['timbre_id'];
        $images = $image->selectByField('timbre_id', $timbre_id, 'principal');
        $enchereSelect['imagePrincipale']= $images[0]['nom'];

        
        View::renderTemplate('Enchere/edit.html', ['enchere'=>$enchereSelect, 'enchere_id'=> $enchere_id,'images'=>$images]);
    }
    
    /**
     * Enregistrer la modification
     */
    public function updateAction()
    {
        Apps::sessionAuth(FALSE);
         
        extract($_POST);

        // Valider si l'enchere existe
        $enchere = new \App\Models\Enchere;
        $enchere_id = $this->route_params['id'];
        if($enchere_id) $enchereSelect = $enchere->selectId($enchere_id);

        if(!$enchereSelect) Apps::url('profil/index');

        // Valider si l'usager est le createur
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
        
        // Valider si l'enchere existe
        $enchere = new \App\Models\Enchere;
        $enchere_id = $this->route_params['id'];
        if($enchere_id) $enchereSelect = $enchere->selectId($enchere_id);

        if ($enchereSelect)
        {
            // Valider si l'usager est le createur
            Apps::usagerAuth($enchereSelect['createur_id'], $_SESSION['user_id'], false);

            // Recuperer les infos de l'enchere
            $image = new Image;
            $imagesProduit = $image->selectByField('timbre_id', $enchereSelect['timbre_id'], 'principal');

            $imagePrincipale = $imagesProduit[0]['nom'];
            
            $offre = new Offre;
            $offres = $offre->selectOffresParEnchere($enchereSelect['enchere_id']);
            $nbOffres = $offre->countOffres($enchereSelect['enchere_id']);
            
            if($offres) $prixCourant = $offres[0]['prix'];
            else $prixCourant = $enchereSelect['prix_plancher'];
        
            $favori = new Favori;
            $favoriSelect = $favori->selectFavori($enchereSelect["enchere_id"], $_SESSION['user_id']);

            if($favoriSelect) $favoriSelect = 1;

            $errors = '';
            extract($_POST);

            if ($_POST)
            {
                $msg = '';
                $validation = new Validation;
                $validation->name('Votre mise')->value($prix)->required();
                
                // Valider les champs 
                if(!$validation->isSuccess()) 
                {
                    $errors = $validation->displayErrors();
                } 
                else
                {
                    // Valider l'offre
                    if($prix <= $prixCourant)
                    {
                        $errors = 'Votre mise doit être plus grande que la mise courante '. $prixCourant .' $.';
                    }
                    else
                    {
                        // Inserer l'offre
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

                /**
                 * Afficher la secion Dernieres nouveautes
                 */
                $encheres = $enchere->selectEncheresNouveautes(true);
        
                $i = 0;

                foreach ($encheres as $enchereChaque) 
                {
                    $enchere_id = $enchereChaque['id'];

                    $nbOffresParEnchere = $offre->countOffres($enchere_id);
                    $encheres[$i]['nbOffres'] = $nbOffresParEnchere;

                    $enchereInfo = $enchere->selectId($enchere_id);

                    $encheres[$i]['timbre_nom'] = $enchereInfo['timbre_nom'];
                    $encheres[$i]['timbre_nom_2'] = $enchereInfo['timbre_nom_2'];
                    $encheres[$i]['timbre_id'] = $enchereInfo['timbre_id'];
                    $encheres[$i]['date_fin'] = $enchereInfo['date_fin'];

                    $images = $image->selectByField('timbre_id', $enchereInfo['timbre_id'], 'principal');
                    $encheres[$i]['image'] = $images[0]['nom'];

                    $offresToutes = $offre->selectOffresParEnchere($enchere_id);

                    if ($offresToutes)
                    {
                        $offreDerniere = $offresToutes[0];
                        $encheres[$i]['mise_courante'] = $offreDerniere['prix'];
                    }
                    else 
                    {
                        $encheres[$i]['mise_courante'] = $encheres[$i]['prix_plancher'];
                    }

                    if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                    {
                        $encheres[$i]['archivee'] = true; 
                    };

                    $i++;
                }

                View::renderTemplate('Enchere/show.html', ['errors'=> $errors, 'msg'=>$msg, 'enchere'=> $enchereSelect, 'encheresNouveautes'=>$encheres, 'images'=>$imagesProduit, 'imagePrincipale'=>$imagePrincipale,'favoriSelect'=>$favoriSelect, 'prixCourant'=> $prixCourant, 'nbOffres'=>$nbOffres]);
            }
            else
            {
                Apps::url('Enchere/index');
            }
        }
        else
        {
            Apps::url('enchere/index');
        }
    }


    /**
     * Rechercher encheres par mot de cle
     */
    public function rechercherAction() 
    {
        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEnchereParNom($_GET['motDeCle']);

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        // Calculer le nombre d'encheres filtres
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

        foreach($encheres as $enchereChaque)
        {
            // Recuperer chaque enchere
            
            $enchereSelect = $enchere->selectId($enchereChaque['id']);

            // Recuperer les infos de l'enchere
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

            // Recuperer les offres de l'enchere
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

            if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheres[$i]['archivee'] = true; 
            };

            $i++;
        }

        // echo "<pre>";
        // print_r($encheres);

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres, 'resultats'=>$nbEncheres]);
        exit();
    }



    /**
     * Selectionner encheres par filtres predefinis
     */
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

        // Reinitialiser la valeur du champs pays
        if($_POST['pays'] == '') unset($_POST['pays']);

        if($_POST)
        {
            // Filtrage d'encheres
            $encheresSelect = $enchere->selecEnchereParFiltre($_POST);
      
            foreach($encheresSelect as $enchereSelect)
            {
                $encheres[] = $enchere->selectId($enchereSelect['id']);
            }

            // Calculer le nombre d'encheres filtres
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
                // Recuperer les infos de l'enchere
                $enchereSelect = $enchere->selectId($enchereChaque['enchere_id']);
                
                
                $timbre = new Timbre;
                $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
                $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
                $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];

                // Recuperer les images de l'enchere
                $image = new Image;
                $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
                $encheres[$i]['image'] = $images[0]['nom'];

                // Recuperer les offres de l'enchere
                $offre = new Offre;
                $offresToutes = $offre->selectOffresParEnchere($enchereChaque['enchere_id']);

                if ($offresToutes)
                {
                    $offreDerniere = $offresToutes[0];
                    $encheres[$i]['mise_courante'] = $offreDerniere['prix'];
                }
                else 
                {
                    $encheres[$i]['mise_courante'] = $encheres[$i]['prix_plancher'];
                }

                if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $encheres[$i]['archivee'] = true; 
                };

                $i++;
            }




            View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'msg'=>$msg, 'encheres'=>$encheres, 'filtres'=>$_POST, 'resultats'=>$nbEncheres]);
            exit();
        }     
        Apps::url('Enchere/index');
        exit();
    }

    /**
     * Mettre en favori une enchere
     */
    public function createFavoriAction()
    {
        Apps::sessionAuth(FALSE);

        $enchere = new \App\Models\Enchere();
        $enchere_id = $this->route_params['id'];
        if($enchere_id) $enchereSelect = $enchere->selectId($enchere_id);

        $favori = new Favori;
        $_POST['enchere_id'] = $enchere_id;
        $_POST['usager_id'] = $_SESSION['user_id'];
        $_POST['timbre_id'] = $enchereSelect['timbre_id'];
        $_POST['createur_id'] = $enchereSelect['createur_id'];
        $insertFavori = $favori->insert($_POST);

        Apps::url('Enchere/show/'.$_POST['enchere_id']);  
    }

    /**
     * Enlever favori
     */
    public function deleteFavoriAction()
    {
        Apps::sessionAuth(FALSE);
        
        $favori = new Favori;
        $_POST['enchere_id'] = $this->route_params['id'];
        $_POST['usager_id'] = $_SESSION['user_id'];
        $delete = $favori->deleteFavori($_POST['enchere_id'], $_POST['usager_id']);

        Apps::url('Enchere/show/'.$_POST['enchere_id']);
    }

    /**
     * Afficher encheres par nouveautes (un des CTAs sur la page d'accueil)
     */
    public function selectEncheresNouveautes()
    {
        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEncheresNouveautes();

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {

            // Recuperer les infos de l'enchere
            $enchereSelect = $enchere->selectId($enchereChaque['id']);
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

            // Recuperer les offres de l'enchere
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
        // echo "<pre>";
        // print_r($encheres);
        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();
    }



    public function selectEncheresEnCours() 
    {
        $data = array();

        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEncheresEnCours();

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            // Recuperer chaque enchere
            $enchereSelect = $enchere->selectId($enchereChaque['id']);

            // Recuperer les infos de l'enchere
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

            // Recuperer les offres de l'enchere
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

            if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheres[$i]['archivee'] = true; 
            };
            
            $i++;
        }
        // echo "<pre>";
        // print_r($encheres);

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();

    }

    public function selectEncheresArchivees() 
    {
        $data = array();

        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEncheresArchivees();

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            // Recuperer chaque enchere
            $enchereSelect = $enchere->selectId($enchereChaque['id']);

            // Recuperer les infos de l'enchere
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

            // Recuperer les offres de l'enchere
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

            if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheres[$i]['archivee'] = true; 
            };
            
            $i++;
        }

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();

    }


    public function selectEncheresPrixEleve() 
    {
        $data = array();

        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEncheresNouveautes();

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            // Recuperer chaque enchere
            $enchereSelect = $enchere->selectId($enchereChaque['id']);

            // Recuperer les infos de l'enchere
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

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

            if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheres[$i]['archivee'] = true; 
            };
            
            $i++;

        }
        

        usort($encheres, array($this, "prixEleve"));

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();
    }


    public function selectEncheresPrixBas() 
    {
        $data = array();

        $etat = new Etat;
        $etats = $etat->select();

        $pays = new Pays;
        $paysTous = $pays->select();

        $dimension = new Dimension;
        $dimensions = $dimension->select();

        $enchere = new \App\Models\Enchere;
        $encheres = $enchere->selectEncheresNouveautes();

        $image = new Image;
        $timbre = new Timbre;
        $offre = new Offre;

        $i = 0;

        foreach($encheres as $enchereChaque)
        {
            // Recuperer chaque enchere
            $enchereSelect = $enchere->selectId($enchereChaque['id']);

            // Recuperer les infos de l'enchere
            $timbreSelect = $timbre->selectId($enchereSelect['timbre_id']);
            $encheres[$i]['timbre_nom'] = $timbreSelect['nom'];
            $encheres[$i]['timbre_nom_2'] = $timbreSelect['nom_2'];
            $encheres[$i]['enchere_id'] = $encheres[$i]['id'];

            // Recuperer les images de l'enchere
            $images = $image->selectByField('timbre_id', $enchereChaque['timbre_id'], 'principal');
            $encheres[$i]['image'] = $images[0]['nom'];

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

            if($encheres[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
            {
                $encheres[$i]['archivee'] = true; 
            };
            
            $i++;

        }
        

        usort($encheres, array($this, "prixBas"));

        View::renderTemplate('Enchere/index.html', ['etats'=> $etats, 'paysTous'=> $paysTous, 'dimensions'=>$dimensions, 'encheres'=>$encheres]);
        exit();
    }

    // Trier les encheres par prix eleve
    private function prixEleve($a, $b) 
    {
        return strcmp($b['mise_courante'], $a['mise_courante']);
    }

    // Trier les encheres par prix eleve
    private function prixBas($a, $b) 
    {
        return strcmp($a['mise_courante'], $b['mise_courante']);
    }
    
}
