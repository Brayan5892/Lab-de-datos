<?php
class db{

    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPass = 'cnqv9HHIcIlvK0ql';
    private $dbName = 'apih';

    public function conecctionDB(){
        $mysqlConnect= "mysql:host=$this->dbHost;port=3306;dbname=$this->dbName";
        $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}