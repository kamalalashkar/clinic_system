<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

include '../includes/header.php';

// جلب قائمة الأطباء
$stmt = $pdo->query("
  SELECT u.id, u.name, d.specialty
  FROM users u 
  JOIN doctors d ON u.id = d.user_id
  WHERE u.role = 'doctor'
");
$doctors = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = intval($_POST['doctor_id']);
    $date = $_POST['appointment_date'];
    $patient_id = $_SESSION['user']['id'];

    // التحقق من الدقائق
    $minutes = date('i', strtotime($date));

    if ($minutes != '00' && $minutes != '30') {

        echo "<div class='alert alert-danger'>⚠️ يجب اختيار الدقائق 00 أو 30 فقط!</div>";

    } elseif (strtotime($date) < strtotime(date('Y-m-d H:i:s'))) {

        echo "<div class='alert alert-danger'>⚠️ لا يمكن حجز موعد في تاريخ سابق!</div>";

    } else {

        // تحقق من صحة الطبيب
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'doctor'");
        $stmt->execute([$doctor_id]);

        if (!$stmt->fetch()) {

            echo "<div class='alert alert-danger'>⚠️ طبيب غير صالح!</div>";

        } else {

            // تحقق هل الموعد محجوز مسبقًا
            $stmt = $pdo->prepare("
                SELECT id FROM appointments
                WHERE doctor_id = ? AND appointment_date = ?
            ");
            $stmt->execute([$doctor_id, $date]);

            if ($stmt->fetch()) {

                echo "<div class='alert alert-danger'>⚠️ هذا الموعد محجوز مسبقًا!</div>";

            } else {

                // احجز الموعد
                $stmt = $pdo->prepare("
                    INSERT INTO appointments 
                    (patient_id, doctor_id, appointment_date, status) 
                    VALUES (?, ?, ?, 'pending')
                ");
                $stmt->execute([$patient_id, $doctor_id, $date]);

                echo "<div class='alert alert-success'>✅ تم حجز الموعد بنجاح!</div>";
            }
        }
    }
}
?>

<h2>حجز موعد جديد</h2>

<form method="POST" class="mb-4">
  <div class="mb-3">
    <label class="form-label">اختر الطبيب</label>
    <select name="doctor_id" class="form-select" required>
      <option value="">-- اختر الطبيب --</option>
      <?php foreach ($doctors as $doc): ?>
        <option value="<?= $doc['id']; ?>">
          <?= htmlspecialchars($doc['name']); ?> (<?= htmlspecialchars($doc['specialty']); ?>)
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="mb-3">
    <label class="form-label">تاريخ الموعد</label>
    <input 
      type="datetime-local" 
      name="appointment_date" 
      class="form-control" 
      step="1800" 
      required
    >
  </div>

  <button type="submit" class="btn btn-primary">حجز الموعد</button>
</form>

<?php include '../includes/footer.php'; ?>
