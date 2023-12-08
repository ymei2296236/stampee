<?php

namespace App\Controllers;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Home extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        // \App\Library\CheckSession::sessionAuth(TRUE);

        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') 
        {
            $usager_id = $_SESSION['user_id'];
            $usager = new \App\Models\Usager;
            $select = $usager->selectId($usager_id);
        }
        else
        {
            $select = '';
        }

        View::renderTemplate('Home/index.html', ['usager'=>$select]);
    }
}
