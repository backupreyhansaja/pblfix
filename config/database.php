<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'lab_kampusfix');
define('DB_USER', 'postgres');
define('DB_PASS', 'raihan100');

class Database {
    private $conn;
    
    public function __construct() {
        try {
            $this->conn = pg_connect("host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS);
            
            if (!$this->conn) {
                throw new Exception("Connection failed: " . pg_last_error());
            }
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function query($sql) {
        return pg_query($this->conn, $sql);
    }
    
    public function fetch($result) {
        return pg_fetch_assoc($result);
    }
    
    public function fetchAll($result) {
        if ($result === false) {
            return [];
        }
        $data = pg_fetch_all($result);
        return $data === false ? [] : $data;
    }
    
    public function escape($string) {
        return pg_escape_string($this->conn, $string);
    }
    
    public function numRows($result) {
        if ($result === false) {
            return 0;
        }
        return pg_num_rows($result);
    }
    
    public function close() {
        pg_close($this->conn);
    }
}
