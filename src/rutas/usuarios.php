<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;

$app = new \Slim\App;

require '../src/rutas/apikey.php';
//Agregar un nuevo usuario
   $app->post('/users/new', function(Request $request){
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

//Modificar usuario identificado por UserId, Password y Address 
$app->put('/users/modify/{id}/{password}/{address}', function(Request $request){
    $id_clienteV = $request->getAttribute('id');
    $passwordV = $request->getAttribute('password');
    $addressV = $request->getAttribute('address');

    $Email = $request->getParam('Email');
    $Password = $request->getParam('Password');
    $Name = $request->getParam('Name');
    $Lastname = $request->getParam('Lastname');
    $Address = $request->getParam('Address');
    $Telephone = $request->getParam('Telephone');

 if(validar($id_clienteV,$passwordV,$addressV )){
   $sql = "UPDATE users SET
           Email = :Email,
           Password = :Password,
           Name = :Name,
           Lastname = :Lastname,
           Address = :Address,
           Telephone = :Telephone
         WHERE UserId='$id_clienteV' and Password='$passwordV' and Address='$addressV'";
      
   try{
     $db = new db();
     $db = $db->conecctionDB();
     $resultado = $db->prepare($sql);
    
     $resultado->bindParam(':Email', $Email);
     $resultado->bindParam(':Password', $Password);
     $resultado->bindParam(':Name', $Name);
     $resultado->bindParam(':Lastname', $Lastname);
     $resultado->bindParam(':Address', $Address);
     $resultado->bindParam(':Telephone', $Telephone);
 
     $resultado->execute();
    
     echo json_encode("Usuario modificado.");  
     
   
     $resultado = null;
     $db = null;
   }catch(PDOException $e){
     echo ("error :C");
     echo '{"error" : {"text":'.$e->getMessage().'}';
   }
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

//Funcion para validar si existe un usuario con los parametros especificados
function validar($UserId, $Password, $Address){
    $sql = "SELECT * FROM users WHERE UserId='$UserId' and Password='$Password' and Address='$Address'";
    try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);
        
        $Total=$resultado->rowCount();
        
        if($Total>0){
            return true;
        }else{
            echo json_encode("No se encontro el usuario");
            return false;
        }
        $resultado = null;
        $db = null;
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }

}

