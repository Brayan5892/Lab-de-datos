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
//Agregar un nuevo hotel
$app->post('/hotels/{APIkey}/new', function(Request $request){
    $APIkey = $request->getAttribute('APIkey');
    $HotelId = ContarH();
    $Name = $request->getParam('Name');
    $Address = $request->getParam('Address');
    $State = $request->getParam('State');
    $Telephone = $request->getParam('Telephone');
    $Fax = $request->getParam('Fax');
    $Email = $request->getParam('Email');
    $Website = $request->getParam('Website');
    $Type = $request->getParam('Type');
    $Size = $request->getParam('Size');

    
    if(ValAPIkey($APIkey)){
        $sql= "INSERT INTO hotels (HotelId, Name, Address, State, Telephone, Fax, Email, Website, Type, Size) VALUES 
        (:HotelId, :Name, :Address, :State, :Telephone, :Fax, :Email, :Website, :Type, :Size)";

        try{
            $db = new db();
            $db = $db->conecctionDB();
            $resultado = $db->prepare($sql);

            $resultado->bindParam(':HotelId', $HotelId);
            $resultado->bindParam(':Name', $Name);
            $resultado->bindParam(':Address', $Address);
            $resultado->bindParam(':State', $State);
            $resultado->bindParam(':Telephone', $Telephone);
            $resultado->bindParam(':Fax', $Fax);
            $resultado->bindParam(':Email', $Email);
            $resultado->bindParam(':Website', $Website);
            $resultado->bindParam(':Type', $Type);
            $resultado->bindParam(':Size', $Size);

            $resultado->execute();
            echo json_encode("Nuevo hotel registrado, Su HotelId es: ".$HotelId);  

            $resultado = null;
            $db = null;
          }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
          }
     }
    
});
//Eliminar un hotel identificado por HotelId
$app->delete('/hotels/{APIkey}/delete/{HotelID}', function(Request $request){
    $APIkey = $request->getAttribute('APIkey');
    $HotelID = $request->getAttribute('HotelID');
    if(ValDeleteH($HotelID) & ValAPIkey($APIkey)){
        $sql = "DELETE FROM hotels  WHERE  HotelId='$HotelID'";
        try{
            $db = new db();
            $db = $db->conecctionDB();
            $resultado = $db->query($sql);

            $Total=$resultado->rowCount();
            echo json_encode("Hotel eliminada correctamente");  
            $resultado = null;
            $db = null;
        }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
        }
    }
});
//Función para validar ApiKey
function ValAPIkey($APIkey){
    $sql = "SELECT * FROM apikeys WHERE APIKey='$APIkey'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
            echo json_encode("API key no registrada");
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}
//Función para contar hoteles
function ContarH(){
    $sql = "SELECT * FROM hotels ";
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
//Función pra validar si se puede eliminar o no un hotel identificado porhotelId
function ValDeleteH($HotID){
    $sql = "SELECT * FROM hotels WHERE HotelId='$HotID'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
            echo json_encode("No se encontro un hotel con los parametros suministrados");
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}

?>