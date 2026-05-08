<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

$user_id = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $blood_type = trim($_POST['blood_type'] ?? '');
    $chronic_diseases = trim($_POST['chronic_diseases'] ?? '');

    $stmt = $pdo->prepare("
        UPDATE users 
        SET phone = ?, gender = ?, dob = ?
        WHERE id = ? AND role = 'patient'
    ");
    $stmt->execute([$phone, $gender, $dob, $user_id]);

    $stmt = $pdo->prepare("
        INSERT INTO patients (user_id, blood_type, chronic_diseases)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE
            blood_type = VALUES(blood_type),
            chronic_diseases = VALUES(chronic_diseases)
    ");
    $stmt->execute([$user_id, $blood_type, $chronic_diseases]);

    header('Location: patient_profile.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
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

include '../includes/header.php';
?>

<h2>تعديل الملف الشخصي</h2>

<form method="POST" class="mt-4">
    <div class="mb-3">
        <label class="form-label">رقم الهاتف</label>
        <input type="text" name="phone" class="form-control"
               value="<?= htmlspecialchars($patient['phone'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">الجنس</label>
        <select name="gender" class="form-control">
            <option value="">اختر الجنس</option>
            <option value="male" <?= ($patient['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>ذكر</option>
            <option value="female" <?= ($patient['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>أنثى</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">تاريخ الميلاد</label>
        <input type="date" name="dob" class="form-control"
               value="<?= htmlspecialchars($patient['dob'] ?? ''); ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">فصيلة الدم</label>
       <div class="mb-3">


    <select name="blood_type" class="form-control">
        <option value="">اختر فصيلة الدم</option>

        <?php
        $blood_types = [
            'A+','A-',
            'B+','B-',
            'AB+','AB-',
            'O+','O-'
        ];

        foreach ($blood_types as $type):
        ?>
            <option value="<?= $type; ?>"
                <?= ($patient['blood_type'] ?? '') === $type ? 'selected' : ''; ?>>
                <?= $type; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
    </div>

    <div class="mb-3">
        <label class="form-label">الأمراض المزمنة</label>
        <textarea name="chronic_diseases" class="form-control" rows="4"><?= htmlspecialchars($patient['chronic_diseases'] ?? ''); ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">حفظ التعديلات</button>
    <a href="patient_profile.php" class="btn btn-secondary">إلغاء</a>
</form>

<?php include '../includes/footer.php'; ?>