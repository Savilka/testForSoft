<?php

namespace App\Controller;

use App\Model\Ingredient;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IngredientController
{
    public function getIngredients(Request $request, Response $response): Response
    {
        try {
            $response->getBody()->write(json_encode(Ingredient::all()));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function getIngredientById(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) && !is_numeric($args['id'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            if ($ingredient = Ingredient::getById($args['id'])) {
                $response->getBody()->write(json_encode($ingredient));
                return $response->withStatus(200)->withHeader('Content-type', 'application/json');
            } else {
                return $response->withStatus(404);
            }
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function deleteIngredient(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) && !is_numeric($args['id'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            Ingredient::delete($args['id']);
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function addIngredient(Request $request, Response $response): Response
    {
        $bodyData = $request->getParsedBody();

        if (!isset($bodyData['name']) && !isset($bodyData['unit'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            $id = Ingredient::add(['name' => $bodyData['name'], 'unit' => $bodyData['unit']]);
            $response->getBody()->write(json_encode($id));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function updateIngredient(Request $request, Response $response, array $args): Response
    {
        $bodyData = $request->getParsedBody();

        if (!isset($args['id']) && !is_numeric($args['id']) &&
            !isset($bodyData['value']) && !is_array($bodyData['value'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            Ingredient::update((int)$args['id'], $bodyData['value']);
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }
}