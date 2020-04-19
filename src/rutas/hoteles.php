<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;




$app = new \Slim\App;
//Busqueda de hotel por cualquier parametro
$app->get('/hotels/{Attribute}/{column}', function(Request $request, Response $response){
   $attribute_hotel = $request->getAttribute('Attribute');
   $column_hotels= $request->getAttribute('column');
   
  
   switch($attribute_hotel){
       case 'Small':
       $sql = "SELECT * FROM hotels  WHERE  $column_hotels<51";
       break;
       case 'Medium':
      $sql = "SELECT * FROM hotels  WHERE  $column_hotels>50 and $column_hotels<101 ";
       break;
      case 'Large':
        $sql = "SELECT * FROM hotels  WHERE  $column_hotels>100";
      break;
      default:
      $sql = "SELECT * FROM hotels  WHERE  $column_hotels='$attribute_hotel'";
   }
    try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() > 0){
            $hoteles = $resultado->fetchAll(PDO::FETCH_OBJ);
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


$app->post('/usuario/nuevo', function(Request $request, Response $response){
    $UserId = Contar();
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
        echo json_encode("Nuevo cliente registrado, Su UserId es: ".$UserId);  
    
        $resultado = null;
        $db = null;
      }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
      }
});

function Contar(){
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

