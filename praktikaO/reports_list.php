<?php
$pageTitle = 'Мои отчеты о практике';
require_once "db/db.php";
if (!isset($_SESSION['user'])) header("Location: index.php");

$user_id = $_SESSION['user']['id_user'];

// Получаем отчеты студента
$reports_query = mysqli_query($db, 
    "SELECT * FROM practice_reports 
     WHERE student_id = '$user_id' 
     ORDER BY created_at DESC");

$reports = mysqli_fetch_all($reports_query, MYSQLI_ASSOC);

ob_start();
?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Мои отчеты о практике</h2>
        <a href="create_zayavka.php" class="btn btn-success">+ Новый отчет</a>
    </div>

    <?php if (empty($reports)): ?>
        <div class="text-center py-5">
            <p class="text-muted">У вас пока нет отчетов о практике</p>
            <a href="create_zayavka.php" class="btn btn-primary">Создать первый отчет</a>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 g-4">
            <?php foreach ($reports as $report): 
                // Цвета и тексты для статусов
                $status_color = [
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger'
                ];
                $status_text = [
                    'pending' => 'На проверке',
                    'approved' => 'Принято',
                    'rejected' => 'На доработку'
                ];
                $color = $status_color[$report['status']] ?? 'secondary';
                $text = $status_text[$report['status']] ?? 'Неизвестно';
            ?>
                <div class="col">
                    <div class="card card-custom h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Отчет #<?= $report['id_report'] ?></span>
                            <span class="badge bg-<?= $color ?>"><?= $text ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($report['organization_name']) ?></h5>
                            <p><strong>ФИО:</strong> <?= htmlspecialchars($report['fio']) ?></p>
                            <p><strong>Группа:</strong> <?= htmlspecialchars($report['group_name']) ?></p>
                            <p><strong>Специальность:</strong> <?= htmlspecialchars($report['specialty']) ?></p>
                            <p><strong>Период практики:</strong><br>
                                <?= date('d.m.Y', strtotime($report['practice_start_date'])) ?> - 
                                <?= date('d.m.Y', strtotime($report['practice_end_date'])) ?>
                            </p>
                            <p><strong>Дата подачи:</strong> <?= date('d.m.Y H:i', strtotime($report['created_at'])) ?></p>
                            
                            <!-- Комментарий преподавателя при статусе "На доработку" -->
                            <?php if ($report['status'] == 'rejected' && !empty($report['teacher_comment'])): ?>
                                <div class="alert alert-danger mt-3 mb-0 py-2">
                                    <small>
                                        <strong><i class="bi bi-exclamation-triangle"></i> Отчет отправлен на доработку!</strong><br>
                                        <strong>Комментарий преподавателя:</strong><br>
                                        <?= nl2br(htmlspecialchars($report['teacher_comment'])) ?>
                                    </small>
                                </div>
                            <?php elseif ($report['status'] == 'rejected'): ?>
                                <div class="alert alert-danger mt-3 mb-0 py-2">
                                    <small><strong><i class="bi bi-exclamation-triangle"></i> Отчет отправлен на доработку!</strong> Пожалуйста, пересмотрите информацию и отправьте заново.</small>
                                </div>
                            <?php elseif ($report['status'] == 'approved'): ?>
                                <div class="alert alert-success mt-3 mb-0 py-2">
                                    <small><strong><i class="bi bi-check-circle"></i> Отчет принят!</strong> Ваш отчет успешно прошел проверку.</small>
                                </div>
                            <?php elseif ($report['status'] == 'pending'): ?>
                                <div class="alert alert-warning mt-3 mb-0 py-2">
                                    <small><strong><i class="bi bi-clock"></i> Отчет находится на проверке.</strong> Ожидайте решения преподавателя.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-transparent">
                            <!-- Кнопка просмотра отчета -->
                            <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewReport<?= $report['id_report'] ?>">
                                <i class="bi bi-eye"></i> Просмотреть отчет
                            </button>
                            
                            <!-- Кнопка создания нового отчета (если отклонен) -->
                            <?php if ($report['status'] == 'rejected'): ?>
                                <a href="create_zayavka.php" class="btn btn-sm btn-warning ms-2">
                                    <i class="bi bi-pencil"></i> Исправить
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Модальное окно для просмотра полного отчета -->
                <div class="modal fade" id="viewReport<?= $report['id_report'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Отчет #<?= $report['id_report'] ?> - <?= htmlspecialchars($report['organization_name']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Информация о студенте -->
                                <h6 class="border-bottom pb-2">Информация о студенте:</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>ФИО:</strong> <?= htmlspecialchars($report['fio']) ?></p>
                                        <p><strong>Группа:</strong> <?= htmlspecialchars($report['group_name']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Специальность:</strong> <?= htmlspecialchars($report['specialty']) ?></p>
                                        <p><strong>Статус:</strong> <span class="badge bg-<?= $color ?>"><?= $text ?></span></p>
                                    </div>
                                </div>
                                
                                <!-- Место практики -->
                                <h6 class="border-bottom pb-2 mt-4">Место прохождения практики:</h6>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <p><strong>Организация:</strong> <?= htmlspecialchars($report['organization_name']) ?></p>
                                        <p><strong>Адрес:</strong> <?= htmlspecialchars($report['organization_address']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Руководитель от организации:</strong> <?= htmlspecialchars($report['supervisor_info']) ?></p>
                                    </div>
                                </div>
                                
                                <!-- Период практики -->
                                <h6 class="border-bottom pb-2 mt-4">Период практики:</h6>
                                <p class="mb-3">
                                    <strong>Дата начала:</strong> <?= date('d.m.Y', strtotime($report['practice_start_date'])) ?><br>
                                    <strong>Дата окончания:</strong> <?= date('d.m.Y', strtotime($report['practice_end_date'])) ?>
                                </p>
                                
                                <!-- Описание работ -->
                                <h6 class="border-bottom pb-2 mt-4">Описание выполненных работ:</h6>
                                <div class="border p-3 bg-light rounded mb-3">
                                    <?= nl2br(htmlspecialchars($report['work_description'])) ?>
                                </div>
                                
                                <!-- Комментарий преподавателя (если есть) -->
                                <?php if (!empty($report['teacher_comment'])): ?>
                                    <h6 class="border-bottom pb-2 mt-4">Комментарий преподавателя:</h6>
                                    <div class="border p-3 bg-warning bg-opacity-10 rounded mb-3">
                                        <?= nl2br(htmlspecialchars($report['teacher_comment'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Дополнительная информация -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <p><strong>Дата подачи отчета:</strong><br><?= date('d.m.Y H:i', strtotime($report['created_at'])) ?></p>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <p><strong>Статус проверки:</strong><br><span class="badge bg-<?= $color ?>"><?= $text ?></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                <?php if ($report['status'] == 'rejected'): ?>
                                    <a href="create_zayavka.php" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i> Создать исправленный отчет
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Статистика -->
        <div class="mt-4 p-3 bg-light rounded">
            <div class="row text-center">
                <div class="col-md-3">
                    <h5><?= count($reports) ?></h5>
                    <small class="text-muted">Всего отчетов</small>
                </div>
                <div class="col-md-3">
                    <h5><?= count(array_filter($reports, fn($r) => $r['status'] == 'pending')) ?></h5>
                    <small class="text-muted">На проверке</small>
                </div>
                <div class="col-md-3">
                    <h5><?= count(array_filter($reports, fn($r) => $r['status'] == 'approved')) ?></h5>
                    <small class="text-muted">Принято</small>
                </div>
                <div class="col-md-3">
                    <h5><?= count(array_filter($reports, fn($r) => $r['status'] == 'rejected')) ?></h5>
                    <small class="text-muted">На доработку</small>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Добавьте иконки Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<style>
    .card-custom {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }
    .card-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
    }
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }
    .alert-warning {
        border-left-color: #ffc107;
    }
    .alert-success {
        border-left-color: #198754;
    }
    .alert-danger {
        border-left-color: #dc3545;
    }
</style>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>