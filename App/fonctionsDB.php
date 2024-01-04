<?php
	$connexion = connexionDB();
		
	/**
	 * Connection avec la base de données
	 */
	function connexionDB() {
		define('DB_HOST', 'localhost');
		define('DB_USER', 'root');
		define('DB_PASSWORD', 'root');			// MAC
		//define('DB_PASSWORD', '');			// Windows

		$laConnexion = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
				
		if (!$laConnexion) {
			// La connexion n'a pas fonctionné
			die('Erreur de connexion à la base de données. ' . mysqli_connect_error());
		}
		
		$db = mysqli_select_db($laConnexion, 'stampee');

		if (!$db) {
			die ('La base de données n\'existe pas.');
		}
		
		mysqli_query($laConnexion, 'SET NAMES "utf8"');
		return $laConnexion;
	}


	/**
	 * Exécute la requête SQL
	 * Si le paramètre $insert est true, retourne l'id de la ressource ajoutée à la db
	 */
	function executeRequete($requete, $insert = false) {
		global $connexion;
		if ($insert) {
			mysqli_query($connexion, $requete);
			return $connexion->insert_id;
		} else {
			$resultats = mysqli_query($connexion, $requete);
			return $resultats;
		}
	}

    function selectEncheresEnCours()
    {      
        $sql=
            "SELECT enchere.id AS enchere_id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
            FROM enchere 
			JOIN timbre
			on enchere.timbre_id = timbre.id
            WHERE date_fin > NOW()";

		return executeRequete($sql);
	}

    function selectEncheresArchivees()
    {      
        $sql=
            "SELECT enchere.id AS enchere_id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
            FROM enchere 
			JOIN timbre
			on enchere.timbre_id = timbre.id
            WHERE date_fin < NOW()";

		return executeRequete($sql);
	}

	function selectEncheresPrixBas()
	{
		$sql=
		"SELECT enchere.id AS enchere_id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
		FROM enchere 
		JOIN timbre
		on enchere.timbre_id = timbre.id
		ORDER by prix ASC";
	
		return executeRequete($sql);
	}

	function selectEncheresNouveautes()
	{
		$sql=
		"SELECT enchere.id AS enchere_id, timbre.id AS timbre_id, timbre.nom AS timbre_nom, timbre.nom_2 AS timbre_nom_2, prix_plancher, date_fin, coup_de_coeur
		FROM enchere 
		JOIN timbre
		on enchere.timbre_id = timbre.id
		ORDER by enchere.id DESC";
	
		return executeRequete($sql);
	}



	function select($table)
    {
        $sql="SELECT * FROM $table";
		return executeRequete($sql);

    }
	
    function selectByField($table, $column, $value, $field='id')
    {
        $sql="SELECT * FROM $table WHERE $column = '$value' ORDER BY $field DESC" ;

		return executeRequete($sql);
    }

	function selectId($table, $value)
    {
        $sql="SELECT * FROM $table WHERE id = '$value'";
		return executeRequete($sql);
    }

    function selectOffresParEnchere($enchereId)
    {
    
        $sql = 
        "SELECT offre.id AS offre_id, prix, usager_id
        FROM offre 
        LEFT JOIN enchere 
        ON enchere.id = offre.enchere_id  
        WHERE enchere.id = '$enchereId'
        ORDER BY prix DESC";    

		return executeRequete($sql);

    }

?>

