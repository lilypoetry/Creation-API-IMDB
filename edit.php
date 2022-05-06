<?php
/**
 * Mise à jour un film
 * Méthode : PUT
 */

// Retour d'en-tête
// header('Access-Control-Allow-Origin: http://api.test');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

// Récupération de la méthode
$method = $_SERVER['REQUEST_METHOD'];

/**
 * Si la méthod est différente de "PUT"
 */

if ($method !== 'PUT')
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

/**
 * Si le paramètre "id" n'existe pas dans l'URL,
 * on retourne une erreur 400
 */
if (empty($_GET['id'])) 
    {
        http_response_code(400);

        echo json_encode([
            'status' => 400,
            'message' => 'Bad Request'
        ]);

        exit;
    }

// Récupération de la valeur du paramètre "id"
$id = htmlspecialchars(strip_tags($_GET['id']));

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

            // Met à jour en BDD
            $query = $db->prepare('UPDATE movies SET title = :title, description = :description, date = :date, time = :time, director = :director, image = :image, trailer = :trailer WHERE id = :id');

            $query->bindValue(':title', $datas['title']);
            $query->bindValue(':description', $datas['description']);
            $query->bindValue(':date', $datas['date']);
            $query->bindValue(':time', $datas['time'], PDO::PARAM_INT);
            $query->bindValue(':director', $datas['director']);
            $query->bindValue(':image', $datas['image']);
            $query->bindValue(':trailer', $datas['trailer']);
            $query->bindValue(':id', $id, PDO::PARAM_INT);
            $query->execute();

            /**
             * Si aucune ligne de la table SQL n'est affectée par la
             * requête ci-dessous, on retourne une erreur
             */
            if ($query->rowCount() === 0)
                {
                    http_response_code(404);

                    echo json_encode([
                        'status' => 404,
                        'message' => 'Not Found'
                    ]);
                
                    exit;
                }

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