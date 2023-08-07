<?php

namespace App\Controller;

use App\Model\Recipe;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RecipeController
{
    public function getRecipes(Request $request, Response $response): Response
    {
        try {
            $response->getBody()->write(json_encode(Recipe::all()));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function getRecipeById(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) && !is_numeric($args['id'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            if ($ingredient = Recipe::getById($args['id'])) {
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

    public function deleteRecipe(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) && !is_numeric($args['id'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            Recipe::delete($args['id']);
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function addRecipe(Request $request, Response $response): Response
    {
        $bodyData = $request->getParsedBody();

        if (!isset($bodyData['name']) && !isset($bodyData['steps']) && !isset($bodyData['path_to_photo']) &&
            !isset($bodyData['ingredients']) && !is_array($bodyData['ingredients'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            $id = Recipe::add([
                'name' => $bodyData['name'],
                'steps' => $bodyData['steps'],
                'ingredients' => $bodyData['ingredients'],
                'path_to_photo' => $bodyData['path_to_photo']
            ]);
            $response->getBody()->write(json_encode($id));
            return $response->withStatus(200)->withHeader('Content-type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function updateRecipe(Request $request, Response $response, array $args): Response
    {
        $bodyData = $request->getParsedBody();

        if (!isset($args['id']) && !is_numeric($args['id']) &&
            !isset($bodyData['value']) && !is_array($bodyData['value'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        try {
            if (Recipe::update((int)$args['id'], $bodyData['value'])) {
                return $response->withStatus(200)->withHeader('Content-type', 'application/json');
            } else {
                return $response->withStatus(404);
            }
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function uploadImage(Request $request, Response $response, array $args): Response
    {
        if (!isset($args['id']) && !is_numeric($args['id'])) {
            $response->getBody()->write('Error: incorrect request data');
            return $response->withStatus(400);
        }

        $directory = 'public/upload';

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];

        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            return $response->withStatus(500);
        }

        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        try {
            if (Recipe::update((int)$args['id'], ['path_to_photo' => $directory . DIRECTORY_SEPARATOR . $filename])) {
                return $response->withStatus(200)->withHeader('Content-type', 'application/json');
            } else {
                return $response->withStatus(404);
            }
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }
}