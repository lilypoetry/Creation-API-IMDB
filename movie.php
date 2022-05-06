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
 * Si la méthod est différente de "GET"
 */

if ($method !== 'GET')
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

/**
 * Si le paramètre "id" n'existe pas dans l'URL,
 * on return une erreur 400
 */

if (empty($_GET['id']))
{
    http_response_code(400);

    echo_json_encode(
        [
            'status' => 400,
            'message' => 'Bad request'
        ]
    );
}

// Récuperation de la valeur du paramètre "id"
$id = htmlspecialchars(strip_tags($_GET['id']));

// Sélection en BDD
$query = $db->prepare('SELECT * FROM movies WHERE id = :id');
$query->bindValue(':id', $id, PDO::PARAM_INT);
$query->execute();

// Récupère l'enregistrement trouvé
$movie = $query->fetch();


if (!$movie) 
{
    echo json_encode(
        [
            'status' => 404,
            'message' => 'Film not found'
        ]
    );
    exit;
}

// 200 - OK
http_response_code(200);

// Retourne les données au format JSON
echo json_encode($movie);