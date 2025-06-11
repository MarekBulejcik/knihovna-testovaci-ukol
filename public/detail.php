<?php

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Database.php';

$db = new Database();

$book_id = $_GET['id'] ?? null;
$book = null;

if ($book_id) {
    $book = $db->getBookById((int)$book_id);
}

$pageTitle = $book ? $book['title'] : 'Kniha nenalezena';

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">

        <?php if ($book): ?>
            <h1><?php echo htmlspecialchars($book['title']); ?></h1>
            <h3><?php echo htmlspecialchars($book['author']); ?> (<?php echo htmlspecialchars($book['publication_year']); ?>)</h3>
            
            <p><strong>Anotace:</strong><br>
                <?php echo nl2br(htmlspecialchars($book['annotation'])); ?>
            </p>

            <p><strong>Hodnocení:</strong> <?php echo htmlspecialchars($book['rating']); ?> / 5</p>

        <?php else: ?>
            <h1>Kniha nenalezena</h1>
            <p>Litujeme, ale kniha s tímto ID v databázi neexistuje.</p>
        <?php endif; ?>

        <br>
        <div class="actions no-print" style="margin-top: 2rem; text-align: right;">
            <a href="index.php">Zpět na katalog</a>
            <button onclick="window.print()" style="margin-left: 1rem;">Tisk</button>
        </div>
    </div>
</body>
</html>