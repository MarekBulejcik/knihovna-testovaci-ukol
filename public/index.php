<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Database.php';

try {
    $db = new Database();
    $db->createTable();

    $books = $db->getAllBooks();

    $pageTitle = 'Katalog knih';
    require_once __DIR__ . '/../views/book_list.php';
    
} catch (Exception $e) {
    die("Došlo k chybě: " . $e->getMessage());
}