<?php
namespace App\Library;

use \Core\View;
use \Core\Model;
use \App\Config;

class Apps
{
    // valide la session
    static public function sessionAuth($status)
    {        
        // empêcher une connextion répétitive
        if ($status == TRUE)
        {
            if(isset($_SESSION['fingerPrint']) && $_SESSION['fingerPrint'] === md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']))
            {
                Apps::url('index.php');
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
                Apps::url('usager/login');
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
            Apps::url('profil/index');
            exit(); 
        }
    }

    static public function idCheck($condition)
    {
        if($condition)
        {
            return true;
        } 
        else
        {
            Apps::url('enchere/index');
            exit();
        }
    }

    static public function reArrayFiles($file)
    {
        $file_ary = array();
        $file_count = count($file['name']);
        $file_key = array_keys($file);
        
        for($i=0;$i<$file_count;$i++)
        {
            foreach($file_key as $val)
            {
                $file_ary[$i][$val] = $file[$val][$i];
            }
        }
        return $file_ary;
    }

    static public function library($library) 
    {
        return require_once('library/'.$library.'.php');
    }

    static public function url($url)
    {
        header('location:'. Config::URL_RACINE.$url);
        exit();
    }

    /**
     * Exécute la requête SQL
     * Si le paramètre $insert est true, retourne l'id de la ressource ajoutée à la db
     */
    static public function executeRequete($requete, $insert = false) 
    {

        print_r($db);

        if ($insert) 
        {
            mysqli_query($connexion, $requete);
            return $connexion->insert_id;
        } 
        else 
        {
            $stmt = $db->query($sql);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }

}

?>

