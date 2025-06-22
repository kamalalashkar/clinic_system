<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    if ($role == 'patient') {
        header("Location: patient_dashboard.php");
    } elseif ($role == 'doctor') {
        header("Location: doctor_dashboard.php");
    } elseif ($role == 'admin') {
        header("Location: admin_dashboard.php");
    }
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];
    $blood_type = trim($_POST['blood_type']);
    $chronic_diseases = trim($_POST['chronic_diseases']);

    if (empty($name) || empty($email) || empty($password) || empty($confirm) || empty($blood_type) || empty($chronic_diseases)) {
        $errors[] = "جميع الحقول مطلوبة!";
    }

    if ($password !== $confirm) {
        $errors[] = "كلمة المرور وتأكيدها غير متطابقين!";
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = "البريد الإلكتروني مستخدم مسبقًا!";
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, 'patient')");
        $stmt->execute([$name, $email, $hash]);

        $last_user_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO patients (user_id, blood_type, chronic_diseases) VALUES (?, ?, ?)");
        $stmt->execute([$last_user_id, $blood_type, $chronic_diseases]);

        header("Location: login.php");
        exit();
    }
}

include '../includes/header.php';
?>

<h2 class="mb-4">إنشاء حساب جديد</h2>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="POST" class="mb-4">
  <div class="mb-3">
    <label for="name" class="form-label">الاسم الكامل</label>
    <input type="text" name="name" id="name" class="form-control" placeholder="أدخل اسمك" required>
  </div>

  <div class="mb-3">
    <label for="email" class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" id="email" class="form-control" placeholder="example@email.com" required>
  </div>

  <div class="mb-3">
    <label for="password" class="form-label">كلمة المرور</label>
    <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
  </div>

  <div class="mb-3">
    <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
    <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="********" required>
  </div>

  <div class="mb-3">
    <label for="blood_type" class="form-label">فصيلة الدم</label>
    <select name="blood_type" id="blood_type" class="form-select" required>
      <option value="">-- اختر فصيلة الدم --</option>
      <option value="A+">A+</option>
      <option value="A-">A-</option>
      <option value="B+">B+</option>
      <option value="B-">B-</option>
      <option value="AB+">AB+</option>
      <option value="AB-">AB-</option>
      <option value="O+">O+</option>
      <option value="O-">O-</option>
    </select>
  </div>

  <div class="mb-3">
    <label for="chronic_diseases" class="form-label">الأمراض المزمنة</label>
    <textarea name="chronic_diseases" id="chronic_diseases" class="form-control" placeholder="اذكر الأمراض المزمنة إن وجدت" required></textarea>
  </div>

  <button type="submit" class="btn btn-primary">إنشاء الحساب</button>
</form>

<p>لديك حساب؟ <a href="login.php">تسجيل الدخول</a></p>

<?php include '../includes/footer.php'; ?>
