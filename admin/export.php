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

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="umfrage_' . $survey['id'] . '_rueckmeldungen.csv"');

$output = fopen('php://output', 'w');
// CSV-Header
fputcsv($output, ['Frage', 'Antwort', 'Name', 'E-Mail', 'Kindername', 'Schulklasse']);

$questions = $conn->query("SELECT * FROM ".$table_prefix."questions WHERE survey_id = $survey_id");
while ($question = $questions->fetch_assoc()) {
    $options = $conn->query("SELECT * FROM ".$table_prefix."options WHERE question_id = {$question['id']}");
    while ($option = $options->fetch_assoc()) {
        $responses = $conn->query("
            SELECT r.name, r.email, r.child_name, r.class
            FROM ".$table_prefix."responses r
            JOIN ".$table_prefix."response_options ro ON r.id = ro.response_id
            WHERE ro.option_id = {$option['id']}
        ");
        while ($response = $responses->fetch_assoc()) {
            fputcsv($output, [
                $question['question_text'],
                $option['option_text'],
                $response['name'],
                $response['email'],
                $response['child_name'] ?? '',
                $response['class'] ?? ''
            ]);
        }
    }
}

fclose($output);
exit;