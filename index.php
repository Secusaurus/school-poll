<?php
require_once 'config/config.php';

$link = $_GET['id'] ?? '';
$survey = $conn->query("SELECT * FROM ".$table_prefix."surveys WHERE link = '$link'")->fetch_assoc();

if (!$survey) {
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($survey['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo-bookmark">
            <img src="assets/images/logo.svg" alt="Logo">
        </div>
        <h1>Keine Umfrage gefunden</h1>
        <p>
            Eventuell ist die URL falsch, die Umfrage abgelaufen oder du wolltest dich sowieso als Admin anmelden?
        </p>
        <p class="submit-area">
            <a href="admin/dashboard.php"><button type="button" class="button button-secondary">Als Admin anmelden</button></a>
        </p>
    </div>
</body>
<?php
    exit;
}

// **Fragen abfragen und in ein Array speichern**
$questions = $conn->query("SELECT * FROM ".$table_prefix."questions WHERE survey_id = {$survey['id']}");
$question_list = [];
while ($q = $questions->fetch_assoc()) {
    $question_list[] = $q;
}

// **Optionen für jede Frage abfragen**
$options = [];
foreach ($question_list as $q) {
    $options[$q['id']] = $conn->query("SELECT * FROM ".$table_prefix."options WHERE question_id = {$q['id']}");
}

// Klassenstatistiken abfragen
$class_stats = $conn->query("
    SELECT class, COUNT(*) as count
    FROM ".$table_prefix."responses
    WHERE survey_id = {$survey['id']}
    GROUP BY class
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $name = $_POST['name'];
    $child_name = $_POST['child_name'] ?? null;
    $child_class = $_POST['child_class'] ?? null;

    print ("INSERT INTO ".$table_prefix."responses (survey_id, email, name, child_name, class) VALUES ({$survey['id']}, '$email', '$name', '$child_name', '$child_class')");

    // Antworten speichern
    $conn->query("INSERT INTO ".$table_prefix."responses (survey_id, email, name, child_name, class) VALUES ({$survey['id']}, '$email', '$name', '$child_name', '$child_class')");
    $response_id = $conn->insert_id;

    foreach ($_POST['answers'] as $question_id => $option_ids) {
        foreach ($option_ids as $option_id) {
            $conn->query("INSERT INTO ".$table_prefix."response_options (response_id, option_id) VALUES ($response_id, $option_id)");
        }
    }

    header("Location: thanks.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($survey['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo-bookmark">
            <img src="assets/images/logo.svg" alt="Logo">
        </div>
        <h1><?= htmlspecialchars($survey['title']) ?></h1>
        <p class="description"><?= htmlspecialchars($survey['description']) ?></p>

        <!-- Klassenstatistiken anzeigen -->
        <?php if ($class_stats->num_rows > 0): ?>
            <div class="class-stats">
                <ul>
                    <?php while ($stat = $class_stats->fetch_assoc()): ?>
                        <?php if (trim($stat['class']) != ''): ?>
                            <li><?= $stat['count'] ?> <?php if ($stat['count'] != 1) { print "Personen"; } else { print "Person"; } ?>
                            aus Klasse <?= htmlspecialchars($stat['class']) ?>
                            <?php if ($stat['count'] != 1) { print "haben"; } else { print "hat"; } ?> sich beteiligt</li>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="yourinfo">
                <h3>Informationen zu dir</h3>

                <label>Dein Name:</label>
                <input type="text" name="name" required>

                <label>E-Mail oder Telefonnummer:</label>
                <input type="text" name="email" placeholder="ggf. zur Kontaktaufnahme" required>

                <label>Name des Kindes (optional):</label>
                <input type="text" name="child_name" placeholder="zur besseren Zuordnung">

                <label>Schulklasse (optional):</label>
                <input type="text" name="child_class" placeholder="z.B. 3a">
            </div>

            <!-- Fragen und Optionen anzeigen -->
            <?php foreach ($question_list as $q): ?>
                <div class="question">
                    <h3><?= htmlspecialchars($q['question_text']) ?></h3>
                    <?php
                    // Optionen für diese Frage abfragen
                    $option_result = $conn->query("SELECT * FROM ".$table_prefix."options WHERE question_id = {$q['id']}");
                    while ($o = $option_result->fetch_assoc()):
                    ?>
                        <label>
                            <input type="checkbox" name="answers[<?= $q['id'] ?>][]" value="<?= $o['id'] ?>">
                            <?= htmlspecialchars($o['option_text']) ?>
                            <?php
                            $count = $conn->query("SELECT COUNT(*) FROM ".$table_prefix."response_options WHERE option_id = {$o['id']}")->fetch_row()[0];
                            $remaining = max(0, $o['desired_count'] - $count);
                            if ($o['desired_count'] > 0 && $remaining > 0) {
                                echo " (noch mindestens $remaining benötigt)";
                            }
                            ?>
                        </label>
                    <?php endwhile; ?>
                </div>
            <?php endforeach; ?>

            <p class="submit-area">
                <button type="submit" class="button">Absenden</button>
            </p>
        </form>

        <p class="dsgvo-link">
            <a href="dsgvo.php" class="label">Datenschutzerklärung</a>
        </p>
    </div>
</body>
</html>