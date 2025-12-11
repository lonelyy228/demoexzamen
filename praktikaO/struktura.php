<?php
require_once "db/db.php";

$navLinks = [];
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['user_type_id'] == 2) {
        $navLinks = [['href' => 'admin.php', 'text' => 'Админ']];
    } else {
        $navLinks = [
            ['href' => 'reports_list.php', 'text' => 'Отчеты'],
            ['href' => 'create_zayavka.php', 'text' => 'Новый отчет']
        ];
    }
    $navLinks[] = ['href' => 'logout.php', 'text' => 'Выход'];
} else {
    $navLinks = [
        ['href' => 'index.php', 'text' => 'Вход'],
        ['href' => 'registration.php', 'text' => 'Регистрация']
    ];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Практика Онлайн <?= $pageTitle ?></title>
    <link rel="icon" href="images/logog3.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-white shadow-sm py-2">
        <div class="container text-center">
            <div class="d-inline-flex align-items-center">
                <img src="images/logog3.png" alt="logo" class="logo-img me-2">
                <h1 class="h4 mb-0">Практика Онлайн</h1>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-dark bg-primary py-2">
        <div class="container justify-content-center">
            <?php foreach ($navLinks as $link): ?>
                <a href="<?= htmlspecialchars($link['href']) ?>" class="nav-link px-3 mx-1"><?= htmlspecialchars($link['text']) ?></a>
            <?php endforeach; ?>
        </div>
    </nav>

    <main class="flex-grow-1 container py-3">
        <h1 class="text-center h2 mb-4"><?= $pageTitle ?></h1>
        <?= $pageContent ?? '' ?>
    </main>

    <footer class="bg-dark text-white py-2 mt-auto">
        <div class="container text-center">
            <small>2025</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>