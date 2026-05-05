<?php
require_once '../config/config.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$survey_id = $_GET['id'] ?? null;
if (!$survey_id) {
    die("Keine Umfrage ausgewählt.");
}

$survey = $conn->query("SELECT * FROM ".$table_prefix."surveys WHERE id = $survey_id")->fetch_assoc();
if (!$survey) {
    die("Umfrage nicht gefunden.");
}

$class_stats = $conn->query("
    SELECT class, COUNT(*) as count
    FROM ".$table_prefix."responses
    WHERE survey_id = {$survey['id']}
    GROUP BY class
");


$lines = [];

$questions = $conn->query("SELECT * FROM ".$table_prefix."questions WHERE survey_id = $survey_id ORDER BY id");
$questions_list = [];
while ($question = $questions->fetch_assoc()) {
    $questions_list[] = $question;
    $options = $conn->query("SELECT * FROM ".$table_prefix."options WHERE question_id = {$question['id']} ORDER BY id");
    while ($option = $options->fetch_assoc()) {
        $responses = $conn->query("
            SELECT r.name, r.email, r.child_name, r.class
            FROM ".$table_prefix."responses r
            JOIN ".$table_prefix."response_options ro ON r.id = ro.response_id
            WHERE ro.option_id = {$option['id']}
            ORDER BY r.name ASC
        ");
        while ($response = $responses->fetch_assoc()) {
            $lines[] = [
                "question" => $question['question_text'],
                "answer" => $option['option_text'],
                "name" => $response['name'],
                "contact" => $response['email'],
                "child" => $response['child_name'] ?? '',
                "class" => $response['class'] ?? ''
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rückmeldungen für <?= htmlspecialchars($survey['title']) ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="logo-bookmark">
            <img src="../assets/images/logo.svg" alt="Logo">
        </div>
        <h1>Rückmeldungen: <?= htmlspecialchars($survey['title']) ?></h1>
        <p class="description"><?= htmlspecialchars($survey['description']) ?></p>
        

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

        <div class="stats-container">
            <h2>Antwortstatistik</h2>
            <?php foreach ($questions_list as $question): ?>
                <h3><?= htmlspecialchars($question['question_text']) ?></h3>
                <?php
                // Optionen für diese Frage abfragen
                $options = $conn->query("
                    SELECT
                        o.option_text,
                        o.desired_count,
                        COUNT(ro.id) AS answer_count
                    FROM ".$table_prefix."options o
                    LEFT JOIN ".$table_prefix."response_options ro ON o.id = ro.option_id
                    WHERE o.question_id = {$question['id']}
                    GROUP BY o.id
                ");
                ?>
                <div class="stat-segment">
                    <?php while ($option = $options->fetch_assoc()): ?>
                        <div class="stat-item">
                            <div class="stat-label">
                                <?= htmlspecialchars($option['option_text']) ?>:
                                <?php
                                    print($option['answer_count']) . " " . ($option['answer_count'] != 1 ? "Antworten" : "Antwort");
                                ?>
                                <?php if ($option['desired_count'] > 0): ?>
                                    (Erforderlich: <?= $option['desired_count'] ?>)
                                <?php endif; ?>
                            </div>
                            <?php if ($option['desired_count'] > 0): ?>
                                <div class="progress-container">
                                    <div class="progress-bar"
                                        style="width: <?= min(100, ($option['answer_count'] / $option['desired_count']) * 100) ?>%">
                                    </div>
                                    <span class="progress-text">
                                        <?= round(($option['answer_count'] / $option['desired_count']) * 100, 1) ?>%
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endforeach; ?>

            <hr />
        
            <h2>Einzelne Antworten</h2>

            <table>
                <thead>
                    <tr>
                        <th>Frage</th>
                        <th>Antwort</th>
                        <th>Name</th>
                        <th>Kontakt</th>
                        <th>Kindername</th>
                        <th>Schulklasse</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($lines as $response)
                    {
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($response['question']) ?></td>
                        <td><?= htmlspecialchars($response['answer']) ?></td>
                        <td><?= htmlspecialchars($response['name']) ?></td>
                        <td><?= htmlspecialchars($response['contact']) ?></td>
                        <td><?= htmlspecialchars($response['child'] ?? '') ?></td>
                        <td><?= htmlspecialchars($response['class'] ?? '') ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="submit-area">
            <a href="export.php?id=<?= $survey_id ?>" class="button">Als CSV exportieren</a>
            <a href="dashboard.php" class="button button-secondary">Zurück zum Dashboard</a>
        </div>
    </div>
</body>
</html>