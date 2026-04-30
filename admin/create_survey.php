<?php
require_once '../config/config.php';
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$survey_id = $_GET['id'] ?? null;
$survey = null;
$questions = [];
$options = [];

if ($survey_id) {
    $survey = $conn->query("SELECT * FROM ".$table_prefix."surveys WHERE id = $survey_id")->fetch_assoc();
    $questions = $conn->query("SELECT * FROM ".$table_prefix."questions WHERE survey_id = $survey_id");
    while ($q = $questions->fetch_assoc()) {
        $options[$q['id']] = $conn->query("SELECT * FROM ".$table_prefix."options WHERE question_id = {$q['id']}");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $link = guidv4();

    if ($survey_id) {
        $conn->query("UPDATE ".$table_prefix."surveys SET title = '$title', description = '$description' WHERE id = $survey_id");
    } else {
        $conn->query("INSERT INTO ".$table_prefix."surveys (title, description, link) VALUES ('$title', '$description', '$link')");
        $survey_id = $conn->insert_id;
    }

    // Fragen verarbeiten
    foreach ($_POST['questions'] as $i => $question_text) {
        if (empty($question_text)) continue;

        // Prüfen, ob die Frage bereits existiert
        $question_id = null;
        if (isset($_POST['question_ids'][$i]) && !empty($_POST['question_ids'][$i])) {
            $question_id = (int)$_POST['question_ids'][$i];
            $conn->query("UPDATE ".$table_prefix."questions SET question_text = '$question_text' WHERE id = $question_id");
        } else {
            $conn->query("INSERT INTO ".$table_prefix."questions (survey_id, question_text) VALUES ($survey_id, '$question_text')");
            $question_id = $conn->insert_id;
        }

        // Bestehende Optionen für diese Frage abfragen
        $existing_options = $conn->query("SELECT id FROM ".$table_prefix."options WHERE question_id = $question_id");
        $existing_option_ids = [];
        while ($opt = $existing_options->fetch_assoc()) {
            $existing_option_ids[] = $opt['id'];
        }

        // Neue Optionen aus dem Formular verarbeiten
        if (isset($_POST['options'][$i]['text'])) {
            foreach ($_POST['options'][$i]['text'] as $j => $option_text) {
                if (empty($option_text)) continue;

                $desired_count = $_POST['options'][$i]['desired_count'][$j] ?? 0;
                $option_id = $_POST['option_ids'][$i][$j] ?? null;

                if ($option_id && in_array($option_id, $existing_option_ids)) {
                    // Bestehende Option aktualisieren
                    $conn->query("UPDATE ".$table_prefix."options SET option_text = '$option_text', desired_count = $desired_count WHERE id = $option_id");
                    // ID aus der Liste der bestehenden Optionen entfernen
                    $existing_option_ids = array_diff($existing_option_ids, [$option_id]);
                } else {
                    // Neue Option einfügen
                    $conn->query("INSERT INTO ".$table_prefix."options (question_id, option_text, desired_count) VALUES ($question_id, '$option_text', $desired_count)");
                }
            }
        }

        // Nicht mehr vorhandene Optionen löschen
        foreach ($existing_option_ids as $option_id) {
            $conn->query("DELETE FROM ".$table_prefix."options WHERE id = $option_id");
        }
    }

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $survey_id ? 'Umfrage bearbeiten' : 'Neue Umfrage erstellen' ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script>
    let questionCount = <?= isset($survey_id) ? $questions->num_rows : 0 ?>;

    function addQuestion() {
        const container = document.getElementById('questions-container');
        const questionIndex = questionCount++;

        const questionDiv = document.createElement('div');
        questionDiv.className = 'question';

        questionDiv.innerHTML = `
            <label>Frage:</label>
            <input type="text" name="questions[]" required>
            <div class="options-container">
                <div class="option">
                    <label>Antwortmöglichkeit:</label>
                    <input type="text" name="options[${questionIndex}][text][]" required>
                    <label>Gewünschte Anzahl:</label>
                    <input type="number" name="options[${questionIndex}][desired_count][]" min="0" value="0">
                    <button type="button" class="button button-secondary" onclick="this.parentElement.remove()">Option entfernen</button>
                </div>
            </div>
            <button type="button" class="button" onclick="addOption(this, ${questionIndex})">+ Option hinzufügen</button>
        `;

        container.appendChild(questionDiv);
    }

    function addOption(button, questionIndex) {
        const container = button.parentElement.querySelector('.options-container');

        const optionDiv = document.createElement('div');
        optionDiv.className = 'option';

        optionDiv.innerHTML = `
            <label>Antwortmöglichkeit:</label>
            <input type="text" name="options[${questionIndex}][text][]" required>
            <label>Gewünschte Anzahl:</label>
            <input type="number" name="options[${questionIndex}][desired_count][]" min="0" value="0">
            <button type="button" class="button button-secondary" onclick="this.parentElement.remove()">Option entfernen</button>
        `;

        container.appendChild(optionDiv);
    }
    </script>
</head>
<body>
    <div class="container">
        <div class="logo-bookmark">
            <img src="../assets/images/logo.svg" alt="Logo">
        </div>
        <h1><?= $survey_id ? 'Umfrage bearbeiten' : 'Neue Umfrage erstellen' ?></h1>
        <form method="post">
            <label>Titel:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($survey['title'] ?? '') ?>" required>

            <label>Beschreibung:</label>
            <textarea name="description" required><?= htmlspecialchars($survey['description'] ?? '') ?></textarea>

            <div id="questions-container">
                <?php if ($survey_id): ?>
                    <?php
                    $questions = $conn->query("SELECT * FROM ".$table_prefix."questions WHERE survey_id = $survey_id");
                    $i = 0;
                    while ($q = $questions->fetch_assoc()):
                        $options = $conn->query("SELECT * FROM ".$table_prefix."options WHERE question_id = {$q['id']}"); ?>
                        <div class="question">
                            <label>Frage:</label>
                            <input type="hidden" name="question_ids[<?= $i ?>]" value="<?= $q['id'] ?>">
                            <input type="text" name="questions[]" value="<?= htmlspecialchars($q['question_text']) ?>" required>

                            <div class="options-container">
                                <?php $j = 0; while ($o = $options->fetch_assoc()): ?>
                                    <div class="option">
                                        <label>Antwortmöglichkeit:</label>
                                        <input type="hidden" name="option_ids[<?= $i ?>][<?= $j ?>]" value="<?= $o['id'] ?>">
                                        <input type="text" name="options[<?= $i ?>][text][]" value="<?= htmlspecialchars($o['option_text']) ?>" required>
                                        <label>Gewünschte Anzahl:</label>
                                        <input type="number" name="options[<?= $i ?>][desired_count][]" min="0" value="<?= $o['desired_count'] ?>">
                                        <button type="button" class="button button-secondary" onclick="this.parentElement.remove()">Option entfernen</button>
                                    </div>
                                <?php $j++; endwhile; ?>
                            </div>
                            <button type="button" class="button" onclick="addOption(this, <?= $i ?>)">+ Option hinzufügen</button>
                        </div>
                    <?php $i++; endwhile; ?>
                <?php else: ?>
                    <div class="question">
                        <label>Frage:</label>
                        <input type="text" name="questions[]" placeholder="Frage" required>

                        <div class="options-container">
                            <div class="option">
                                <label>Antwortmöglichkeit:</label>
                                <input type="text" name="options[0][text][]" required>
                                <label>Gewünschte Anzahl:</label>
                                <input type="number" name="options[0][desired_count][]" min="0" value="0">
                                <button type="button" class="button button-secondary" onclick="this.parentElement.remove()">x</button>
                            </div>
                        </div>
                        <button type="button" class="button" onclick="addOption(this, 0)">+ Option hinzufügen</button>
                    </div>
                <?php endif; ?>
            </div>

            <p>
                <button type="button" onclick="addQuestion()" class="button">+ Frage hinzufügen</button>
            </p>
            <p class="submit-area">
                <button type="submit" class="button button-secondary">Speichern</button>
                <a href="dashboard.php"><button type="button" class="button">Abbrechen</button></a>
            </p>
        </form>
    </div>
</body>
</html>