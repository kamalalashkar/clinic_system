<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';
?>

<h2>مرحبًا، <?= htmlspecialchars($_SESSION['user']['name']); ?> - لوحة تحكم الإداري</h2>

<h3>إحصائيات النظام</h3>
<div class="row">

    <!-- عدد المرضى -->
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">عدد المرضى</div>
            <div class="card-body">
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'patient'");
                $patient_count = $stmt->fetchColumn();
                ?>
                <h4 class="card-title"><?= $patient_count ?></h4>
            </div>
        </div>
    </div>

    <!-- عدد الأطباء -->
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">عدد الأطباء</div>
            <div class="card-body">
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'doctor'");
                $doctor_count = $stmt->fetchColumn();
                ?>
                <h4 class="card-title"><?= $doctor_count ?></h4>
            </div>
        </div>
    </div>

    <!-- عدد المواعيد -->
    <div class="col-md-4">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">عدد المواعيد</div>
            <div class="card-body">
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) FROM appointments");
                $appointments_count = $stmt->fetchColumn();
                ?>
                <h4 class="card-title"><?= $appointments_count ?></h4>
            </div>
        </div>
    </div>

</div>

<h3 class="mt-4">إدارة النظام</h3>
<div class="list-group">
    <a href="admin_profile.php" class="list-group-item list-group-item-action">الملف الشخصي</a>
    <a href="manage_patients.php" class="list-group-item list-group-item-action">إدارة المرضى</a>
    <a href="manage_doctors.php" class="list-group-item list-group-item-action">إدارة الأطباء</a>
    <a href="manage_admins.php" class="list-group-item list-group-item-action">إدارة الإداريين</a>
    <a href="appointments_overview.php" class="list-group-item list-group-item-action">عرض المواعيد</a>
    <a href="logs.php" class="list-group-item list-group-item-action">سجلات الدخول</a>
</div>

<?php include '../includes/footer.php'; ?>