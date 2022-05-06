<?php

/**
 * Récupération de tous les films
 * Méthode : GET
 */

// Retour d'en-tête
// header('Access-Control-Allow-Origin: http://api.test');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Récupération de la méthode
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Si la méthod est différente de "POST"
 */

if ($method !== 'POST')
    {
        // Récupère ou définit le code de réponse HTTP
        header('405 Method Not Allowed', true, 405);
        // http_response_code(405); si ne marche pas aves le précedant

        echo json_encode(
            [
                'status' => 405,
                'message' => 'Method Not Allowed'
            ]
        );
        exit;
    }

// Connexion à la BDD
require_once 'connexion.php';

// Récupère les données envoyées en POST
$datas = json_decode(file_get_contents('php://input'), true);

// echo json_encode($data['director']);

/**
 * Si tous les champs sont bien remplis, on insère en BDD
 */
if (
    !empty($datas['title']) &&
            !empty($datas['description']) &&
            !empty($datas['date']) &&
            !empty($datas['time']) &&
            !empty($datas['director']) &&
            !empty($datas['image']) &&            
            !empty($datas['trailer'])
    )
        {
            // Nettoie les données
            foreach ($datas as $key => $value) 
                {
                    $data[$key] = htmlspecialchars(strip_tags($value));
                }
            // Insertion en BDD
            $query = $db->prepare('INSERT INTO movies (title, description, date, time, director, image, trailer) VALUES (:title, :description, :date, :time, :director, :image, :trailer)');

            $query->bindValue(':title', $datas['title']);
            $query->bindValue(':description', $datas['description']);
            $query->bindValue(':date', $datas['date']);
            $query->bindValue(':time', $datas['time'], PDO::PARAM_INT);
            $query->bindValue(':director', $datas['director']);
            $query->bindValue(':image', $datas['image']);
            $query->bindValue(':trailer', $datas['trailer']);
            $query->execute();

            // Récupération de l'ID nouvellement inséré
            $id = $db->lastInsertId();

            echo json_encode($id);

            // 201 - Created
            http_response_code(201);                

            // Retour de la ressource
            echo json_encode(
                [
                    'id' => $id,
                    ...$datas // '...' pour associer un tableau avec un autre
                ]
            );
        }    
// Sinon, retourne une erreur...
else
{        
    http_response_code(400);

    echo_json_encode(
        [
            'status' => 400,
            'message' => 'Bad Request'
        ]
    );
    exit;
}