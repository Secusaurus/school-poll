<?php
require_once 'config.php';

$tables = [
    "surveys" => "CREATE TABLE {$table_prefix}surveys (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        link VARCHAR(255) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "questions" => "CREATE TABLE {$table_prefix}questions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        survey_id INT NOT NULL,
        question_text TEXT NOT NULL,
        FOREIGN KEY (survey_id) REFERENCES {$table_prefix}surveys(id) ON DELETE CASCADE
    )",

    "options" => "CREATE TABLE {$table_prefix}options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        question_id INT NOT NULL,
        option_text TEXT NOT NULL,
        desired_count INT NOT NULL DEFAULT 0,
        FOREIGN KEY (question_id) REFERENCES {$table_prefix}questions(id) ON DELETE CASCADE
    )",

    "responses" => "CREATE TABLE {$table_prefix}responses (
        id INT AUTO_INCREMENT PRIMARY KEY,
        survey_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        name VARCHAR(255) NOT NULL,
        child_name VARCHAR(255),
        class VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (survey_id) REFERENCES {$table_prefix}surveys(id) ON DELETE CASCADE
    )",

    "response_options" => "CREATE TABLE {$table_prefix}response_options (
        id INT AUTO_INCREMENT PRIMARY KEY,
        response_id INT NOT NULL,
        option_id INT NOT NULL,
        FOREIGN KEY (response_id) REFERENCES {$table_prefix}responses(id) ON DELETE CASCADE,
        FOREIGN KEY (option_id) REFERENCES {$table_prefix}options(id) ON DELETE CASCADE
    )"
];

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($tables as $tableName => $sql) {
        $prefixedTableName = $table_prefix . $tableName;
        $checkQuery = $conn->query("SHOW TABLES LIKE '$prefixedTableName'");
        if ($checkQuery->rowCount() > 0) {
            echo "Tabelle $prefixedTableName existiert bereits. Überspringe.<br>";
            continue;
        }

        $conn->exec($sql);
        echo "Tabelle $prefixedTableName erfolgreich angelegt.<br>";
    }

    echo "Installation abgeschlossen.";
} catch (PDOException $e) {
    echo "Fehler: " . $e->getMessage();
}
?>