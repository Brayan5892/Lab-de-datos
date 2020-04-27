<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;

<<<<<<< HEAD
$app = new \Slim\App;

require '../src/rutas/apikey.php';
//Funcion agregar usuario
   $app->post('/users/new', function(Request $request){
=======



$app = new \Slim\App;
   $app->post('/users/new', function(Request $request, Response $response){
>>>>>>> e43e4c561cb32308b2aaa7eddff4fc049418627a
    $UserId = ContarU();
    $Email = $request->getParam('Email');
    $Password = $request->getParam('Password');
    $Name = $request->getParam('Name');
    $Lastname = $request->getParam('Lastname');
    $Address = $request->getParam('Address');
    $Telephone = $request->getParam('Telephone');

<<<<<<< HEAD
=======
    

>>>>>>> e43e4c561cb32308b2aaa7eddff4fc049418627a
    $sql= "INSERT INTO Users (UserId,Email, Password, Name, Lastname, Address, Telephone) VALUES 
    (:UserId,:Email, :Password, :Name, :Lastname, :Address, :Telephone)";

    try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->prepare($sql);
<<<<<<< HEAD
=======
    
>>>>>>> e43e4c561cb32308b2aaa7eddff4fc049418627a
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
<<<<<<< HEAD

//Funcion modificar usuario
$app->put('/api/usuario/modificar/{id}/{password}/{address}', function(Request $request){
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

=======
>>>>>>> e43e4c561cb32308b2aaa7eddff4fc049418627a
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

<<<<<<< HEAD
//Funcion para validar si existe un usuario con los parametros inducidos
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
=======
>>>>>>> e43e4c561cb32308b2aaa7eddff4fc049418627a
?>