<?php

use App\Controller\RecipeController;
use App\Controller\IngredientController;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->group('/api', function (RouteCollectorProxy $group) {
    $group->group('/recipe', function (RouteCollectorProxy $group) {
        $group->get('/all', [RecipeController::class, 'getRecipes']);
        $group->get('/{id}', [RecipeController::class, 'getRecipeById']);
        $group->post('/addRecipe', [RecipeController::class, 'addRecipe']);
        $group->delete('/{id}', [RecipeController::class, 'deleteRecipe']);
        $group->patch('/{id}', [RecipeController::class, 'updateRecipe']);
        $group->post('/{id}/uploadImage', [RecipeController::class, 'uploadImage']);
    });

    $group->group('/ingredient', function (RouteCollectorProxy $group) {
        $group->get('/all', [IngredientController::class, 'getIngredients']);
        $group->get('/{id}', [IngredientController::class, 'getIngredientById']);
        $group->post('/addIngredient', [IngredientController::class, 'addIngredient']);
        $group->delete('/{id}', [IngredientController::class, 'deleteIngredient']);
        $group->patch('/{id}', [IngredientController::class, 'updateIngredient']);
    });
});

$app->addErrorMiddleware(true, false, false);

$app->run();