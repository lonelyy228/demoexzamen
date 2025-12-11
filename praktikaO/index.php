<?php
$pageTitle = 'Авторизация';
require_once "db/db.php";

if (isset($_SESSION['user'])) {
    header("Location: " . ($_SESSION['user']['user_type_id'] == 2 ? 'admin.php' : 'reports_list.php'));
    exit();
}

$loginError = '';
$login = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = strip_tags($_POST['login'] ?? '');
    $password = strip_tags($_POST['password'] ?? '');
    
    if ($user = find($login, $password)) {
        $_SESSION['user'] = $user;
        header("Location: " . ($user['user_type_id'] == 2 ? 'admin.php' : 'reports_list.php'));
        exit();
    } else {
        $loginError = "Неверный логин или пароль.";
    }
}

ob_start();
?>
<div class="col-md-6 mx-auto">
    <?php if ($loginError): ?>
        <div class="alert alert-danger text-center mb-3"><?= $loginError ?></div>
    <?php endif; ?>
    <form method="post" class="bg-white p-4 rounded shadow-sm">
        <div class="mb-3">
            <label class="form-label">Логин</label>
            <input type="text" name="login" class="form-control" value="<?= htmlspecialchars($login) ?>" required>
        </div>
        <div class="mb-4">
            <label class="form-label">Пароль</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Вход</button>
    </form>
    <p class="text-center mt-3">
        <small>Нет аккаунта? <a href="registration.php">Зарегистрируйтесь</a></small>
    </p>
</div>
<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>