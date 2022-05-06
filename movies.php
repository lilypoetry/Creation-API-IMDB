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

    require_once 'connexion.php';

$query = $db->query('SELECT * FROM movies');
$movies = $query->fetchAll();

// 200 - OK
http_response_code(200);

// Retourne les données au format JSON
echo json_encode($movies);

