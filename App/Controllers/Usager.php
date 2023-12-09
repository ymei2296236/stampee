<?php

namespace App\Controllers;

use \Core\View;

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

        View::renderTemplate('Usager/create.html');
    }

    public function storeAction()
    {
        $validation = new \App\Library\Validation;
        extract($_POST);

        $validation->name('Utilisateur')->value($id)->max(50)->required()->pattern('email');
        $validation->name('Mot de passe')->value($password)->max(20)->min(5);
        $validation->name('Alias')->value($alias)->required();
        
        if(!$validation->isSuccess()) 
        {
            $errorsValidation = $validation->displayErrors();

            View::renderTemplate('Usager/create.html', ['errors'=>$errorsValidation, 'usager'=>$_POST['id'], 'alias'=>$_POST['alias']]);
            exit();
        }

        extract($_POST);

        $usager = new \App\Models\Usager;
        $checkUser = $usager->checkDuplicate('id', $_POST['id']);
        
        if ($checkUser)
        {
            View::renderTemplate('Usager/create.html', ['errors'=>$checkUser, 'usager'=>$_POST['id'], 'alias'=>$_POST['alias']]);
            exit();
        }

        // $usager = new \App\Models\Usager;
        $checkAlias = $usager->checkDuplicate('alias', $_POST['alias']);

        if ($checkAlias)
        {
            View::renderTemplate('Usager/create.html', ['errors'=>$checkAlias, 'usager'=>$_POST['id'], 'alias'=>$_POST['alias']]);
            exit();
        }

        $options = ['cost' => 10];
        $salt = "!dL$*u";
        $passwordSalt = $_POST['password'].$salt;
        $_POST['password'] = password_hash($passwordSalt, PASSWORD_BCRYPT, $options);

        $insert = $usager->insert($_POST);
        header("location:/stampee/public/index.php");
        exit();

    }
}
