<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

$admin_id = $_SESSION['user']['id'];

$message = "";

// جلب البيانات الحالية
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    die("الإداري غير موجود");
}

// عند حفظ التعديلات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $dob = $_POST['dob'];

    // تحديث البيانات الأساسية
    $stmt = $pdo->prepare("
        UPDATE users 
        SET name = ?, email = ?, phone = ?, gender = ?, dob = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $name,
        $email,
        $phone,
        $gender,
        $dob,
        $admin_id
    ]);

    // تحديث كلمة المرور إذا تم إدخالها
    if (!empty($_POST['password'])) {

        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = ?
            WHERE id = ?
        ");

        $stmt->execute([$new_password, $admin_id]);
    }

    $message = "تم تحديث الملف الشخصي بنجاح";

    // تحديث بيانات الجلسة
    $_SESSION['user']['name'] = $name;

    // إعادة تحميل البيانات
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
}

include '../includes/header.php';
?>

<h2 class="mb-4">تعديل الملف الشخصي</h2>

<?php if ($message): ?>
    <div class="alert alert-success">
        <?= $message ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">

        <form method="POST">

            <div class="mb-3">
                <label class="form-label">الاسم</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($admin['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($admin['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">رقم الهاتف</label>
                <input type="text" name="phone" class="form-control"
                       value="<?= htmlspecialchars($admin['phone']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">الجنس</label>
                <select name="gender" class="form-control">
                    <option value="ذكر" <?= $admin['gender'] == 'ذكر' ? 'selected' : '' ?>>
                        ذكر
                    </option>

                    <option value="أنثى" <?= $admin['gender'] == 'أنثى' ? 'selected' : '' ?>>
                        أنثى
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">تاريخ الميلاد</label>
                <input type="date" name="dob" class="form-control"
                       value="<?= $admin['dob'] ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">
                    كلمة المرور الجديدة (اختياري)
                </label>

                <input type="password" name="password" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">
                حفظ التعديلات
            </button>

            <a href="admin_profile.php" class="btn btn-secondary">
                رجوع
            </a>

        </form>

    </div>
</div>

<?php include '../includes/footer.php'; ?>