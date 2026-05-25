<?php
session_start();

require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use ArPHP\I18N\Arabic;

checkPatient();

$patient_id = $_SESSION['user']['id'];
$prescription_id = $_GET['id'] ?? null;

$stmt = $pdo->prepare("
    SELECT p.*, u.name AS doctor_name
    FROM prescriptions p
    JOIN users u ON p.doctor_id = u.id
    WHERE p.id = ? AND p.patient_id = ?
");

$stmt->execute([$prescription_id, $patient_id]);
$prescription = $stmt->fetch();

if (!$prescription) {
    die('Prescription not found');
}

$Arabic = new Arabic();

$title = $Arabic->utf8Glyphs('وصفة طبية');
$detailsLabel = $Arabic->utf8Glyphs('التفاصيل :');

$doctorLabel = 'Doctor';
$dateLabel = 'Date';

$doctorName = htmlspecialchars($prescription['doctor_name']);
$content = $Arabic->utf8Glyphs(htmlspecialchars($prescription['content']));
$date = htmlspecialchars($prescription['created_at']);

$html = '
<html>
<head>
<meta charset="UTF-8">
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    padding: 20px;
}

h2 {
    text-align: center;
    margin-bottom: 30px;
}

.container {
    direction: rtl;
    text-align: right;
    border: 1px solid #ccc;
    padding: 20px;
}

.info-row {
    display: table;
    width: 100%;
    margin-bottom: 12px;
}

.label {
    display: table-cell;
    width: 18%;
    text-align: right;
    font-weight: bold;
}

.value {
    display: table-cell;
    width: 82%;
    text-align: left;
}

.details-title {
    font-weight: bold;
    margin-top: 20px;
    margin-bottom: 10px;
}

.details-content {
    border-top: 1px solid #eee;
    padding-top: 10px;
    line-height: 1.8;
}
</style>
</head>

<body>

<h2>' . $title . '</h2>

<div class="container">

    <div class="info-row">
        <span class="label">' . $doctorLabel . ':</span>
        <span class="value">' . $doctorName . '</span>
    </div>

    <div class="info-row">
        <span class="label">' . $dateLabel . ':</span>
        <span class="value">' . $date . '</span>
    </div>

    <div class="details-title">' . $detailsLabel . '</div>

    <div class="details-content">
        ' . nl2br($content) . '
    </div>

</div>

</body>
</html>
';

$dompdf = new Dompdf();

$dompdf->loadHtml($html, 'UTF-8');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream('prescription.pdf', ['Attachment' => true]);

exit;
?>