<?php
session_start();
require_once '../config/database.php';
require_once '../config/auth.php';

// تحقق أن المستخدم هو مريض
checkPatient();

include '../includes/header.php';

$patient_id = $_SESSION['user']['id'];

// جلب جميع الوصفات الخاصة بالمريض مع اسم الطبيب
$stmt = $pdo->prepare("
    SELECT p.*, u.name AS doctor_name 
    FROM prescriptions p
    JOIN users u ON p.doctor_id = u.id
    WHERE p.patient_id = ?
    ORDER BY p.created_at DESC
");
$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll();
?>

<h2 class="mb-4 text-center">وصفاتي الطبية</h2>

<?php if ($prescriptions): ?>
    <div class="container">
        <?php foreach ($prescriptions as $prescription): ?>
            <div class="card shadow mb-4" id="prescription-<?= $prescription['id']; ?>">
                <div class="card-header bg-primary text-white">
                    وصفة طبية
                </div>

                <div class="card-body">
                    <p>
                        <strong>الطبيب:</strong>
                        د. <?= htmlspecialchars($prescription['doctor_name']); ?>
                    </p>

                    <p>
                        <strong>التاريخ:</strong>
                        <?= htmlspecialchars($prescription['created_at']); ?>
                    </p>

                    <hr>

                    <p><strong>تفاصيل الوصفة:</strong></p>

                    <div class="alert alert-light">
                        <?= nl2br(htmlspecialchars($prescription['content'])); ?>
                    </div>

                    <div class="d-flex gap-2">
                        <button 
                            onclick="printPrescription('prescription-<?= $prescription['id']; ?>')" 
                            class="btn btn-primary">
                            🖨 طباعة
                        </button>

                        <a 
                            href="download_prescription.php?id=<?= $prescription['id']; ?>" 
                            class="btn btn-success">
                            📄 تنزيل PDF
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info text-center">
        ليس لديك وصفات طبية مسجلة حتى الآن.
    </div>
<?php endif; ?>

<script>
function printPrescription(id) {
    const content = document.getElementById(id).innerHTML;

    const printWindow = window.open('', '', 'width=800,height=600');

    printWindow.document.write(`
        <html dir="rtl">
        <head>
            <title>طباعة الوصفة</title>
            <meta charset="UTF-8">
            <style>
                body {
                    font-family: Arial, sans-serif;
                    direction: rtl;
                    padding: 30px;
                }

                .btn,
                a {
                    display: none !important;
                }

                .card {
                    border: 1px solid #ccc;
                    padding: 20px;
                }

                .card-header {
                    font-size: 22px;
                    font-weight: bold;
                    margin-bottom: 20px;
                }

                .alert {
                    border: 1px solid #eee;
                    padding: 15px;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            ${content}
        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}
</script>

<?php include '../includes/footer.php'; ?>