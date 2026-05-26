<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

if (!isset($_GET['id'])) {
    die("رقم المستخدم غير موجود!");
}

$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, $id]);

    header("Location: edit_user.php?id=" . $id . "&success=1");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    die("المستخدم غير موجود!");
}

/* تحديد صفحة الرجوع حسب نوع المستخدم */
$backPage = 'admin_dashboard.php';

switch ($user['role']) {
    case 'admin':
        $backPage = 'manage_admins.php';
        break;

    case 'doctor':
        $backPage = 'manage_doctors.php';
        break;

    case 'patient':
        $backPage = 'manage_patients.php';
        break;
}

include '../includes/header.php';
?>

<h2>تعديل بيانات المستخدم</h2>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        تم تحديث البيانات بنجاح
    </div>
<?php endif; ?>

<form method="POST">
  <div class="mb-3">
    <label class="form-label">الاسم</label>
    <input 
      type="text" 
      name="name" 
      class="form-control" 
      value="<?= htmlspecialchars($user['name']); ?>" 
      required
    >
  </div>

  <div class="mb-3">
    <label class="form-label">البريد الإلكتروني</label>
    <input 
      type="email" 
      name="email" 
      class="form-control" 
      value="<?= htmlspecialchars($user['email']); ?>" 
      required
    >
  </div>

  <button type="submit" class="btn btn-primary">حفظ التغييرات</button>

  <a href="<?= $backPage ?>" class="btn btn-secondary">
    رجوع
  </a>
</form>

<?php include '../includes/footer.php'; ?>