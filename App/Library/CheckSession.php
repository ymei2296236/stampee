<?php
namespace App\Library;

class CheckSession {

    // valide la session
    static public function sessionAuth($status)
    {        
        // empêche un logout non voulu
        if ($status == TRUE)
        {
            if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']))
                header('location:'.\App\Config::URL_RACINE);
            else
                return true;
        }
        // impose un login
        else
        {
            if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']))
                return true;
            else
                View::renderTemplate('Home/index.html');

        }
    }

    // valide si l'usager connecté a le privilège de l'Admin
    static public function privilegeAuth()
    {
        if($_SESSION['privilege'] == 1) 
            return true;
        else
            header('location:'.\App\Config::URL_RACINE);
    }
}

?>

