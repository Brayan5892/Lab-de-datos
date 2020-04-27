<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


require '../vendor/autoload.php';
require '../src/config/db.php';


$app = new \Slim\App;



require '../src/rutas/usuarios.php';
require '../src/rutas/apikey.php';
require '../src/rutas/reservas.php';
require '../src/rutas/hoteles.php';
$app->run();

?>