<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use database\Car;

$app = new \Slim\App([
    'settings' => ['displayErrorDetails' => true],
    'view' => new \Slim\Views\Twig("../templates/")
]);
$app->config['debug'] = true;

$app->get('/cars', function (Request $request, Response $response) {
    $cars = Car::all();
    $response = $this->view->render($response, "cars/index.html", compact("cars") );
    return $response;
});

$app->get('/cars/create', function (Request $request, Response $response) {
    $response = $this->view->render($response, "cars/create.html");
    return $response;
});

$app->post('/cars/create', function (Request $request, Response $response) {
    $car = new Car;
    $car->brand = $request->getParsedBody()["brand"];
    $car->license_plate = $request->getParsedBody()["license-plate"];
    $car->save();
    return $response->withRedirect('/cars');
});

$app->get('/cars/{id}/edit', function (Request $request, Response $response, $args) {
    $car = Car::get($args["id"]);
    $response = $this->view->render($response, "cars/edit.html", compact("car") );
    return $response;
});

$app->post('/cars/{id}/edit', function (Request $request, Response $response) {
    $car = new Car;
    $car->brand = $request->getParsedBody()["brand"];
    $car->license_plate = $request->getParsedBody()["license-plate"];
    $car->save();
    return $response->withRedirect('/cars');
});

$app->get('/cars/{id}/delete', function (Request $request, Response $response, $args) {
    $car = Car::destroy($args["id"]);
    return $response->withRedirect('/cars');
});

return $app;
