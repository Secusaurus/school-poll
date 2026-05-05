<?php
require_once '../config/config.php';
session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Löschfunktion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $conn->query("DELETE FROM ".$table_prefix."surveys WHERE id = $delete_id");
    header("Location: dashboard.php");
    exit;
}


$surveys = $conn->query("
    SELECT s.*, COUNT(r.id) AS answercount
    FROM ".$table_prefix."surveys s
    LEFT JOIN ".$table_prefix."responses r ON s.id = r.survey_id
    GROUP BY s.id
    ORDER BY s.created_at DESC
");

// Funktion zum Generieren eines QR-Codes als Data-URL
function generateQRCode($url) {
    $size = 200;
    $qrCodeUrl = "https://qrcode.tec-it.com/API/QRCode?data=" . urlencode($url);
    return $qrCodeUrl;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin-Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo-bookmark">
            <img src="../assets/images/logo.svg" alt="Logo">
        </div>
        <h1>Umfragen-Verwaltung</h1>
        <a href="create_survey.php" class="button">Neue Umfrage erstellen</a>
        <table>
            <tr>
                <th>Titel</th>
                <th>Beschreibung</th>
                <th>Rückmeldungen</th>
                <th>Link</th>
                <th>Aktionen</th>
            </tr>
            <?php while ($survey = $surveys->fetch_assoc()): ?>
                <?php
                $surveyUrl = "https://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/?id=" . urlencode($survey['link']);
                $qrCodeUrl = generateQRCode($surveyUrl);
                ?>
            <tr>
                <td><strong><?= htmlspecialchars($survey['title']) ?></strong></td>
                <td><p class="description"><?= trim(htmlspecialchars($survey['description'])) ?></p></td>
                <td class="text-center">
                    <a href="view_responses.php?id=<?= $survey['id'] ?>" class="button">Anzeigen (<?= $survey['answercount'] ?> Stück)</a>
                </td>
                <td>
                    <a href="../?id=<?= urlencode($survey['link']) ?>" target="_blank" class="button">Öffnen</a><br />
                    <a href="<?= $qrCodeUrl ?>" target="_blank" class="button">QR-Code</a>
                </td>
                <td>
                    <a href="create_survey.php?id=<?= $survey['id'] ?>" class="button">Bearbeiten</a>
                    <a href="?delete=<?= $survey['id'] ?>" class="button button-secondary" onclick="return confirm('Möchtest du diese Umfrage wirklich löschen?')">Löschen</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>

        
        <div class="submit-area">
            <a href="?logout=1" class="button button-secondary">Abmelden</a>
        </div>
    </div>
</body>
</html>