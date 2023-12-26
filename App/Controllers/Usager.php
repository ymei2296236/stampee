<?php

namespace App\Controllers;

use \Core\View;
use \App\Library\Apps;
use \App\Library\Validation;

/**
 * Usager controller
 *
 * PHP version 7.0
 */
class Usager extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function createAction()
    {    
        session_destroy();

        View::renderTemplate('Usager/create.html');
    }

    public function storeAction()
    {
        $validation = new Validation;
        extract($_POST);

        $errors='';       
        $usager = new \App\Models\Usager;

        
        $validation->name('Utilisateur')->value($id)->max(50)->required()->pattern('email');
        $checkUser = $usager->checkDuplicate('id', $id);
        if ($checkUser) 
            $errors .= '<li>'.$checkUser.'</li>';
        
        $validation->name('Mot de passe')->value($password)->max(20)->min(5);
        
        $validation->name('Alias')->value($alias)->required();
        $checkAlias = $usager->checkDuplicate('alias', $_POST['alias']);
        if ($checkAlias) 
            $errors .= '<li>'.$checkAlias.'</li>';

        
        if(!$validation->isSuccess()) 
            $errors .= $validation->displayErrors();

        if(!$errors)
        {
            $options = ['cost' => 10];
            $salt = "!dL$*u";
            $passwordSalt = $_POST['password'].$salt;
            $_POST['password'] = password_hash($passwordSalt, PASSWORD_BCRYPT, $options);
            
            $insert = $usager->insert($_POST);
            session_destroy();

            $msg = 'Félicitations ! Votre compte est prêt. Veuillez vous connecter.';
            
            View::renderTemplate('Usager/login.html', ['msg'=>$msg]);
            exit();
        }
        View::renderTemplate('Usager/create.html', ['errors'=>$errors, 'usager'=>$_POST['id'], 'alias'=>$_POST['alias']]);
        exit();
    }

    public function loginAction()
    { 
        Apps::sessionAuth(TRUE);

        View::renderTemplate('Usager/login.html');
    }

    public function authAction()
    {
        Apps::sessionAuth(TRUE);

        $validation = new \App\Library\Validation;
        extract($_POST);

        $validation->name('Utilisateur')->value($id)->max(50)->required()->pattern('email');
        $validation->name('Mot de passe')->value($password)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
            View::renderTemplate('Usager/login.html', ['errors'=>$errors, 'user'=>$_POST]);
            exit();
        }

        $usager = new \App\Models\Usager;
        $checkUser = $usager->checkUser($_POST['id'], $_POST['password']);

        // si donnée saisie n'est pas valide, afficher le message
        // si valide, rédiriger ver l'accueil (chemain défini dans $usager->checkUser)
        View::renderTemplate('Usager/login.html', ['errors'=>$checkUser, 'user'=>$_POST]);
    }

    public function logoutAction()
    {
        session_destroy();
        Apps::url('index.php');
        exit();    
    }
}
