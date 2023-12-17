<?php

namespace App\Controllers;

use \Core\View;
use \App\Library\RequirePage;
use \App\Library\CheckSession;
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

        $msg=[];        
        $errors='';       
        $usager = new \App\Models\Usager;
 
        // echo "<pre>";
        
        $validation->name('Utilisateur')->value($id)->max(50)->required()->pattern('email');
        
        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        }
        else
        {
            if($id) 
            {
                $checkUser = $usager->checkDuplicate('id', $id);
                if ($checkUser) $msg[] = $checkUser;
            }
        }

        $validation->name('Mot de passe')->value($password)->max(20)->min(5);

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        }
        
        $validation->name('Alias')->value($alias)->required();

        if(!$validation->isSuccess()) 
        {
            $errors = $validation->displayErrors();
        }
        else
        {
            $checkAlias = $usager->checkDuplicate('alias', $_POST['alias']);
            if ($checkAlias) $msg[] = $checkAlias;
        }

        if($errors || $msg)
        {
            View::renderTemplate('Usager/create.html', ['errors'=>$errors, 'msgs'=>$msg, 'usager'=>$_POST['id'], 'alias'=>$_POST['alias']]);
            exit();
        }
        else
        {
            $options = ['cost' => 10];
            $salt = "!dL$*u";
            $passwordSalt = $_POST['password'].$salt;
            $_POST['password'] = password_hash($passwordSalt, PASSWORD_BCRYPT, $options);
            
            // $usager = new \App\Models\Usager;
            $insert = $usager->insert($_POST);
            session_destroy();

            $msg[] = 'Félicitations ! Votre compte est prêt. Veuillez vous connecter.';
            View::renderTemplate('Usager/login.html', ['msgs'=>$msg]);
            exit();
        }
    }

    public function loginAction()
    { 
        CheckSession::sessionAuth(TRUE);

        View::renderTemplate('Usager/login.html');
    }

    public function authAction()
    {
        CheckSession::sessionAuth(TRUE);

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
        RequirePage::url('index.php');
        exit();    
    }
}
