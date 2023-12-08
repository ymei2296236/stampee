<?php

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
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
        // print_r($_POST);
        if(isset($_POST['id'], $_POST['password'] ))
        {
            if($_POST['id'] != '' && $_POST['password'] != '') 
            {

            extract($_POST);

            $usager = new \App\Models\Usager;
            $checkUser = $usager->checkDuplicate($_POST['id']);
            
            if ($checkUser)
            {
                View::renderTemplate('Usager/create.html', ['errors'=>$checkUser, 'usager'=>$_POST['id']]);
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
        else
        {
            View::renderTemplate('Usager/create.html');
        }
    }



}
