<?php


namespace App\Controllers;

use \Core\View;

/**
 * Login controller
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
        \App\Library\CheckSession::sessionAuth(TRUE);

        View::renderTemplate('Login/index.html');
    }

    public function authAction()
    {
        \App\Library\CheckSession::sessionAuth(TRUE);

        $validation = new \App\Library\Validation;
        extract($_POST);
        $validation->name('Utilisateur')->value($id)->max(50)->required()->pattern('email');
        $validation->name('Mot de passe')->value($password)->max(20)->min(5);

        if(!$validation->isSuccess()) {
            $errors = $validation->displayErrors();
            View::renderTemplate('Login/index.html', ['errors'=>$errors, 'user'=>$_POST]);
            exit();
        }

        $usager = new \App\Models\Usager;
        $checkUser = $usager->checkUser($_POST['id'], $_POST['password']);

        print_r($checkUser);
        View::renderTemplate('Login/index.html', ['errors'=>$checkUser, 'user'=>$_POST]);

    }

    public function logout()
    {
        session_destroy();
        header("location:/stampee/public/index.php");
        exit();    }
}
