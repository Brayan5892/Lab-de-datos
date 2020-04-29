<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;


$app = new \Slim\App;

require '../src/rutas/usuarios.php';
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
            $hoteles = $resultado->fetchAll(PDO::FETCH_ASSOC);
            foreach ($hoteles as $row) {
                echo "Hotel Id: ".$row['HotelId'];
                echo "\n";
                echo "Nombre: ".$row['Name']; 
                echo "\n";
                echo "Address: ".$row['Address'];
                echo "\n";
                echo "State: ".$row['State']; 
                echo "\n";
                echo "Telephone: ".$row['Telephone'];
                echo "\n";
                echo "Fax: ".$row['Fax']; 
                echo "\n";
                echo "Email: ".$row['Email'];
                echo "\n";
                echo "Website: ".$row['Website']; 
                echo "\n";
                echo "Type: ".$row['Type'];
                echo "\n";
                echo "Size: ".$row['Size']; 
                echo "\n";
             }
        }else{
            echo json_encode("No se han encontrado hoteles en la base de datos");
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
    if($Telephone==null){
        $Telephone='NA';
    }
    if($Fax==null){
        $Fax='NA';
    }
    if($Email==null){
        $Email='NA';
    }
    if($Website==null){
        $Website='NA';
    }
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
//Modificar hotel identificado por HotelId
$app->put('/hotels/{APIkey}/modify/{HotelId}', function(Request $request){
    $HotID = $request->getAttribute('HotelId');
    $APIkey = $request->getAttribute('APIkey');

    $Telephone = $request->getParam('Telephone');
    $Email = $request->getParam('Email');
    $Website = $request->getParam('Website');
    $Type = $request->getParam('Type');
    $Size = $request->getParam('Size');

 if(ValAPIkey($APIkey) & ValHotel($HotID)){
   $sql = "UPDATE hotels SET
           Telephone = :Telephone,
           Email = :Email,
           Website = :Website,
           Type = :Type,
           Size = :Size
         WHERE HotelId='$HotID'";   
   try{
     $db = new db();
     $db = $db->conecctionDB();
     $resultado = $db->prepare($sql);
    
     $resultado->bindParam(':Telephone', $Telephone);
     $resultado->bindParam(':Email', $Email);
     $resultado->bindParam(':Website', $Website);
     $resultado->bindParam(':Type', $Type);
     $resultado->bindParam(':Size', $Size);
 
     $resultado->execute();
    
     echo json_encode("Hotel modificado exitosamente.");  
     
   
     $resultado = null;
     $db = null;
   }catch(PDOException $e){
     echo ("error :C");
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
            echo json_encode("Hotel eliminado correctamente");  
            $resultado = null;
            $db = null;
        }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
        }
    }
});

