<?php

namespace App\Models;

use PDO;

use Model;

class Usager extends CRUD 
{
    protected $table = 'usager';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'password', 'alias', 'privilege_id'];

    // login authentication

    public function checkUser($id, $password) 
    {
        $db = static::getDB();

        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($id));
        $count = $stmt->rowCount();

        if($count === 1) {
            $salt = "!dL$*u";
            $passwordSalt = $password.$salt;
            $info_user = $stmt->fetch();

            if(password_verify($passwordSalt, $info_user['password']))
            {
                session_regenerate_id();
                $_SESSION['user_id'] = $info_user['id'];
                $_SESSION['privilege'] = $info_user['privilege_id'];
                $_SESSION['fingerPrint'] = md5($_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR']);

                header('location:'.\App\Config::URL_RACINE);
                exit();
            }
            else
            {
                $errors = "<ul><li>Verifiez le mot de passe</li></ul>";
                return $errors;
            }
        }
        else
        {
            $errors = "<ul><li>Verifiez le nom de l'utilisateur</li></ul>";
            return $errors;
        }
    }

}

?>