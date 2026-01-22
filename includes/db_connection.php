<?php
// includes/db_connection.php
require_once 'config.php';

class Database {
    private $conn;
    private static $instance = null;
    private $connection_time = null;
    
    // Private constructor untuk singleton pattern
    public function __construct() {
        $this->connect();
    }
    
    // Singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    // Koneksi ke database
    private function connect() {
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->conn->connect_error) {
            throw new Exception("Connection failed: " . $this->conn->connect_error);
        }
        
        // Set charset
        $this->conn->set_charset("utf8mb4");
        $this->connection_time = time();
    }
    
    // Get connection - FIXED untuk PHP 8.4 (tanpa ping())
    public function getConnection() {
        // Jika koneksi belum ada atau terputus, buat baru
        if (!$this->conn || !$this->isConnectionValid()) {
            $this->reconnect();
        }
        return $this->conn;
    }
    
    // Cek apakah koneksi masih valid (tanpa ping() yang deprecated)
    private function isConnectionValid() {
        try {
            // Cek dengan query sederhana
            if ($this->conn->query("SELECT 1") === false) {
                return false;
            }
            
            // Cek timeout (opsional: jika koneksi lebih dari 1 jam, reconnect)
            $current_time = time();
            if (($current_time - $this->connection_time) > 3600) { // 1 jam
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    // Reconnect jika diperlukan
    private function reconnect() {
        if ($this->conn) {
            @$this->conn->close(); // @ untuk suppress error jika sudah closed
        }
        $this->connect();
    }
    
    // Execute query dengan prepared statement
    public function executeQuery($sql, $params = [], $types = '') {
        $conn = $this->getConnection();
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }
        
        return $stmt;
    }
    
    // Fetch single row
    public function fetchOne($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }
    
    // Fetch all rows
    public function fetchAll($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        $result = $stmt->get_result();
        
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        
        $stmt->close();
        return $rows;
    }
    
    // Insert data dan return last insert ID
    public function insert($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        $insert_id = $this->conn->insert_id;
        $stmt->close();
        return $insert_id;
    }
    
    // Update data
    public function update($sql, $params = [], $types = '') {
        $stmt = $this->executeQuery($sql, $params, $types);
        $affected_rows = $stmt->affected_rows;
        $stmt->close();
        return $affected_rows;
    }
    
    // Delete data
    public function delete($sql, $params = [], $types = '') {
        return $this->update($sql, $params, $types);
    }
    
    // Begin transaction
    public function beginTransaction() {
        $this->getConnection()->begin_transaction();
    }
    
    // Commit transaction
    public function commit() {
        $this->conn->commit();
    }
    
    // Rollback transaction
    public function rollback() {
        $this->conn->rollback();
    }
    
    // Escape string
    public function escape($string) {
        return $this->getConnection()->real_escape_string($string);
    }
    
    // Get last insert ID
    public function getLastInsertId() {
        return $this->conn->insert_id;
    }
    
    // Get affected rows
    public function getAffectedRows() {
        return $this->conn->affected_rows;
    }
    
    // Close connection
    public function close() {
        if ($this->conn) {
            $this->conn->close();
            $this->conn = null;
            self::$instance = null;
        }
    }
    
    // Destructor
    public function __destruct() {
        // Jangan otomatis close di destructor, biarkan PHP handle
        // $this->close();
    }
    
    // ===== STATIC HELPER METHODS =====
    
    // Static method untuk kemudahan
    public static function query($sql, $params = [], $types = '') {
        return self::getInstance()->executeQuery($sql, $params, $types);
    }
    
    public static function getOne($sql, $params = [], $types = '') {
        return self::getInstance()->fetchOne($sql, $params, $types);
    }
    
    public static function getAll($sql, $params = [], $types = '') {
        return self::getInstance()->fetchAll($sql, $params, $types);
    }
    
    public static function insertId($sql, $params = [], $types = '') {
        return self::getInstance()->insert($sql, $params, $types);
    }
    
    public static function execute($sql, $params = [], $types = '') {
        return self::getInstance()->update($sql, $params, $types);
    }
}
?>