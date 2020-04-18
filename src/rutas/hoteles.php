<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;

$app = new \Slim\App;

$app->get('/api/hotels', function(Request $request, Response $response){
    $sql = "SELECT * FROM hotels c WHERE c.HotelId=5";
    try{
        
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() > 0){
            $hoteles= $resultado->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($hoteles);
        }else{
            echo json_encode("No existen hoteles en la base de datos");
        }
        
        $resultado = null;
        $db = null;
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }


});