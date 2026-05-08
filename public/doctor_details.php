<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

$doctor_id = $_GET['id'] ?? null;

if (!$doctor_id) {
    die("لم يتم تحديد الطبيب");
}

$stmt = $pdo->prepare("
    SELECT 
        u.id,
        u.name,
        u.email,
        u.phone,
        u.gender,
        d.specialty,
        d.bio,
        d.consultation_fee
    FROM users u
    JOIN doctors d ON u.id = d.user_id
    WHERE u.id = ? AND u.role = 'doctor'
");

$stmt->execute([$doctor_id]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    die("الطبيب غير موجود");
}

include '../includes/header.php';
?>

<h2 class="mb-4">تفاصيل الطبيب</h2>

<div class="card">
    <div class="card-header bg-primary text-white">
        د. <?= htmlspecialchars($doctor['name']) ?>
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
                <td>
                    <?php
                    if (($doctor['gender'] ?? '') === 'male') {
                        echo 'ذكر';
                    } elseif (($doctor['gender'] ?? '') === 'female') {
                        echo 'أنثى';
                    } else {
                        echo 'غير محدد';
                    }
                    ?>
                </td>
            </tr>

            <tr>
                <th>التخصص</th>
                <td><?= htmlspecialchars($doctor['specialty']) ?></td>
            </tr>

            <tr>
                <th>نبذة عن الطبيب</th>
                <td><?= nl2br(htmlspecialchars($doctor['bio'] ?? 'غير محدد')) ?></td>
            </tr>

            <tr>
                <th>رسوم الاستشارة</th>
                <td><?= htmlspecialchars($doctor['consultation_fee']) ?> ل س</td>
            </tr>
        </table>

        <a href="book_appointment.php?doctor_id=<?= $doctor['id'] ?>" class="btn btn-success">
            حجز موعد
        </a>

        <a href="search_doctors.php" class="btn btn-secondary">
            رجوع
        </a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>