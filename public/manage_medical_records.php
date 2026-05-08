<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkDoctor();

include '../includes/header.php';

$doctor_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("
  SELECT mr.*, u.name AS patient_name 
  FROM medical_records mr
  JOIN users u ON mr.patient_id = u.id
  WHERE mr.doctor_id = ?
  ORDER BY mr.created_at DESC
");
$stmt->execute([$doctor_id]);
$records = $stmt->fetchAll();
?>

<h2>إدارة السجلات الطبية</h2>

<a href="add_medical_record.php" class="btn btn-success mb-3">➕ إضافة سجل طبي جديد</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>رقم السجل</th>
      <th>اسم المريض</th>
      <th>التشخيص</th>
      <th>العلاج</th>
      <th>تاريخ الإنشاء</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($records): ?>
      <?php foreach ($records as $record): ?>
        <tr>
          <td><?= $record['id']; ?></td>
          <td><?= htmlspecialchars($record['patient_name']); ?></td>
          <td><?= nl2br(htmlspecialchars($record['diagnosis'])); ?></td>
          <td><?= nl2br(htmlspecialchars($record['treatment'])); ?></td>
          <td><?= $record['created_at']; ?></td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="6">🚫 لا توجد سجلات طبية حتى الآن.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php include '../includes/footer.php'; ?>
