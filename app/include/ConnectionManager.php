<?php

class ConnectionManager {
   
    public function getConnection() {
        
        $host = "localhost";
        $username = "root";
        $password = "root";
        // $password = "gIlqPAoSB41B";
        $dbname = "bossbaby";
        $port = 3306;

        $url  = "mysql:host={$host};dbname={$dbname};port={$port}";
        
        $conn = new PDO($url, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        $conn->exec("SET time_zone = '+8:00'");
        return $conn;  
        
    }
    
}