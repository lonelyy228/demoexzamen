<?php
$pageTitle = 'Регистрация';
require_once "db/db.php";

$errors = [];
$success = '';
$fields = ['surname','name','otchestvo','gruppa','number','email','login','password'];
$labels = ['Фамилия','Имя','Отчество','Группа','Номер билета','Email','Логин','Пароль'];

foreach($fields as $field) $$field = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach($fields as $field) $$field = trim($_POST[$field] ?? '');
    
    foreach($fields as $i => $field) {
        if (empty($$field)) $errors[] = "Заполните " . $labels[$i];
    }
    
    if (!empty($login)) {
        $check = mysqli_query($db, "SELECT id_user FROM user WHERE username = '$login'");
        if (mysqli_num_rows($check) > 0) $errors[] = "Логин уже занят";
    }
    
    // Проверка email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный email";
    }
        
    if (empty($errors)) {
        // ПРАВИЛЬНЫЙ запрос с верным порядком полей
        $sql = "INSERT INTO user (user_type_id, surname, name, otchestvo, gruppa, number, email, username, password) 
                VALUES ('1', '$surname', '$name', '$otchestvo', '$gruppa', '$number', '$email', '$login', MD5('$password'))";
        
        if (mysqli_query($db, $sql)) {
            $success = "Регистрация успешна!";
            foreach($fields as $field) $$field = '';
        } else {
            $errors[] = "Ошибка базы данных: " . mysqli_error($db);
        }
    }
}

ob_start();
?>
<div class="col-md-8 col-lg-6 mx-auto">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger mb-3">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success mb-3"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 rounded shadow-sm">
        <?php foreach($fields as $i => $field): ?>
        <div class="mb-3">
            <label class="form-label"><?= $labels[$i] ?> *</label>
            <?php if($field == 'password'): ?>
                <input type="password" name="<?= $field ?>" class="form-control" value="<?= htmlspecialchars($$field) ?>" required>
            <?php elseif($field == 'email'): ?>
                <input type="email" name="<?= $field ?>" class="form-control" value="<?= htmlspecialchars($$field) ?>" required>
            <?php else: ?>
                <input type="text" name="<?= $field ?>" class="form-control" value="<?= htmlspecialchars($$field) ?>" required>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        
        <button type="submit" class="btn btn-primary w-100 py-2">Зарегистрироваться</button>
    </form>
    
    <p class="text-center mt-3">
        <small>Есть аккаунт? <a href="index.php">Войдите</a></small>
    </p>
</div>
<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>