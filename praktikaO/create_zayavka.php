<?php
$pageTitle = 'Новый отчет о практике';
require_once "db/db.php"; 

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = $success = '';
$user = $_SESSION['user']; 
$user_id = $user['id_user'];

// Получение списка групп для выпадающего списка - исправлено поле gruppa
$groups_list_result = mysqli_query($db, "SELECT DISTINCT gruppa FROM user WHERE gruppa IS NOT NULL AND gruppa != '' AND gruppa != '0' ORDER BY gruppa");
$available_groups = mysqli_fetch_all($groups_list_result, MYSQLI_ASSOC);

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Сбор и очистка данных
    $fio_student = mysqli_real_escape_string($db, $_POST['fio'] ?? '');
    $group_name = mysqli_real_escape_string($db, $_POST['group_name'] ?? '');
    $specialty = mysqli_real_escape_string($db, $_POST['specialty'] ?? '');
    $date_from = mysqli_real_escape_string($db, $_POST['date_from'] ?? '');
    $date_to = mysqli_real_escape_string($db, $_POST['date_to'] ?? '');
    $org_name = mysqli_real_escape_string($db, $_POST['org_name'] ?? '');
    $org_address = mysqli_real_escape_string($db, $_POST['org_address'] ?? '');
    $supervisor_info = mysqli_real_escape_string($db, $_POST['supervisor_info'] ?? '');
    $work_description = mysqli_real_escape_string($db, $_POST['work_description'] ?? '');

    // Проверка на заполненность всех полей
    if (empty($fio_student) || empty($group_name) || empty($specialty) || 
        empty($date_from) || empty($date_to) || empty($org_name) || 
        empty($org_address) || empty($supervisor_info) || empty($work_description)) {
        
        $error = "Все поля обязательны для заполнения!";
    } else {
        $query = "INSERT INTO practice_reports (
                      student_id, 
                      fio, 
                      group_name, 
                      specialty, 
                      practice_start_date, 
                      practice_end_date, 
                      organization_name, 
                      organization_address, 
                      supervisor_info, 
                      work_description
                  ) VALUES (
                      '$user_id', 
                      '$fio_student', 
                      '$group_name', 
                      '$specialty', 
                      '$date_from', 
                      '$date_to', 
                      '$org_name', 
                      '$org_address', 
                      '$supervisor_info', 
                      '$work_description'
                  )";
        
        if (mysqli_query($db, $query)) {
            $success = "Отчет успешно создан!";
            // Очищаем POST данные
            $_POST = array();
        } else {
            $error = "Ошибка базы данных при создании отчета: " . mysqli_error($db);
        }
    }
}

ob_start();
?>
<div class="col-md-10 col-lg-8 mx-auto">
    <h2><?= htmlspecialchars($pageTitle) ?></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger text-center mb-3"><?= $error ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success text-center mb-3"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" class="bg-white p-4 p-md-5 rounded shadow-lg">
        
        <!-- ФИО -->
        <div class="mb-3">
            <label class="form-label required-label">ФИО студента</label>
            <input type="text" name="fio" class="form-control" 
                   value="<?= htmlspecialchars($_POST['fio'] ?? $user['surname'] . ' ' . $user['name'] . ' ' . $user['otchestvo']) ?>" required>
        </div>

        <!-- Группа и Специальность -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="form-label required-label">Группа</label>
                <select name="group_name" class="form-select" required>
                    <option value="">Выберите группу</option>
                    <?php 
                    $current_group = $_POST['group_name'] ?? $user['gruppa'];
                    
                    foreach ($available_groups as $group_item): 
                    ?>
                        <option value="<?= htmlspecialchars($group_item['gruppa']) ?>" 
                            <?= $current_group === $group_item['gruppa'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($group_item['gruppa']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label required-label">Специальность</label>
                <input type="text" name="specialty" class="form-control" 
                       value="<?= htmlspecialchars($_POST['specialty'] ?? '') ?>" required>
            </div>
        </div>
        
        <!-- Даты прохождения практики -->
        <h5 class="border-bottom pb-1 mt-4">Период практики</h5>
        <div class="row mb-4">
            <div class="col-md-6 mb-3 mb-md-0">
                <label class="form-label required-label">Дата начала практики</label>
                <input type="date" name="date_from" class="form-control" 
                       value="<?= htmlspecialchars($_POST['date_from'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label required-label">Дата окончания практики</label>
                <input type="date" name="date_to" class="form-control" 
                       value="<?= htmlspecialchars($_POST['date_to'] ?? '') ?>" required>
            </div>
        </div>

        <!-- Организация -->
        <h5 class="border-bottom pb-1">Место прохождения практики</h5>
        <div class="mb-3">
            <label class="form-label required-label">Название организации</label>
            <input type="text" name="org_name" class="form-control" 
                   value="<?= htmlspecialchars($_POST['org_name'] ?? '') ?>" required>
        </div>

        <div class="mb-4">
            <label class="form-label required-label">Адрес организации</label>
            <input type="text" name="org_address" class="form-control" 
                   value="<?= htmlspecialchars($_POST['org_address'] ?? '') ?>" required>
        </div>

        <!-- Руководитель от организации -->
        <div class="mb-4">
            <label class="form-label required-label">ФИО и должность руководителя от организации</label>
            <input type="text" name="supervisor_info" class="form-control" 
                   value="<?= htmlspecialchars($_POST['supervisor_info'] ?? '') ?>" required>
        </div>
        
        <!-- Описание работ -->
        <h5 class="border-bottom pb-1">Выполненные работы</h5>
        <div class="mb-4">
            <label class="form-label required-label">Краткое описание выполненных работ</label>
            <textarea name="work_description" class="form-control" rows="6" required><?= htmlspecialchars($_POST['work_description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-3 fs-5">Создать отчет</button>
    </form>
    
    <div class="text-center mt-4">
        <a href="zayavka.php" class="text-decoration-none">← Назад к отчетам</a>
    </div>
</div>

<style>
    .required-label::after {
        content: ' *';
        color: red;
    }
</style>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php"; 
?>