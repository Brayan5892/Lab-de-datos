<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Message;




$app = new \Slim\App;
$app->post('/reservations/new', function(Request $request){
    $ResId = Contar2();
    $HotelId = $request->getParam('HotelId');
    $UserId = $request->getParam('UserId');
    $GuestsNum = $request->getParam('GuestsNum');
    $RoomT = $request->getParam('RoomT');
    $RoomA = $request->getParam('RoomA');
    $IDate = $request->getParam('IDate');
    $FDate = $request->getParam('FDate');

    
    if(ValHotelId($HotelId) & ValUserId($UserId)){
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
            echo json_encode("Nueva reserva registrada, Su ResId es: ".$ResId);  

            $resultado = null;
            $db = null;
          }catch(PDOException $e){
            echo '{"error" : {"text":'.$e->getMessage().'}';
          }
     }
    
});
$app->delete('/reservations/delete/{ResID}/{HotelID}/{Fecha}', function(Request $request){
    $ReservID = $request->getAttribute('ResID');
    $HotelID = $request->getAttribute('HotelID');
    $Fecha = $request->getAttribute('Fecha');
    if(ValDelete($ReservID,$HotelID,$Fecha)){
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
function Contar2(){
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
function ValDelete($ReservID,$HotelID,$Fecha){
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
?>