<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $errors[] = "جميع الحقول مطلوبة!";
    }

    if ($password !== $confirm) {
        $errors[] = "كلمة المرور وتأكيدها غير متطابقين!";
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "البريد الإلكتروني مستخدم من قبل!";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'admin')");
        $stmt->execute([$name, $email, $hash]);

        header("Location: manage_admins.php");
        exit();
    }
}

include '../includes/header.php';
?>

<h2>إضافة إداري جديد</h2>

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
    <label class="form-label">اسم الإداري</label>
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

  <button type="submit" class="btn btn-primary">حفظ الإداري</button>
</form>

<a href="manage_admins.php" class="btn btn-secondary">إلغاء والعودة</a>

<?php include '../includes/footer.php'; ?>
