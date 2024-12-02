<?php
class database
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "4a-pro";

    // Initialize database connection
    public function initDatabase(){
        try {
            $con = new PDO("mysql:host=$this->host;dbname=".$this->dbname, 
            $this->user, 
            $this->pass);
            // Set the PDO error mode to exception
            $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $con;
        } catch (PDOException $th) {
            echo $th;
            return null;
        }
    }

    // Execute a query
    public function execute($query, $params = []){
        $con = $this->initDatabase();
        if ($con) {
            $stmt = $con->prepare($query);
            return $stmt->execute($params);
        }
        return false;
    }
}
