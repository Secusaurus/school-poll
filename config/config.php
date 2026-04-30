<?php
// Hier bitte konfigurieren
$db_host = 'localhost';
$db_user = 'carlt';
$db_pass = 'toor';
$db_name = 'poll';

$table_prefix = 'poll_';


// Administrator-Passwort
$admin_password = password_hash('admin123', PASSWORD_DEFAULT);


// DSGVO-Informationen

$dsgvo_name = "Vorname Nachname";
$dsgvo_address = "X-Straße 1, 12345 Ort";
$dsgvo_mail = "email@demo.com";




// Ab hier nichts mehr anfassen!


// Verbindung herstellen
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}



// Globale Funktionen

function guidv4($data = null) {
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
?>