<?php
session_start();

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

include '../includes/header.php';
?>


<div class="text-center">
    <h1 class="mb-4">مرحبًا بك في نظام إدارة العيادات الذكي</h1>
    <p class="lead mb-4">
        يوفر هذا النظام خدمات متكاملة لإدارة مواعيدك وسجلاتك الطبية بكل سهولة وأمان.
    </p>

    <div class="d-grid gap-2 col-6 mx-auto">
        <a href="login.php" class="btn btn-primary btn-lg">تسجيل الدخول</a>
        <a href="register.php" class="btn btn-outline-primary btn-lg">إنشاء حساب جديد</a>
    </div>
</div>

<div class="mt-5 text-center">
    <h3>مميزات النظام</h3>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">حجز المواعيد مع الأطباء بسهولة</li>
        <li class="list-group-item">إدارة السجلات الطبية والوصفات</li>
        <li class="list-group-item">استشارات طبية عن بُعد</li>
        <li class="list-group-item">إشعارات ومتابعة مستمرة</li>
    </ul>
</div>

<?php include '../includes/footer.php'; ?>
