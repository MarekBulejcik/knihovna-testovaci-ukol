<?php

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Database.php';

$db = new Database();
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$errors = [];
$successMessage = '';

if ($action === 'add_book' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['is_logged_in'])) {
    // 1. ZISK DAT Z FORMULÁŘE
    $newBook = [
        'title' => $_POST['title'] ?? '',
        'author' => $_POST['author'] ?? '',
        'publication_year' => $_POST['publication_year'] ?? '',
        'annotation' => $_POST['annotation'] ?? '',
        'rating' => $_POST['rating'] ?? ''
    ];

    // 2. VALIDACE
    if (empty($newBook['title'])) {
        $errors[] = 'Název je povinný.';
    }
    if (empty($newBook['author'])) {
        $errors[] = 'Autor je povinný.';
    }
    if (!empty($newBook['publication_year']) && !filter_var($newBook['publication_year'], FILTER_VALIDATE_INT)) {
        $errors[] = 'Rok vydání musí být platné číslo.';
    }
    if (!empty($newBook['rating']) && !filter_var($newBook['rating'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]])) {
        $errors[] = 'Hodnocení musí být číslo od 1 do 5.';
    }

    // 3. ULOŽENÍ DO DATABÁZE POKUD NEJSOU CHYBY
    if (empty($errors)) {
        $db->addBook($newBook);
        $successMessage = 'Kniha byla úspěšně přidána!';
        // RESET TEXTU V INPUTECH
        $_POST = []; 
    }
}

// ZPRACOVÁNÍ IMPORTU SOUBORU (books.json)
if ($action === 'import' && isset($_SESSION['is_logged_in'])) {
    $json_file = file_get_contents(__DIR__ . '/../books.json');
    $books = json_decode($json_file, true);

    foreach ($books as $book) {
        $db->addBook($book);
    }

    header('Location: admin.php?import_success=1');
    exit;
}

// ZPRACOVÁNÍ ODHLÁŠENÍ
if ($action === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ZPRACOVÁNÍ PŘIHLÁŠENÍ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    if ($_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['is_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $error = 'Nesprávné heslo!';
    }
}

// KONTROLA STATUSU PŘIHLÁŠENÍ UŽIVATELE
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrace</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; }
        .form-group input, .form-group textarea { width: 100%; max-width: 400px; padding: 0.5rem; }
        .error { color: red; border: 1px solid red; padding: 1rem; margin-bottom: 1rem; }
        .success { color: green; border: 1px solid green; padding: 1rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administrace</h1>

        <!-- POKUD JE UŽIVATEL PŘIHLÁŠEN -->
        <?php if ($isLoggedIn): ?>
            <p>Jste přihlášen(a). <a href="admin.php?action=logout">Odhlásit se</a> | <a href="index.php">Zpět na katalog</a></p>
            <hr>

            <!-- ZOBRAZOVÁNÍ OZNÁMENÍ ÚSPĚŠNÉHO IMPORTU KNIH -->
            <?php if (isset($_GET['import_success'])): ?>
                <div class="success">Knihy byly úspěšně naimportovány!</div>
            <?php endif; ?>

            <!-- ZOBRAZOVÁNÍ POTVRZENÍ -->
            <?php if ($successMessage): ?>
                <div class="success"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <!-- ZOBRAZOVÁNÍ CHYB -->
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <strong>Chyba při ukládání:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <h2>Přidat novou knihu</h2>
            <form method="POST" action="admin.php">
                <input type="hidden" name="action" value="add_book">
                <div class="form-group">
                    <label for="title">Název*</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="author">Autor*</label>
                    <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="publication_year">Rok vydání</label>
                    <input type="number" id="publication_year" name="publication_year" value="<?php echo htmlspecialchars($_POST['publication_year'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="annotation">Anotace</label>
                    <textarea id="annotation" name="annotation" rows="4"><?php echo htmlspecialchars($_POST['annotation'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="rating">Hodnocení (1-5)</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" value="<?php echo htmlspecialchars($_POST['rating'] ?? ''); ?>">
                </div>
                <button type="submit">Přidat knihu</button>
            </form>

            <hr>
            <h2>Další akce</h2>
            <a href="admin.php?action=import">Importovat knihy z JSON</a>

        <!-- POKUD JE UŽIVATEL NEPŘIHLÁŠEN -->
        <?php else: ?>
            <form method="POST" action="admin.php">
                <label for="password">Heslo:</label><br>
                <input type="password" id="password" name="password" required>
                <button type="submit">Přihlásit se</button>
            </form>
            <?php if (isset($login_error)): ?>
                <p style="color: red;"><?php echo $login_error; ?></p>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</body>
</html>