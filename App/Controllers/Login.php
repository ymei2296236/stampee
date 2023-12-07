<?php

namespace App\Controllers;

use \Core\View;

/**
 * Contact controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        View::renderTemplate('Auth/index.html');

    }

    public function auth()
    {
        // CheckSession::sessionAuth(TRUE);

        // $validation = new Validation;
        // extract($_POST);
        // $validation->name('Utilisateur')->value($username)->max(50)->required()->pattern('email');
        // $validation->name('Mot de passe')->value($password)->max(20)->min(5);

        // if(!$validation->isSuccess()) {
        //     $errors = $validation->displayErrors();
        //     return Twig::render('auth/index.php', ['errors'=>$errors, 'user'=>$_POST]);
        //     exit();
        // }
        $usager = new Usager;
        $checkUser = $usager->checkUser($_POST['username'], $_POST['password']);

        Twig::render('auth/index.php', ['errors'=>$checkUser, 'usager'=>$_POST]);    
    }

    public function logout()
    {
        // session_destroy();
        // RequirePage::url('login');
    }
}
