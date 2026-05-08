<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkDoctor();

$doctor_id = $_SESSION['user']['id'];
$errors = [];
$success = "";

$stmt = $pdo->query("SELECT id, name FROM users WHERE role = 'patient' ORDER BY name");
$patients = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = intval($_POST['patient_id']);
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);

    if (!$patient_id || empty($diagnosis)) {
        $errors[] = "جميع الحقول مطلوبة باستثناء العلاج (يمكن تركه فارغًا).";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'patient'");
        $stmt->execute([$patient_id]);
        if (!$stmt->fetch()) {
            $errors[] = "المريض غير صالح.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, diagnosis, treatment) VALUES (?, ?, ?, ?)");
            $stmt->execute([$patient_id, $doctor_id, $diagnosis, $treatment]);
            $success = "✅ تم إضافة السجل الطبي بنجاح!";
        }
    }
}

include '../includes/header.php';
?>

<h2>إضافة سجل طبي جديد</h2>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul>
      <?php foreach ($errors as $error): ?>
        <li><?= htmlspecialchars($error); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<?php if ($success): ?>
  <div class="alert alert-success"><?= $success; ?></div>
<?php endif; ?>

<form method="POST">
  <div class="mb-3">
    <label class="form-label">اختر المريض</label>
    <select name="patient_id" class="form-select" required>
      <option value="">-- اختر المريض --</option>
      <?php foreach ($patients as $p): ?>
        <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">التشخيص</label>
    <textarea name="diagnosis" class="form-control" rows="4" required></textarea>
  </div>

  <div class="mb-3">
    <label class="form-label">العلاج (اختياري)</label>
    <textarea name="treatment" class="form-control" rows="3"></textarea>
  </div>

  <button type="submit" class="btn btn-primary">💾 حفظ السجل</button>
  <a href="manage_medical_records.php" class="btn btn-secondary">🔙 العودة</a>
</form>

<?php include '../includes/footer.php'; ?>
