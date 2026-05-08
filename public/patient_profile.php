<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.name,
        u.email,
        u.phone,
        u.gender,
        u.dob,
        p.blood_type,
        p.chronic_diseases
    FROM users u
    LEFT JOIN patients p ON u.id = p.user_id
    WHERE u.id = ? AND u.role = 'patient'
");
$stmt->execute([$user_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    die('لم يتم العثور على بيانات المريض.');
}

include '../includes/header.php';
?>

<h2>الملف الشخصي للمريض</h2>

<div class="card mt-4">
    <div class="card-body">
        <h4 class="card-title">
            <?= htmlspecialchars($patient['name']); ?>
        </h4>

        <p><strong>البريد الإلكتروني:</strong>
            <?= htmlspecialchars($patient['email']); ?>
        </p>

        <p><strong>رقم الهاتف:</strong>
            <?= htmlspecialchars($patient['phone'] ?? 'غير محدد'); ?>
        </p>

        <p><strong>الجنس:</strong>
            <?= htmlspecialchars($patient['gender'] ?? 'غير محدد'); ?>
        </p>

        <p><strong>تاريخ الميلاد:</strong>
            <?= htmlspecialchars($patient['dob'] ?? 'غير محدد'); ?>
        </p>

        <p><strong>فصيلة الدم:</strong>
            <?= htmlspecialchars($patient['blood_type'] ?? 'غير محدد'); ?>
        </p>

        <p><strong>الأمراض المزمنة:</strong><br>
            <?= nl2br(htmlspecialchars($patient['chronic_diseases'] ?? 'لا يوجد')); ?>
        </p>

        <a href="patient_edit_profile.php" class="btn btn-primary">
            تعديل الملف الشخصي
        </a>
        <a href="patient_dashboard.php" class="btn btn-secondary">العودة للوحة التحكم</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>