<?php

namespace App\Models;

use PDO;
use \App\Library\RequirePage;


class Usager extends CRUD 
{
    protected $table = 'usager';
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'password', 'alias', 'privilege_id'];

    // valider l'inscription
    public function checkDuplicate($field='id', $value) 
    {
        $db = static::getDB();

        $sql = "SELECT * FROM $this->table WHERE $field = '$value'";
        $stmt = $db->prepare($sql);
        $stmt->execute(array($value));
        $count = $stmt->rowCount();

        if($count === 1) {
            $error = "{$value} déjà existe.";
            return $error;
        }
    }

    // valider login
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

                RequirePage::url('index.php');
                exit();
            }
            else
            {
                $errors = "La combinaison de l’ID utilisateur et le mot de passe entré n’est pas valide.";
                return $errors;
            }
        }
        else
        {
            $errors = "La combinaison de l’ID utilisateur et le mot de passe entré n’est pas valide.";
            return $errors;
        }
    }

}

?>