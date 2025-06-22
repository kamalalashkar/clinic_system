<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

// تحقق أن المستخدم هو مريض
checkPatient();

include '../includes/header.php';

$patient_id = $_SESSION['user']['id'];

// جلب جميع الوصفات الخاصة بالمريض مع اسم الطبيب
$stmt = $pdo->prepare("
    SELECT p.*, u.name AS doctor_name 
    FROM prescriptions p
    JOIN users u ON p.doctor_id = u.id
    WHERE p.patient_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll();
?>

<h2>وصفاتي الطبية</h2>

<?php if ($prescriptions): ?>
    <div class="list-group">
        <?php foreach ($prescriptions as $prescription): ?>
            <div class="list-group-item mb-3">
                <strong>الطبيب:</strong> د. <?= htmlspecialchars($prescription['doctor_name']); ?><br>
                <strong>التاريخ:</strong> <?= htmlspecialchars($prescription['created_at']); ?><br>
                <strong>التفاصيل:</strong>
                <p><?= nl2br(htmlspecialchars($prescription['content'])); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>ليس لديك وصفات طبية مسجلة حتى الآن.</p>
<?php endif; ?>


<?php include '../includes/footer.php'; ?>
