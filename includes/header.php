<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة العيادات</title>
    
    <!-- إضافة ملف CSS لـ Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- إضافة أي CSS إضافي هنا -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- تخصيص الواجهة على الموبايل -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- شريط التنقل (Navbar) -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="index.php">نظام العيادة</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user'])): ?>
          <!-- إذا كان المستخدم مسجل دخول، عرض خيارات الخروج -->
          <li class="nav-item">
            <a class="nav-link" href="logout.php">تسجيل الخروج</a>
          </li>
          <li class="nav-item">
            <span class="nav-link">مرحبًا، <?= htmlspecialchars($_SESSION['user']['name']); ?></span>
          </li>
        <?php else: ?>
          <!-- إذا لم يكن المستخدم مسجل دخول، عرض روابط التسجيل والدخول -->
          <li class="nav-item">
            <a class="nav-link" href="login.php">تسجيل الدخول</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="register.php">إنشاء حساب</a>
          </li>
        <?php endif; ?>

        <!-- إذا كان المستخدم إداريًا -->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" href="admin_dashboard.php">لوحة التحكم</a>
          </li>
        <?php endif; ?>

        <!-- إذا كان المستخدم مريضًا -->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'patient'): ?>
          <li class="nav-item">
            <a class="nav-link" href="patient_dashboard.php">لوحة مريض</a>
          </li>
        <?php endif; ?>

        <!-- إذا كان المستخدم طبيبًا -->
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'doctor'): ?>
          <li class="nav-item">
            <a class="nav-link" href="doctor_dashboard.php">لوحة طبيب</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- بداية محتوى الصفحة -->
<div class="container mt-4">
