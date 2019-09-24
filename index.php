<?php

use Dotenv\Dotenv;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
require 'src/DatabaseManager.php';

require __DIR__ . '/vendor/autoload.php';

/**
 * Instantiate App
 *
 * In order for the factory to work you need to ensure you have installed
 * a supported PSR-7 implementation of your choice e.g.: Slim PSR-7 and a supported
 * ServerRequest creator (included with Slim PSR-7)
 */
$app = AppFactory::create();

// Add Routing Middleware
$app->addRoutingMiddleware();

$dotenv = Dotenv::create(__DIR__);
$dotenv->load();
$dotenv->required(['ENV', 'DB_CONNECTION_STRING'])->notEmpty();

// In order to use the Mongodb/Client everywhere, it's been made global
$dbclient = new DatabaseManager();
global $db;
$GLOBALS['db']= $dbclient->databaseConnect();

/**
 * Add Error Handling Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * which can be replaced by a callable of your choice.

 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
if (getenv('ENV') == 'dev') {
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
}
elseif (getenv('ENV') == 'prod') {
    $errorMiddleware = $app->addErrorMiddleware(false, false, false);
}

$app->get('/characters', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    if (array_key_exists('campaign-id', $params) ) {
        $characters = $GLOBALS['db']->dnd->characters->find([
            'campaignId' => $params['campaign-id']
        ]);
        foreach ($characters as $character) {
            $response->getBody()->write(json_encode($character, JSON_PRETTY_PRINT));
        }
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    else {
        $response->getBody()->write('Missing campaign-id parameter');
        return $response->withStatus(400);
    }
}
);

$app->post('/characters', function (Request $request, Response $response) {
    $request_type = $request->getHeader('CONTENT_TYPE');
    if($request_type[0] != 'application/json'){
        $response->getBody()->write('Only application/json accepted');
        return $response->withStatus(400, 'Invalid Json');
    }
    $characters = $request->getBody();
    $GLOBALS['db']->dnd->characters->insertOne(json_decode($characters, true));
    return $response->withStatus(200, 'Character Created');
});

$app->run();