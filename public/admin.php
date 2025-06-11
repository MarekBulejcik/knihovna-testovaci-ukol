<?php

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Database.php';

$db = new Database();

$action = $_GET['action'] ?? null;

if ($action === 'import' && isset($_SESSION['is_logged_in'])) {
    $json_file = file_get_contents(__DIR__ . '/../books.json');
    $books = json_decode($json_file, true);

    foreach ($books as $book) {
        $db->addBook($book);
    }

    header('Location: admin.php?import_success=1');
    exit;
}

if ($action === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['is_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Nesprávné heslo!';
    }
}

$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Administrace</h1>

        <?php if ($isLoggedIn): ?>
            <p>Jste přihlášen(a).</p>
            
            <?php if (isset($_GET['import_success'])): ?>
                <p style="color: green;">Knihy byly úspěšně naimportovány!</p>
            <?php endif; ?>

            <a href="admin.php?action=import">Importovat knihy z JSON</a><br><br>
            <a href="admin.php?action=logout">Odhlásit se</a><br><br>
            <a href="index.php">Zpět na katalog</a>

        <?php else: ?>
            <form method="POST" action="admin.php">
                <label for="password">Heslo:</label><br>
                <input type="password" id="password" name="password" required>
                <button type="submit">Přihlásit se</button>
            </form>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</body>
</html>