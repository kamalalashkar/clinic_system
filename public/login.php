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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = $user;

        $role = $user['role'];
        if ($role == 'patient') {
            header("Location: patient_dashboard.php");
        } elseif ($role == 'doctor') {
            header("Location: doctor_dashboard.php");
        } elseif ($role == 'admin') {
            header("Location: admin_dashboard.php");
        }
        exit();
    } else {
        $error = "بيانات الدخول غير صحيحة!";
    }
}

include '../includes/header.php';
?>

<h2 class="mb-4">تسجيل الدخول</h2>

<?php if (isset($error)): ?>
  <div class="alert alert-danger">
    <?= htmlspecialchars($error); ?>
  </div>
<?php endif; ?>

<form method="POST" class="mb-4">
  <div class="mb-3">
    <label for="email" class="form-label">البريد الإلكتروني</label>
    <input type="email" name="email" id="email" class="form-control" placeholder="example@email.com" required>
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">كلمة المرور</label>
    <input type="password" name="password" id="password" class="form-control" placeholder="********" required>
  </div>
  <button type="submit" class="btn btn-primary">دخول</button>
</form>

<p>ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a></p>

<?php include '../includes/footer.php'; ?>
