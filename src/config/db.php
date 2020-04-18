<?php
class db{

    private $dbHost = 'localhost';
    private $dbUser = 'root';
    private $dbPass = 'cYxUOKO9cGzGyZRR';
    private $dbName = 'apih';

    public function conecctionDB(){
        $mysqlConnect= "mysql:host=$this->dbHost;dbname=$this->dbName";
        $dbConnection = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
        $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConnection;
    }
}