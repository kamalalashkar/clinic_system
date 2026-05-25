<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

include '../includes/header.php';

$result = "";
$doctors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symptoms = trim($_POST['symptoms']);

    if (!empty($symptoms)) {
        $stmt = $pdo->query("SELECT * FROM symptom_specialty_rules");
        $rules = $stmt->fetchAll();

        foreach ($rules as $rule) {
            if (mb_stripos($symptoms, $rule['keyword']) !== false) {
                $result = $rule['specialty'];
                break;
            }
        }

        if (!empty($result)) {
            $stmt = $pdo->prepare("
                SELECT u.id, u.name, d.specialty, d.consultation_fee
                FROM users u
                JOIN doctors d ON u.id = d.user_id
                WHERE d.specialty LIKE ?
            ");
            $stmt->execute(["%$result%"]);
            $doctors = $stmt->fetchAll();
        } else {
            $result = "لم يتم العثور على تخصص مناسب. يرجى مراجعة الإداري.";
        }
    }
}
?>

<h2>اقتراح التخصص حسب الأعراض</h2>

<form method="POST" class="mb-4">
    <div class="mb-3">
        <label class="form-label">اكتب الأعراض التي تعاني منها</label>
        <textarea name="symptoms" class="form-control" rows="4" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">اقتراح التخصص</button>
</form>

<?php if (!empty($result)): ?>
    <div class="alert alert-info">
        <strong>التخصص المقترح:</strong>
        <?= htmlspecialchars($result); ?>
    </div>
<?php endif; ?>

<?php if (!empty($doctors)): ?>
    <h4>الأطباء المناسبون:</h4>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>اسم الطبيب</th>
                <th>التخصص</th>
                <th>أتعاب الكشف</th>
                <th>الإجراء</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($doctors as $doctor): ?>
                <tr>
                    <td><?= htmlspecialchars($doctor['name']); ?></td>
                    <td><?= htmlspecialchars($doctor['specialty']); ?></td>
                    <td><?= htmlspecialchars($doctor['consultation_fee']); ?></td>
                    <td>
                        <a href="book_appointment.php?doctor_id=<?= $doctor['id']; ?>" class="btn btn-success btn-sm">
                            حجز موعد
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>