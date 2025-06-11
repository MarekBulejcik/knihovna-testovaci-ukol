<?php

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../src/Database.php';

$db = new Database();
$action = $_GET['action'] ?? $_POST['action'] ?? null;
$errors = [];
$successMessage = '';

// NAHRÁVÁNÍ JSON SOUBORŮ S DATY
if ($action === 'import_upload' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['is_logged_in'])) {
    if (isset($_FILES['json_file']) && $_FILES['json_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp_path = $_FILES['json_file']['tmp_name'];
        $file_name = $_FILES['json_file']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_extension === 'json') {
            $json_content = file_get_contents($file_tmp_path);
            $books = json_decode($json_content, true);

            // KONTROLA JSON SOUBORU
            if (is_array($books)) {
                $imported_count = 0;
                foreach ($books as $book) {
                    // PŘIDÁVÁM DATA KNIH DO DATABÁZE (FUNKCE ADDBOOK SI OŠETŘÍ DUPLICITNÍ KNIHY)
                    $db->addBook($book);
                    $imported_count++;
                }
                $successMessage = "Bylo úspěšně naimportováno " . $imported_count . " knih.";
            } else {
                $errors[] = 'Soubor neobsahuje validní JSON formát.';
            }
        } else {
            $errors[] = 'Prosím, nahrajte soubor s koncovkou .json';
        }
    } else {
        // ZPRACOVÁNÍ CHYB
        switch ($_FILES['json_file']['error']) {
            case UPLOAD_ERR_NO_FILE:
                $errors[] = 'Nebyl vybrán žádný soubor.';
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = 'Soubor je příliš velký.';
                break;
            default:
                $errors[] = 'Při nahrávání souboru došlo k chybě.';
        }
    }
}

// ZPRACOVÁNÍ MANUÁLNÍHO PŘIDÁNÍ KNIH
if ($action === 'add_book' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['is_logged_in'])) {
    $newBook = [
        'title' => $_POST['title'] ?? '', 'author' => $_POST['author'] ?? '',
        'publication_year' => $_POST['publication_year'] ?? '', 'annotation' => $_POST['annotation'] ?? '',
        'rating' => $_POST['rating'] ?? ''
    ];
    if (empty($newBook['title'])) { $errors[] = 'Název je povinný.'; }
    if (empty($newBook['author'])) { $errors[] = 'Autor je povinný.'; }
    if (!empty($newBook['publication_year']) && !filter_var($newBook['publication_year'], FILTER_VALIDATE_INT)) { $errors[] = 'Rok vydání musí být platné číslo.'; }
    if (!empty($newBook['rating']) && !filter_var($newBook['rating'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]])) { $errors[] = 'Hodnocení musí být číslo od 1 do 5.'; }

    if (empty($errors)) {
        $db->addBook($newBook);
        $successMessage = 'Kniha byla úspěšně přidána!';
        $_POST = []; 
    }
}

// ZPRACOVÁNÍ ODHLÁŠENÍ
if ($action === 'logout') {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// ZPRACOVÁNÍ PŘIHLÁŠENÍ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !in_array($action, ['add_book', 'import_upload'])) {
    if (isset($_POST['password']) && $_POST['password'] === ADMIN_PASSWORD) {
        $_SESSION['is_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Nesprávné heslo!';
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
            <p>Jste přihlášen(a). <a href="admin.php?action=logout">Odhlásit se</a> | <a href="index.php">Zpět na katalog</a></p>
            <hr>

            <?php if ($successMessage): ?>
                <div class="success"><?php echo $successMessage; ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="error">
                    <strong>Chyba:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <h2>Importovat knihy z JSON souboru</h2>
            <form method="POST" action="admin.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import_upload">
                <div class="form-group">
                    <label for="json_file">Vyberte JSON soubor</label>
                    <input type="file" id="json_file" name="json_file" accept=".json,application/json">
                </div>
                <button type="submit">Importovat</button>
            </form>

            <hr>
            
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