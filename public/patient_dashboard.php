<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkPatient();

include '../includes/header.php';
?>

<h2>مرحبًا بك، <?= htmlspecialchars($_SESSION['user']['name']); ?></h2>
<p>هذه لوحة تحكم المريض. اختر ما ترغب في القيام به من الخيارات التالية:</p>

<div class="list-group">
    <a href="patient_profile.php" class="list-group-item list-group-item-action">
    الملف الشخصي
</a>
<a href="search_doctors.php" class="list-group-item list-group-item-action">
    البحث عن طبيب
</a>
    <a href="book_appointment.php" class="list-group-item list-group-item-action">حجز موعد</a>
    <a href="view_calendar.php" class="list-group-item list-group-item-action">عرض مواعيدي</a>
    <a href="medical_record.php" class="list-group-item list-group-item-action">عرض سجلي الطبي</a>
    <a href="prescriptions.php" class="list-group-item list-group-item-action">عرض وصفاتي</a>
    <a href="suggest_specialty.php" class="list-group-item list-group-item-action">
    اقتراح التخصص حسب الأعراض  </a>
    <a href="consultation.php" class="list-group-item list-group-item-action">الاستشارات عن بعد</a>
</div>

<?php include '../includes/footer.php'; ?>
