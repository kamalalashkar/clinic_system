<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

// التحقق من أن المستخدم هو طبيب
checkDoctor();

include '../includes/header.php';

// معالجة إصدار الوصفة
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_SESSION['user']['id'];
    $content = $_POST['content'];

    // إضافة الوصفة الطبية
    $stmt = $pdo->prepare("INSERT INTO prescriptions (record_id, doctor_id, patient_id, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([NULL, $doctor_id, $patient_id, $content]);

    header("Location: doctor_dashboard.php");
    exit();
}
?>

<h2>إصدار وصفة طبية</h2>

<form method="POST">
  <div class="mb-3">
    <label for="patient_id" class="form-label">اختر المريض</label>
    <select name="patient_id" id="patient_id" class="form-select" required>
      <?php
      $stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'patient'");
      $patients = $stmt->fetchAll();
      foreach ($patients as $patient): ?>
        <option value="<?= $patient['id']; ?>"><?= htmlspecialchars($patient['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label for="content" class="form-label">محتوى الوصفة</label>
    <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
  </div>
  
  <button type="submit" class="btn btn-primary">إصدار الوصفة</button>
</form>

<?php include '../includes/footer.php'; ?>
