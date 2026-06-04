<?php
class Database {
    private static $instance = null;
    private $conn;
    
    private $host = "localhost";
    private $db_name = "photoshare_db"; // ბაზას დაარქვი ეს სახელი phpMyAdmin-ში
    private $username = "root";
    private $password = "root"; // MAMP default password

    // Private კონსტრუქტორი, რომ გარედან ახალი ობიექტი არ შეიქმნას
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            die("კავშირის შეცდომა: " . $exception->getMessage());
        }
    }

    // სტატიკური მეთოდი კავშირის მისაღებად
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance->conn;
    }
}