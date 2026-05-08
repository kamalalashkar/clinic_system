<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT 
        u.id,
        u.name,
        u.email,
        u.phone,
        d.specialty,
        d.consultation_fee
    FROM users u
    JOIN doctors d ON u.id = d.user_id
    WHERE u.role = 'doctor'
";

$params = [];

if (!empty($search)) {
    $sql .= " AND (u.name LIKE ? OR d.specialty LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY u.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<h2 class="mb-4">البحث عن طبيب</h2>

<form method="GET" class="mb-4">
    <div class="input-group">
        <input type="text" name="search" class="form-control"
               placeholder="ابحث باسم الطبيب أو التخصص"
               value="<?= htmlspecialchars($search) ?>">

        <button type="submit" class="btn btn-primary">
            بحث
        </button>
    </div>
</form>

<?php if (count($doctors) > 0): ?>
    <div class="row">
        <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        د. <?= htmlspecialchars($doctor['name']) ?>
                    </div>

                    <div class="card-body">
                        <p>
                            <strong>التخصص:</strong>
                            <?= htmlspecialchars($doctor['specialty']) ?>
                        </p>

                        <p>
                            <strong>رسوم الاستشارة:</strong>
                            <?= htmlspecialchars($doctor['consultation_fee']) ?> ل س
                        </p>

                        <a href="doctor_details.php?id=<?= $doctor['id'] ?>" class="btn btn-info">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        لا يوجد أطباء مطابقون للبحث.
    </div>
<?php endif; ?>

<a href="patient_dashboard.php" class="btn btn-secondary mt-3">العودة للوحة التحكم</a>

<?php include '../includes/footer.php'; ?>