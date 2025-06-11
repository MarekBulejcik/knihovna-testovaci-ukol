<?php

class Database {
    private ?PDO $pdo = null;

    public function __construct() {
        if ($this->pdo === null) {
            try {
                $this->pdo = new PDO('sqlite:' . DB_PATH);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                die("Chyba připojení k databázi: " . $e->getMessage());
        }
    }
}

    public function createTable(): void {
        $sql = "
        CREATE TABLE IF NOT EXISTS books (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            author VARCHAR(255) NOT NULL,
            publication_year INT,
            annotation TEXT,
            rating INT
        )";
        $this->pdo->exec($sql);
    }

    public function getAllBooks(): array {
        $stmt = $this->pdo->query("SELECT id, title, author, publication_year FROM books ORDER BY author, title");
        return $stmt->fetchAll();
    }
}