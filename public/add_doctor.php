<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

// فقط الإداري يمكنه الوصول
checkAdmin();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $specialty = trim($_POST['specialty']);
    $bio = trim($_POST['bio']);
    $fee = trim($_POST['consultation_fee']);

    // التحقق من الحقول
    if (empty($name) || empty($email) || empty($password) || empty($confirm) || empty($specialty) || empty($fee)) {
        $errors[] = "جميع الحقول مطلوبة!";
    }

    if ($password !== $confirm) {
        $errors[] = "كلمة المرور وتأكيدها غير متطابقين!";
    }

    if (!is_numeric($fee) || $fee < 0) {
        $errors[] = "أتعاب الكشف يجب أن تكون رقمًا موجبًا.";
    }

    // تحقق من أن البريد غير مستخدم
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "البريد الإلكتروني مستخدم من قبل!";
    }

    if (empty($errors)) {
        // تشفير كلمة المرور
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // إضافة في جدول users
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'doctor')");
        $stmt->execute([$name, $email, $hash]);

        $doctor_id = $pdo->lastInsertId();

        // إضافة بيانات الطبيب في جدول doctors
        $stmt = $pdo->prepare("INSERT INTO doctors (user_id, specialty, bio, consultation_fee) VALUES (?, ?, ?, ?)");
        $stmt->execute([$doctor_id, $specialty, $bio, $fee]);

        // إعادة التوجيه لصفحة إدارة الأطباء
        header("Location: manage_doctors.php");
        exit();
    }
}

include '../includes/header.php';
?>

<h2>إضافة طبيب جديد</h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" class="mb-4">
  <div class="mb-3">
    <label class="form-label">اسم الطبيب</label>
    <input type="text" name="name" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">كلمة المرور</label>
    <input type="password" name="password" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">تأكيد كلمة المرور</label>
    <input type="password" name="confirm_password" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">التخصص</label>
    <input type="text" name="specialty" class="form-control" required>
  </div>

  <div class="mb-3">
    <label class="form-label">السيرة الذاتية</label>
    <textarea name="bio" class="form-control"></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">أتعاب الكشف</label>
    <input type="number" name="consultation_fee" step="0.01" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-primary">حفظ الطبيب</button>
</form>

<a href="manage_doctors.php" class="btn btn-secondary">إلغاء والعودة</a>

<?php include '../includes/footer.php'; ?>
