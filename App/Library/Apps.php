<?php
namespace App\Library;

use \Core\View;
use \Core\Model;
use \App\Config;

class Apps
{
    /**
     * Valider la session
     */
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

    /**
     * Valider si l'usager est le createur
     */
    static public function usagerAuth($createur_id, $usager_id, $same=true)
    {
        // L'usager et le createur doivent etre identiques
        if ($same == true)
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
        // L'usager et le createur ne doivent pas etre identiques
        else
        {
            if ($createur_id != $usager_id)
            {
                return true;
            }
            else
            {
                Apps::url('Enchere/index');
                exit(); 
            }
        }
    }


    /**
     * Televerser plusieurs images
     */
    static public function reArrayFiles($file)
    {
        $file_ary = array();
        $file_count = count($file['name']);
        $file_key = array_keys($file);
        
        for($i = 0; $i < $file_count; $i++)
        {
            foreach($file_key as $val)
            {
                $file_ary[$i][$val] = $file[$val][$i];
            }
        }
        return $file_ary;
    }

    /**
     * Diriger vers une page
     */
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

