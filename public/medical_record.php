<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

include '../includes/header.php';

$patient_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM medical_records WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$medical_records = $stmt->fetchAll();
?>

<h2>السجل الطبي</h2>

<?php if (count($medical_records) > 0): ?>
    <ul class="list-group">
    <?php foreach ($medical_records as $record): ?>
        <li class="list-group-item">
            <strong>التشخيص:</strong> <?= htmlspecialchars($record['diagnosis']); ?> 
            <br>
            <strong>العلاج:</strong> <?= htmlspecialchars($record['treatment']); ?>
            <br>
            <strong>التاريخ:</strong> <?= htmlspecialchars($record['created_at']); ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>لا توجد سجلات طبية حالياً.</p>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
