<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;
$app = new \Slim\App;
$app->post('/APIkey/new', function(Request $request){
    $APIkey = rand(1000000,9999999);
    $ContactN = $request->getParam('ContactN');
    $Company = $request->getParam('Company');
    $Email = $request->getParam('Email');

    
        $sql= "INSERT INTO apikeys (APIKey, ContactName, Company, Email) VALUES 
    (:apikey, :ContactN, :Company, :Email)";

        try{
            $db = new db();
            $db = $db->conecctionDB();
            $resultado = $db->prepare($sql);

            $resultado->bindParam(':apikey', $APIkey);
            $resultado->bindParam(':ContactN', $ContactN);
            $resultado->bindParam(':Company', $Company);
            $resultado->bindParam(':Email', $Email);

            $resultado->execute();
            echo json_encode("Nueva APIkey creada, Su APIkey es: ".$APIkey);  

            $resultado = null;
            $db = null;
          }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
          }
    
});

?>