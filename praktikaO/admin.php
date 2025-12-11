<?php
$pageTitle = 'Админ панель';
require_once "db/db.php";

// Проверяем, что пользователь админ
if (!isset($_SESSION['user']) || $_SESSION['user']['user_type_id'] != 2) {
    header("Location: index.php");
    exit();
}

$message = '';

// Обработка изменения статуса отчета
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $report_id = (int)$_POST['report_id'];
    $new_status = mysqli_real_escape_string($db, $_POST['status'] ?? '');
    $teacher_comment = mysqli_real_escape_string($db, $_POST['teacher_comment'] ?? '');
    
    // Разрешенные статусы
    $allowed_statuses = ['pending', 'approved', 'rejected'];
    
    if (in_array($new_status, $allowed_statuses)) {
        // Если отправляем на доработку, комментарий обязателен
        if ($new_status == 'rejected' && empty($teacher_comment)) {
            $message = "При отправке на доработку необходимо указать комментарий!";
        } else {
            $query = "UPDATE practice_reports 
                      SET status = '$new_status', 
                          teacher_comment = " . ($teacher_comment ? "'$teacher_comment'" : "NULL") . "
                      WHERE id_report = '$report_id'";
            
            if (mysqli_query($db, $query)) {
                $message = "Статус отчета #$report_id успешно изменен!";
            } else {
                $message = "Ошибка при изменении статуса: " . mysqli_error($db);
            }
        }
    } else {
        $message = "Неверный статус!";
    }
}

