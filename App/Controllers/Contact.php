<?php

namespace App\Controllers;

use \Core\View;

/**
 * Contact controller
 *
 * PHP version 7.0
 */
class Contact extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
     */
    public function indexAction()
    {
        $select = new \App\Models\Contact;
        $contacts = $select->select();

        View::renderTemplate('Contact/index.html', ['contacts' => $contacts]);
    }

    public function addAction()
    {
        if(!empty($_POST))
        {
            // $contact = 
        }
        View::renderTemplate('Contact/add.html');
    }

    public function editAction()
    {
        print_r($this->route_params);
        $id = $this->route_params['id'];

        View::renderTemplate('Contact/edit.html', ['id' => $id]);
    }

    public function delete()
    {
        View::renderTemplate('Contact/index.html');
    }
}
