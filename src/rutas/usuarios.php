<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;




$app = new \Slim\App;
   $app->post('/users/new', function(Request $request, Response $response){
    $UserId = ContarU();
    $Email = $request->getParam('Email');
    $Password = $request->getParam('Password');
    $Name = $request->getParam('Name');
    $Lastname = $request->getParam('Lastname');
    $Address = $request->getParam('Address');
    $Telephone = $request->getParam('Telephone');

    

    $sql= "INSERT INTO Users (UserId,Email, Password, Name, Lastname, Address, Telephone) VALUES 
    (:UserId,:Email, :Password, :Name, :Lastname, :Address, :Telephone)";

    try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->prepare($sql);
    
        $resultado->bindParam(':UserId', $UserId);
        $resultado->bindParam(':Email', $Email);
        $resultado->bindParam(':Password', $Password);
        $resultado->bindParam(':Name', $Name);
        $resultado->bindParam(':Lastname', $Lastname);
        $resultado->bindParam(':Address', $Address);
        $resultado->bindParam(':Telephone', $Telephone);
    
        $resultado->execute();
        echo json_encode("Nuevo usuario registrado, Su UserId es: ".$UserId);  
    
        $resultado = null;
        $db = null;
      }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
      }
});
//Función para contar usuarios
function ContarU(){
    $sql = "SELECT * FROM users ";
    try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        $Total=$resultado->rowCount();
        $resultado = null;
        $db = null;
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }

    return ($Total+1);
}

?>