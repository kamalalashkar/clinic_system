<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkDoctor();

$doctor_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
    SELECT 
        u.name,
        u.email,
        u.phone,
        u.gender,
        u.dob,
        d.specialty,
        d.bio,
        d.consultation_fee
    FROM users u
    LEFT JOIN doctors d ON u.id = d.user_id
    WHERE u.id = ? AND u.role = 'doctor'
");

$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("لم يتم العثور على بيانات الطبيب");
}

include '../includes/header.php';
?>

<h2 class="mb-4">الملف الشخصي للطبيب</h2>

<div class="card">
    <div class="card-header bg-primary text-white">
        بيانات الطبيب
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>الاسم</th>
                <td><?= htmlspecialchars($doctor['name']) ?></td>
            </tr>

            <tr>
                <th>البريد الإلكتروني</th>
                <td><?= htmlspecialchars($doctor['email']) ?></td>
            </tr>

            <tr>
                <th>رقم الهاتف</th>
                <td><?= htmlspecialchars($doctor['phone'] ?? 'غير محدد') ?></td>
            </tr>

            <tr>
                <th>الجنس</th>
                <td><?= htmlspecialchars($doctor['gender'] ?? 'غير محدد') ?></td>
            </tr>

            <tr>
                <th>تاريخ الميلاد</th>
                <td><?= htmlspecialchars($doctor['dob'] ?? 'غير محدد') ?></td>
            </tr>

            <tr>
                <th>التخصص</th>
                <td><?= htmlspecialchars($doctor['specialty'] ?? 'غير محدد') ?></td>
            </tr>

            <tr>
                <th>نبذة عن الطبيب</th>
                <td><?= nl2br(htmlspecialchars($doctor['bio'] ?? 'غير محدد')) ?></td>
            </tr>

            <tr>
                <th>رسوم الاستشارة</th>
                <td><?= htmlspecialchars($doctor['consultation_fee'] ?? '0') ?> ل س</td>
            </tr>
        </table>
    </div>
</div>

<a href="edit_doctor_profile.php" class="btn btn-primary mt-3">
    تعديل الملف الشخصي
</a>

<a href="doctor_dashboard.php" class="btn btn-secondary mt-3">
    العودة للوحة التحكم
</a>

<?php include '../includes/footer.php'; ?>