//Busqueda hoteles por ubicacion
$app->get('/hotels/location/{Latitude}/{Longitude}/{Range}', function(Request $request){
    $latitude = $request->getAttribute('Latitude');
    $longitude= $request->getAttribute('Longitude');
    $rango=$request->getAttribute('Range');
    $Nombres[0]=0;
    $resultado=coordenadas($latitude,$longitude,$rango);
        foreach ($resultado['results'] as $row) {
           $HName=$row['name'];
           if(ValHotelName($HName)){
               $Nombres[]=$HName;
           }
        }
        if(sizeof($Nombres) == 1){
            echo json_encode("No se encontraron hoteles en el rango especificado");
        }else{
            for ($i = 1; $i < sizeof($Nombres); $i++) {
                echo ($Nombres[$i]);
                echo("\n");
            }
        }

//Disponibilidad
$app->get('/hotels/{FechaI}/{FechaF}/{State}', function(Request $request){
    $FechaI = $request->getAttribute('FechaI');
    $FechaF= $request->getAttribute('FechaF');
    $State= $request->getAttribute('State');
    $sql = "SELECT * FROM hotels  WHERE  State='$State'";
     try{
         $db = new db();
         $db = $db->conecctionDB();
         $resultado = $db->query($sql);
         if($resultado->rowCount() > 0){
             $hoteles = $resultado->fetchAll(PDO::FETCH_ASSOC);
             $fecha1=new DateTime($FechaI);
             $fecha2=new DateTime($FechaF);
             foreach ($hoteles as $row) { 
                $Vect=dishotel($row['HotelId'],$fecha1,$fecha2,$row['Size']);
                echo("\nEl Hotel ".$row['HotelId']." tiene:\n");
                echo("Single: ".$Vect[0]."\n");
                echo("Double: ".$Vect[1]."\n");
                echo("Suit: ".$Vect[2]."\n");
             }
         }else{
             echo json_encode("No existen hoteles en la base de datos");
         }
         $resultado = null;
         $db = null;
     }catch(PDOException $e){
         echo '{"error" : {"text":'.$e->getMessage().'}';
     }
 });

//Funcion verificar disponibilidad de 1 hotel
function dishotel($HotelId, $FechaI,$FechaF,$Nrooms){
    $sql = "SELECT * FROM reservations WHERE HotelId='$HotelId'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql); 
        if($resultado->rowCount() > 0){
            $Reservas = $resultado->fetchAll(PDO::FETCH_ASSOC);
         $small=ceil($Nrooms*0.3);
         $medium=ceil($Nrooms*0.6);
         $suit=ceil($Nrooms*0.1);
         $tot=($small+$medium+$suit);
         $small=($small-($tot-$Nrooms));
         foreach ($Reservas as $row) {
            $RFechaI=new DateTime($row['InitialDate']);
            $RFechaF=new DateTime($row['FinalDate']);
            if(($FechaI>$RFechaI and $FechaI<$RFechaF) or ($FechaF<$RFechaF and $FechaF>$RFechaI)){
                if($row['RoomType']=='Single'){
                    $small=$small-$row['RoomAmount'];
                }
                if($row['RoomType']=='Double'){
                    $medium=$medium-$row['RoomAmount'];
                }
                if($row['RoomType']=='Suit'){
                    $small=$suit-$row['RoomAmount'];
                }
            }
        }

        }else{
            $small=ceil($Nrooms*0.3);
            $medium=ceil($Nrooms*0.6);
            $suit=ceil($Nrooms*0.1);
            $tot=($small+$medium+$suit);
            $small=($small-($tot-$Nrooms));
        }
         $Vec[0]=$small;
         $Vec[1]=$medium;
         $Vec[2]=$suit;
         return $Vec;
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}

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
function ContarR(){
    $sql = "SELECT * FROM hotels ORDER BY HotelId desc limit 1";
    try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        $Total=$resultado->fetchAll(PDO::FETCH_ASSOC);
        
        $Total2=$Total[0]['HotelId'];
        $resultado = null;
        $db = null;
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
    return ($Total2+1);
}
//Función pra validar si se puede eliminar o no un hotel identificado por hotelId
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
//Función para hallar hoteles en un rango utilizando la API Google Places
function coordenadas($latitud,$longitud,$rango){
    $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=".$latitud.",".$longitud."&radius=".$rango."&type=Lodging&key=AIzaSyDfy86E7C2pwFeM6yxmYmvEkNzj9wqppQ4";
    $response = file_get_contents($url);
    $json = json_decode($response,true);
    return $json;
}
//Función para validar si existe un hotel con un HotelId especifico
function ValHotel($HotelId){
    $sql = "SELECT * FROM hotels WHERE HotelId='$HotelId'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
            echo json_encode("No se encontro un hotel con el HotelId ".$HotelId);
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}
//Función para validar si existe un hotel con un Name especifico
function ValHotelName($HotelN){
    $sql = "SELECT * FROM hotels WHERE Name like '%".$HotelN."%'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
           // echo json_encode("No se encontro un hotel relacionado con el nombre ".$HotelN);
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}