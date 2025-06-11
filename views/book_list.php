<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
        
        <?php if (empty($books)): ?>
            <p>V databázi nejsou žádné knihy.</p>
        <?php else: ?>
            <table class="book-table">
                <thead>
                    <tr>
                        <th>Název</th>
                        <th>Autor</th>
                        <th>Rok vydání</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book): ?>
                        <tr>
                            <td>
                                <a href="detail.php?id=<?php echo $book['id']; ?>">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['publication_year']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>