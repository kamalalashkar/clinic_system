<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkDoctor();

include '../includes/header.php';

$suggestion = "";
$matched_keyword = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symptoms = trim($_POST['symptoms']);

    if (!empty($symptoms)) {
        $stmt = $pdo->query("SELECT * FROM diagnosis_rules");
        $rules = $stmt->fetchAll();

        foreach ($rules as $rule) {
            if (mb_stripos($symptoms, $rule['keyword']) !== false) {
                $suggestion = $rule['suggested_diagnosis'];
                $matched_keyword = $rule['keyword'];
                break;
            }
        }

        if (empty($suggestion)) {
            $suggestion = "لم يتم العثور على تشخيص مبدئي مناسب.";
        }
    }
}
?>

<h2>اقتراح تشخيص أولي</h2>

<p class="text-muted">
هذه الميزة تساعد الطبيب على اقتراح تشخيص مبدئي بناءً على الأعراض المدخلة.
</p>

<form method="POST" class="mb-4">
    <div class="mb-3">
        <label class="form-label">أدخل أعراض المريض</label>
        <textarea name="symptoms" class="form-control" rows="4" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary">
        اقتراح التشخيص
    </button>
</form>

<?php if (!empty($suggestion)): ?>
    <div class="alert alert-info">
        <?php if (!empty($matched_keyword)): ?>
            <p><strong>الكلمة المطابقة:</strong> <?= htmlspecialchars($matched_keyword); ?></p>
        <?php endif; ?>

        <p><strong>التشخيص المبدئي المقترح:</strong> <?= htmlspecialchars($suggestion); ?></p>
    </div>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>