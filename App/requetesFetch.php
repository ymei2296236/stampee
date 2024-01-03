<?php

// namespace App;

require_once('fonctionsDB.php');
// print_r($data);
// print_r('$data');

// use \Core\View;

use \App\Models\Enchere;

$request_payload = file_get_contents('php://input');
$data = json_decode($request_payload, true);

if (isset($data['action']))
{
    switch($data['action'])
    {
        case 'selectEncheresEnCours':  
            $data = array();
            $encheres = selectEncheresEnCours(); 

            while ($enchere = mysqli_fetch_assoc($encheres)) 
            { 
                $data[] = $enchere;
            }

            $i = 0;

            foreach($data as $enchereChaque)
            {
                $images = selectByField('image', 'timbre_id', $enchereChaque['timbre_id'], 'principal');
                $image = mysqli_fetch_assoc($images); 
                $data[$i]['image'] = $image['nom'];

                $offresToutes = selectOffresParEnchere($enchereChaque['enchere_id']);
                $offreDereniere = mysqli_fetch_assoc($offresToutes); 

                if($offreDereniere) $data[$i]['offre'] = $offreDereniere['prix'];
                else $data[$i]['offre'] = $data[$i]['prix_plancher'];

                if($data[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $data[$i]['archive'] = true; 
                };
                $i++;
            }
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($data);	
            break;
        
        case 'selectEncheresPrixBas':
            $data = array();
            $encheres = selectEncheresNouveautes(); 

            while ($enchere = mysqli_fetch_assoc($encheres)) 
            { 
                $data[] = $enchere;
            }

            $i = 0;

            foreach($data as $enchereChaque)
            {
                $images = selectByField('image', 'timbre_id', $enchereChaque['timbre_id'], 'principal');
                $image = mysqli_fetch_assoc($images); 
                $data[$i]['image'] = $image['nom'];

                $offresToutes = selectOffresParEnchere($enchereChaque['enchere_id']);
                $offreDereniere = mysqli_fetch_assoc($offresToutes); 

                if($offreDereniere) $data[$i]['offre'] = $offreDereniere['prix'];
                else $data[$i]['offre'] = $data[$i]['prix_plancher'];

                if($data[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $data[$i]['archive'] = true; 
                };

                $i++;
            }

            function cmp($a, $b) {
                return strcmp($a['offre'], $b['offre']);
            }
            
            usort($data, "cmp");
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($data);	
            break;

        case 'selectEncheresPrixEleve':
            $data = array();
            $encheres = selectEncheresNouveautes(); 

            while ($enchere = mysqli_fetch_assoc($encheres)) 
            { 
                $data[] = $enchere;
            }

            $i = 0;

            foreach($data as $enchereChaque)
            {
                $images = selectByField('image', 'timbre_id', $enchereChaque['timbre_id'], 'principal');
                $image = mysqli_fetch_assoc($images); 
                $data[$i]['image'] = $image['nom'];

                $offresToutes = selectOffresParEnchere($enchereChaque['enchere_id']);
                $offreDereniere = mysqli_fetch_assoc($offresToutes); 

                if($offreDereniere) $data[$i]['offre'] = $offreDereniere['prix'];
                else $data[$i]['offre'] = $data[$i]['prix_plancher'];

                if($data[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $data[$i]['archive'] = true; 
                };

                $i++;
            }

            function cmp($a, $b) {
                return strcmp($b['offre'], $a['offre']);
            }
            
            usort($data, "cmp");
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($data);	
            break;

        case 'selectEncheresNouveautes':
            $data = array();
            $encheres = selectEncheresNouveautes(); 

            while ($enchere = mysqli_fetch_assoc($encheres)) 
            { 
                $data[] = $enchere;
            }

            $i = 0;

            foreach($data as $enchereChaque)
            {
                $images = selectByField('image', 'timbre_id', $enchereChaque['timbre_id'], 'principal');
                $image = mysqli_fetch_assoc($images); 
                $data[$i]['image'] = $image['nom'];

                $offresToutes = selectOffresParEnchere($enchereChaque['enchere_id']);
                $offreDereniere = mysqli_fetch_assoc($offresToutes); 

                if($offreDereniere) $data[$i]['offre'] = $offreDereniere['prix'];
                else $data[$i]['offre'] = $data[$i]['prix_plancher'];

                if($data[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $data[$i]['archive'] = true; 
                };
                
                $i++;
            }
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($data);	
            break;

        case 'selectEncheresArchivees':         
            $data = array();
            $encheres = selectEncheresArchivees(); 

            while ($enchere = mysqli_fetch_assoc($encheres)) 
            { 
                $data[] = $enchere;
            }

            $i = 0;

            foreach($data as $enchereChaque)
            {
                $images = selectByField('image', 'timbre_id', $enchereChaque['timbre_id'], 'principal');
                $image = mysqli_fetch_assoc($images); 
                $data[$i]['image'] = $image['nom'];

                $offresToutes = selectOffresParEnchere($enchereChaque['enchere_id']);
                $offreDereniere = mysqli_fetch_assoc($offresToutes); 

                if($offreDereniere) $data[$i]['offre'] = $offreDereniere['prix'];
                else $data[$i]['offre'] = $data[$i]['prix_plancher'];

                if($data[$i]['date_fin'] < date("Y-m-d h:i:sa")) 
                {
                    $data[$i]['archive'] = true; 
                };

                $i++;
            }
            
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($data);	
            break;
    }
}



?>