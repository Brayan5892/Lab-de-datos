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
            echo "\n\n";
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

//Funcion busqueda hoteles por ubicacion
$app->get('/hotels/ubicacion/{Latitude}/{Longitude}/{Range}', function(Request $request){
    $latitude = $request->getAttribute('Latitude');
    $longitude= $request->getAttribute('Longitude');
    $rango=$request->getAttribute('Range');
    $sql = "SELECT * FROM hotels ";
 try{
     $db = new db();
     $db = $db->conecctionDB();
     $resultado = $db->query($sql);

     if($resultado->rowCount() > 0){
         $hoteles = $resultado->fetchAll(PDO::FETCH_ASSOC);
      
      foreach ($hoteles as $row) {
         $coor=coordenadas($row['Address']);
         if(($coor[0]>($latitude-$rango)) and ($coor[0]<($latitude+$rango)) and ($coor[1]>($longitude-$rango)) and ($coor[1]<($longitude+$rango))){
            echo "Hotel Id: ".$row['HotelId'];
            echo "\n";
            echo "Nombre: ".$row['Name']; 
            echo "\n\n";
            echo "Latitud: ".$coor[0]."\n";
            echo "Longitud: ".$coor[1]."\n";
         }
         
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
                dishotel($row['HotelId'],$fecha1,$fecha2,$row['Size']);
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

//Funcion verifical disponibilidad de 1 hotel
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
                if($row['RoomType']=='Suite'){
                    $small=$suit-$row['RoomAmount'];
                }
            }
        }
        echo("\nEl Hotel ".$HotelId." tiene:\n");
        echo("Single: ".$small."\n");
        echo("Double: ".$medium."\n");
        echo("Suit: ".$suit."\n");
        }else{
            $small=ceil($Nrooms*0.3);
            $medium=ceil($Nrooms*0.6);
            $suit=ceil($Nrooms*0.1);
            $tot=($small+$medium+$suit);
            $small=($small-($tot-$Nrooms));
            echo("El Hotel ".$HotelId." tiene:\n");
            echo("Single: ".$small."\n");
            echo("Double: ".$medium."\n");
            echo("Suit: ".$suit."\n");
        }
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

function coordenadas($address2){
    $address2="31-ST JANUARY ROAD, FONTAINHAS, PANAJI, Panaji , GOA";
    $address = urlencode($address2);
    $url = "https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyDfy86E7C2pwFeM6yxmYmvEkNzj9wqppQ4&address=" . $address;
    $response = file_get_contents($url);
    $json = json_decode($response,true);
 
    $coor[0] = $json['results'][0]['geometry']['location']['lat'];
    $coor[1] = $json['results'][0]['geometry']['location']['lng'];

    return $coor;
}
