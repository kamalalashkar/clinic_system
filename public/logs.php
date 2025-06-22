<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

include '../includes/header.php';
?>


<div class="alert alert-info text-center">
    🚧 سيتم تفعيل هذه الصفحة قريبًا 🚧 
</div>

<?php include '../includes/footer.php'; ?>
