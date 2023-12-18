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
        if($_SESSION) 
            $usager_id = $_SESSION['user_id'];
        else 
            $usager_id = '';
        
        View::renderTemplate('Home/index.html', ['usager_id'=>$usager_id]);
    }
}
