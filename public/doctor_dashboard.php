<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkDoctor();

include '../includes/header.php';
?>

<?php
$gender = $_SESSION['user']['gender'];
$title = ($gender === 'female') ? 'دكتورة' : 'دكتور';
?>

<h2>مرحبًا <?= $title ?> <?= htmlspecialchars($_SESSION['user']['name']); ?></h2>
<p>هذه لوحة تحكم الطبيب. اختر ما ترغب في القيام به من الخيارات التالية:</p>

<h3>مواعيد اليوم</h3>
<?php
$doctor_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
  SELECT a.*, u.name AS patient_name
  FROM appointments a
  JOIN users u ON a.patient_id = u.id
  WHERE a.doctor_id = ?
    AND DATE(a.appointment_date) = CURDATE()
  ORDER BY a.appointment_date ASC
");
$stmt->execute([$doctor_id]);
$appointments = $stmt->fetchAll();

if (count($appointments) > 0): ?>
    <ul class="list-group">
    <?php foreach ($appointments as $appointment): ?>
        <li class="list-group-item">
            <strong>المريض:</strong> <?= htmlspecialchars($appointment['patient_name']); ?> 
            <br>
            <strong>الوقت:</strong> <?= htmlspecialchars($appointment['appointment_date']); ?>
            <br>
            <strong>الحالة:</strong> <?= htmlspecialchars($appointment['status']); ?>
        </li>
    <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>لا توجد مواعيد لهذا اليوم.</p>
<?php endif; ?>

<h3 class="mt-4">الإعدادات</h3>
<div class="list-group">
    <a href="doctor_profile.php" class="list-group-item list-group-item-action">الملف الشخصي</a>
    
    <a href="view_calendar.php" class="list-group-item list-group-item-action">عرض جميع مواعيدي</a>
    <a href="manage_medical_records.php" class="list-group-item list-group-item-action">إدارة السجلات الطبية</a>
    <a href="prescription.php" class="list-group-item list-group-item-action">إصدار الوصفات الطبية</a>
    <a href="suggest_diagnosis.php" class="list-group-item list-group-item-action">
    اقتراح تشخيص أولي
</a>
    <a href="consultation.php" class="list-group-item list-group-item-action">الاستشارات عن بعد</a>
</div>

<?php include '../includes/footer.php'; ?>