// Получаем все отчеты о практике с информацией о студентах
$reports_query = mysqli_query($db, 
    "SELECT pr.*, u.surname, u.name, u.otchestvo, u.email, u.username
     FROM practice_reports pr
     JOIN user u ON pr.student_id = u.id_user
     ORDER BY pr.created_at DESC");

$reports = mysqli_fetch_all($reports_query, MYSQLI_ASSOC);

ob_start();
?>
<div class="container">
    <?php if ($message): ?>
        <div class="alert alert-<?= strpos($message, 'успешно') ? 'success' : 'danger' ?> text-center mb-3"><?= $message ?></div>
    <?php endif; ?>

    <h2 class="text-center mb-4">Все отчеты о практике</h2>
    
    <?php if (count($reports) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Студент</th>
                        <th>Группа</th>
                        <th>Организация</th>
                        <th>Период</th>
                        <th>Статус</th>
                        <th>Дата создания</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $report): 
                        // Цвета и тексты для статусов
                        $status_colors = [
                            'pending' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger'
                        ];
                        $status_texts = [
                            'pending' => 'На проверке',
                            'approved' => 'Принято',
                            'rejected' => 'На доработку'
                        ];
                        $color = $status_colors[$report['status']] ?? 'secondary';
                        $status_text = $status_texts[$report['status']] ?? 'Неизвестно';
                    ?>
                        <tr>
                            <td><strong>#<?= $report['id_report'] ?></strong></td>
                            <td>
                                <?= htmlspecialchars($report['surname']) ?> 
                                <?= htmlspecialchars($report['name']) ?> 
                                <?= htmlspecialchars($report['otchestvo']) ?>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($report['username']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($report['group_name']) ?></td>
                            <td><?= htmlspecialchars($report['organization_name']) ?></td>
                            <td>
                                <?= date('d.m.Y', strtotime($report['practice_start_date'])) ?><br>
                                <span class="text-muted">до</span><br>
                                <?= date('d.m.Y', strtotime($report['practice_end_date'])) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $color ?>"><?= $status_text ?></span>
                            </td>
                            <td><?= date('d.m.Y H:i', strtotime($report['created_at'])) ?></td>
                            <td>
                                <!-- Кнопка для просмотра деталей -->
                                <button type="button" class="btn btn-sm btn-info mb-2" data-bs-toggle="modal" data-bs-target="#reportModal<?= $report['id_report'] ?>">
                                    Просмотреть
                                </button>
                                
                                <!-- Форма для изменения статуса -->
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="report_id" value="<?= $report['id_report'] ?>">
                                    <div class="input-group input-group-sm mb-1">
                                        <select name="status" class="form-select form-select-sm" required id="statusSelect<?= $report['id_report'] ?>">
                                            <option value="pending" <?= $report['status'] == 'pending' ? 'selected' : '' ?>>На проверке</option>
                                            <option value="approved" <?= $report['status'] == 'approved' ? 'selected' : '' ?>>Принять</option>
                                            <option value="rejected" <?= $report['status'] == 'rejected' ? 'selected' : '' ?>>Отклонить</option>
                                        </select>
                                        <button type="submit" name="change_status" class="btn btn-primary btn-sm">✓</button>
                                    </div>
                                    <!-- Поле для комментария (показывается только при выборе "Отклонить") -->
                                    <div class="comment-field mt-1" id="commentField<?= $report['id_report'] ?>" style="display: <?= $report['status'] == 'rejected' ? 'block' : 'none' ?>;">
                                        <textarea name="teacher_comment" class="form-control form-control-sm" 
                                                 placeholder="Укажите причину отправки на доработку..." rows="2"><?= htmlspecialchars($report['teacher_comment'] ?? '') ?></textarea>
                                        <small class="text-muted">Обязательно при отправке на доработку</small>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        
                        <!-- Модальное окно с деталями отчета -->
                        <div class="modal fade" id="reportModal<?= $report['id_report'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Отчет #<?= $report['id_report'] ?> - <?= htmlspecialchars($report['organization_name']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6>Информация о студенте:</h6>
                                                <p><strong>ФИО:</strong> <?= htmlspecialchars($report['fio']) ?></p>
                                                <p><strong>Группа:</strong> <?= htmlspecialchars($report['group_name']) ?></p>
                                                <p><strong>Специальность:</strong> <?= htmlspecialchars($report['specialty']) ?></p>
                                                <p><strong>Логин:</strong> <?= htmlspecialchars($report['username']) ?></p>
                                                <p><strong>Email:</strong> <?= htmlspecialchars($report['email']) ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Место практики:</h6>
                                                <p><strong>Организация:</strong> <?= htmlspecialchars($report['organization_name']) ?></p>
                                                <p><strong>Адрес:</strong> <?= htmlspecialchars($report['organization_address']) ?></p>
                                                <p><strong>Руководитель от организации:</strong> <?= htmlspecialchars($report['supervisor_info']) ?></p>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6>Период практики:</h6>
                                            <p>
                                                с <?= date('d.m.Y', strtotime($report['practice_start_date'])) ?> 
                                                по <?= date('d.m.Y', strtotime($report['practice_end_date'])) ?>
                                            </p>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6>Описание выполненных работ:</h6>
                                            <div class="border p-3 bg-light rounded">
                                                <?= nl2br(htmlspecialchars($report['work_description'])) ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Комментарий преподавателя (если есть) -->
                                        <?php if (!empty($report['teacher_comment'])): ?>
                                        <div class="mb-3">
                                            <h6>Комментарий преподавателя:</h6>
                                            <div class="border p-3 bg-warning bg-opacity-10 rounded">
                                                <?= nl2br(htmlspecialchars($report['teacher_comment'])) ?>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Статус:</strong> <span class="badge bg-<?= $color ?>"><?= $status_text ?></span></p>
                                            </div>
                                            <div class="col-md-6 text-end">
                                                <p><strong>Создан:</strong> <?= date('d.m.Y H:i', strtotime($report['created_at'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="mt-3 text-muted">
            <small>Всего отчетов: <?= count($reports) ?></small>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <p class="text-muted">Отчетов пока нет</p>
        </div>
    <?php endif; ?>
</div>

<!-- Стили для таблицы -->
<style>
    .table th {
        background-color: #343a40;
        color: white;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
</style>

<!-- JS для показа/скрытия поля комментария -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обработчик изменения статуса
    document.querySelectorAll('select[name="status"]').forEach(select => {
        const reportId = select.id.replace('statusSelect', '');
        const commentField = document.getElementById('commentField' + reportId);
        
        // Функция для показа/скрытия комментария
        function toggleCommentField() {
            if (select.value === 'rejected') {
                commentField.style.display = 'block';
            } else {
                commentField.style.display = 'none';
            }
        }
        
        // Инициализация при загрузке
        toggleCommentField();
        
        // Обработчик изменения
        select.addEventListener('change', toggleCommentField);
    });
    
    // Обработчик отправки формы (проверка комментария при отклонении)
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const statusSelect = this.querySelector('select[name="status"]');
            const commentField = this.querySelector('textarea[name="teacher_comment"]');
            
            if (statusSelect && statusSelect.value === 'rejected') {
                if (!commentField || commentField.value.trim() === '') {
                    e.preventDefault();
                    alert('При отправке на доработку необходимо указать комментарий!');
                    commentField.focus();
                }
            }
        });
    });
});
</script>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>