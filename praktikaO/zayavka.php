<?php
$pageTitle = 'Отчеты';
require_once "db/db.php";
if (!isset($_SESSION['user'])) header("Location: index.php");

$stmt = mysqli_prepare($db, "SELECT s.*, st.name_service, pt.name_pay, stat.name_status, s.status_id 
    FROM service s
    JOIN service_type st ON s.service_type_id = st.id_service_type
    JOIN pay_type pt ON s.pay_type_id = pt.id_pay_type
    JOIN status stat ON s.status_id = stat.id_status
    WHERE s.user_id = ? ORDER BY s.data DESC");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user']['id_user']);
mysqli_stmt_execute($stmt);
$zayavki = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

ob_start();
?>
<?php if (empty($zayavki)): ?>
    <div class="text-center py-5">
        <p class="text-muted">У вас пока нет отчетов</p>
        <a href="create_zayavka.php" class="btn btn-primary">Создать отчет</a>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 g-3">
        <?php foreach ($zayavki as $z): 
            $colors = [1=>'success',2=>'primary',3=>'danger'];
            $color = $colors[$z['status_id']] ?? 'secondary';
        ?>
            <div class="col">
                <div class="card card-custom">
                    <div class="card-header d-flex justify-content-between">
                        <span>Отчет #<?= $z['id_service'] ?></span>
                        <span class="badge bg-<?= $color ?>"><?= $z['name_status'] ?></span>
                    </div>
                    <div class="card-body">
                        <p><strong>Адрес:</strong> <?= $z['address'] ?></p>
                        <p><strong>Услуга:</strong> <?= $z['name_service'] ?></p>
                        <p><strong>Дата:</strong> <?= $z['data'] ?> <strong>Время:</strong> <?= $z['time'] ?></p>
                        <p><strong>Оплата:</strong> <?= $z['name_pay'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="text-center mt-3">
        <a href="create_zayavka.php" class="btn btn-success">Создать новый</a>
    </div>
<?php endif; ?>
<?php $pageContent = ob_get_clean(); require_once "struktura.php"; ?>