<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkDoctor();

$doctor_id = $_SESSION['user']['id'];
$message = "";

// جلب البيانات الحالية
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

// عند حفظ التعديلات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $specialty = trim($_POST['specialty'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $consultation_fee = trim($_POST['consultation_fee'] ?? 0);

    // تحديث جدول users
    $stmt = $pdo->prepare("
        UPDATE users 
        SET name = ?, email = ?, phone = ?, gender = ?, dob = ?
        WHERE id = ? AND role = 'doctor'
    ");

    $stmt->execute([
        $name,
        $email,
        $phone,
        $gender,
        $dob,
        $doctor_id
    ]);

    // تحديث أو إضافة بيانات الطبيب في جدول doctors
    $stmt = $pdo->prepare("
        INSERT INTO doctors (user_id, specialty, bio, consultation_fee)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            specialty = VALUES(specialty),
            bio = VALUES(bio),
            consultation_fee = VALUES(consultation_fee)
    ");

    $stmt->execute([
        $doctor_id,
        $specialty,
        $bio,
        $consultation_fee
    ]);

    // تحديث كلمة المرور إذا تم إدخالها
    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = ?
            WHERE id = ? AND role = 'doctor'
        ");

        $stmt->execute([$new_password, $doctor_id]);
    }

    $_SESSION['user']['name'] = $name;

    header("Location: doctor_profile.php");
    exit;
}

include '../includes/header.php';
?>

<h2 class="mb-4">تعديل الملف الشخصي للطبيب</h2>

<div class="card">
    <div class="card-body">

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">الاسم</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($doctor['name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($doctor['email'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control"
                       value="<?= htmlspecialchars($doctor['phone'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">الجنس</label>
                <select name="gender" class="form-control">
                    <option value="">اختر الجنس</option>
                    <option value="male" <?= ($doctor['gender'] ?? '') === 'male' ? 'selected' : '' ?>>ذكر</option>
                    <option value="female" <?= ($doctor['gender'] ?? '') === 'female' ? 'selected' : '' ?>>أنثى</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">تاريخ الميلاد</label>
                <input type="date" name="dob" class="form-control"
                       value="<?= htmlspecialchars($doctor['dob'] ?? '') ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">التخصص</label>
                <input type="text" name="specialty" class="form-control"
                       value="<?= htmlspecialchars($doctor['specialty'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">نبذة عن الطبيب</label>
                <textarea name="bio" class="form-control" rows="4"><?= htmlspecialchars($doctor['bio'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">رسوم الاستشارة</label>
                <input type="number" step="0.01" name="consultation_fee" class="form-control"
                       value="<?= htmlspecialchars($doctor['consultation_fee'] ?? '0') ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">كلمة المرور الجديدة اختياري</label>
                <input type="password" name="password" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">
                حفظ التعديلات
            </button>

            <a href="doctor_profile.php" class="btn btn-secondary">
                رجوع
            </a>

        </form>

    </div>
</div>

<?php include '../includes/footer.php'; ?>