<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;


$app = new \Slim\App;
include_once '../src/rutas/hoteles.php';
//Agregar una nueva reserva
$app->post('/reservations/new', function(Request $request){
    $ResId = ContarR();
    $HotelId = $request->getParam('HotelId');
    $UserId = $request->getParam('UserId');
    $GuestsNum = $request->getParam('GuestsNum');
    $RoomT = $request->getParam('RoomT');
    $RoomA = $request->getParam('RoomA');
    $IDate = $request->getParam('IDate');
    $FDate = $request->getParam('FDate');
    
    
    if(ValHotelId($HotelId) & ValUserId($UserId)){
        $HotelSize=GetSize($HotelId);
        $Vec=dishotel($HotelId,$IDate,$FDate,$HotelSize);
        switch($RoomT){
            case 'Single':
                $num=$Vec[0];
            break;
            case 'Double':
                $num=$Vec[1];
            break;
            case 'Suit':
                $num=$Vec[2];
            break;
        }
        if($num>=$RoomA){
          $Price=GetPrice($HotelSize, $RoomT, $RoomA);  
        $sql= "INSERT INTO reservations (ResId, HotelId, UserId, GuestsNumber, RoomType, RoomAmount, InitialDate, FinalDate) VALUES 
    (:ResId, :HotelId, :UserId, :GuestsNum, :RoomT, :RoomA, :IDate, :FDate)";

        try{
            $db = new db();
            $db = $db->conecctionDB();
            $resultado = $db->prepare($sql);

            $resultado->bindParam(':ResId', $ResId);
            $resultado->bindParam(':HotelId', $HotelId);
            $resultado->bindParam(':UserId', $UserId);
            $resultado->bindParam(':GuestsNum', $GuestsNum);
            $resultado->bindParam(':RoomT', $RoomT);
            $resultado->bindParam(':RoomA', $RoomA);
            $resultado->bindParam(':IDate', $IDate);
            $resultado->bindParam(':FDate', $FDate);

            $resultado->execute();
            echo json_encode("Nueva reserva registrada, Su ResId es: ".$ResId.", tendra un costo de $".$Price." por noche");  

            $resultado = null;
            $db = null;
          }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
          }
        }else{
            echo json_encode("El hotel ".$HotelId." no tiene disponible la cantidad de habitaciones solicitadas en la fecha solicitada.");
        }
    }
});

//Eliminar una reserva identificada por ResID, HotelID y Fecha
$app->delete('/reservations/delete/{ResID}/{HotelID}/{Fecha}', function(Request $request){
    $ReservID = $request->getAttribute('ResID');
    $HotelID = $request->getAttribute('HotelID');
    $Fecha = $request->getAttribute('Fecha');
    if(ValDeleteR($ReservID,$HotelID,$Fecha)){
        $sql = "DELETE FROM reservations  WHERE  ResID='$ReservID' AND HotelId='$HotelID' AND ('$Fecha' BETWEEN InitialDate AND FinalDate)";
        try{
            $db = new db();
            $db = $db->conecctionDB();
            $resultado = $db->query($sql);

            $Total=$resultado->rowCount();
            echo json_encode("Reserva eliminada correctamente");  
            $resultado = null;
            $db = null;
        }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
        }
    }
});
//Función para obtener el tamaño de un hotel
function GetSize($HotelID){
    $sql = "SELECT Size FROM hotels WHERE HotelId='$HotelID'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);
        if($resultado->rowCount() > 0){
            $hoteles = $resultado->fetchAll(PDO::FETCH_ASSOC);
            foreach ($hoteles as $row) {
                return $row['Size'];
            }
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}
//Función para calcular precio de la reserva
function GetPrice($HSize, $RoomT, $RoomA){
    $price =0;
    if($HSize<=50){
        switch($RoomT){
           case 'Single':
                $price=50;
                break;
            case 'Double':
                $price=70;
                break;
            case 'Suit':
                $price=130;
                break;
            }
    }else{
        if($HSize<=100 & $HSize>=51){
            switch($RoomT){
                case 'Single':
                    $price=70;
                    break;
                case 'Double':
                    $price=100;
                    break;
                case 'Suit':
                    $price=200;
                    break;
                }
        }else{
            switch($RoomT){
                case 'Single':
                    $price=90;
                    break;
                case 'Double':
                    $price=120;
                    break;
                case 'Suit':
                    $price=500;
                    break;
            }
        } 
    }
    return $price*$RoomA;
}
//Función para contar reservas
function ContarR(){
    $sql = "SELECT * FROM reservations ";
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
//Función para validar si existe una reserva con un UserId especifico
function ValUserId($UsId){
    $sql = "SELECT * FROM users WHERE UserId='$UsId'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
            echo json_encode("No se encontro un usuario con el UserId ".$UsId);
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}
//Función para validar si existe una reserva con un HotelId especifico
function ValHotelId($HotId){
    $sql = "SELECT * FROM hotels WHERE HotelId='$HotId'";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
            echo json_encode("No se encontro un hotel con el HotelId ".$HotId);
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}
//Funcion para validar si se puede eliminar o no una reserva con los parametros ResId, HotelId y Fecha
function ValDeleteR($ReservID,$HotelID,$Fecha){
    $sql = "SELECT * FROM reservations WHERE ResID='$ReservID' AND HotelId='$HotelID' AND ('$Fecha' BETWEEN InitialDate AND FinalDate)";
     try{
        $db = new db();
        $db = $db->conecctionDB();
        $resultado = $db->query($sql);

        if($resultado->rowCount() <= 0){
            $resultado = null;
            $db = null;
            echo json_encode("No se encontro una reserva con los parametros suministrados");
            return false;
        }else{
            return true;
        }
    }catch(PDOException $e){
        echo '{"error" : {"text":'.$e->getMessage().'}';
    }
}

