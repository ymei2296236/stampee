<?php
namespace App\Library;

use \Core\View;
use \App\Library\RequirePage;

class CheckSession 
{
    // valide la session
    static public function sessionAuth($status)
    {        
        // empêcher une connextion répétitive
        if ($status == TRUE)
        {
            if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']))
            {
                RequirePage::url('index.php');
                exit();
            }
            else
            {
                return true;
            }
        }
        // imposer une connextion
        else
        {
            if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']))
            {
                return true;
            }
            else
            {
                RequirePage::url('usager/login');
                exit();
            }
        }
    }

    static public function usagerAuth($createur_id, $usager_id)
    {
        if ($createur_id == $usager_id)
        {
            return true;
        }
        else
        {
            RequirePage::url('profil/index');
            exit(); 
        }
    }

}

?>

