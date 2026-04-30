<?php
require_once 'config/config.php';

?><!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Datenschutzerklärung</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container dsgvo-text">
        <div class="logo-bookmark">
            <img src="assets/images/logo.svg" alt="Logo">
        </div>
        <h1>Datenschutzerklärung</h1>
        <h2>1. Verantwortlicher</h2>
        <p>
            Verantwortlich für die Verarbeitung der personenbezogenen Daten im Rahmen dieser Web-App ist:
            <br><br>
            <?= $dsgvo_name ?><br>
            <?= $dsgvo_address ?><br>
            E-Mail: <?= $dsgvo_mail ?>
        </p>

        <h2>2. Erhobene Daten und Zweck der Verarbeitung</h2>
        <p>
            Die Web-App erfasst folgende personenbezogene Daten im Rahmen von Umfragen:
        </p>
        <p><strong>Pflichtangaben:</strong></p>
        <ul>
            <li>Name</li>
            <li>Kontaktdaten (E-Mail-Adresse und/oder Telefonnummer)</li>
        </ul>
        <p><strong>Optional:</strong></p>
        <ul>
            <li>Name des Kindes</li>
            <li>Schulklasse</li>
        </ul>
        <p>
            Diese Daten werden ausschließlich für die Durchführung und Auswertung der Umfrage verwendet.
            Die Angabe der optionalen Daten (Name des Kindes, Schulklasse) erfolgt freiwillig und nur,
            wenn der Nutzer diese explizit einwilligt.
        </p>

        <h2>3. Rechtsgrundlage der Verarbeitung</h2>
        <p>
            Die Verarbeitung der Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. a DSGVO (Einwilligung)
            sowie Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung), soweit die Daten für die Durchführung
            der Umfrage erforderlich sind.
        </p>

        <h2>4. Datenweitergabe und Zugriffsbeschränkung</h2>
        <p>
            Die personenbezogenen Daten sind <strong>ausschließlich für die Administratoren der Umfrage</strong>
            einsehbar. Andere Teilnehmer erhalten nur <strong>anonymisierte Summenwerte</strong>
            (z. B. Gesamtanzahl der Stimmen pro Option) angezeigt. Eine Weitergabe der Daten an Dritte
            erfolgt nicht, sofern keine gesetzliche Verpflichtung besteht.
        </p>

        <h2>5. Speicherdauer und Löschung</h2>
        <p>
            Die personenbezogenen Daten werden <strong>nur für den Zeitraum der Umfrage</strong> gespeichert.
            Nach Beendigung der Umfrage werden die Daten <strong>unwiderruflich und vollständig gelöscht</strong>,
            sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.
        </p>

        <h2>6. Rechte der betroffenen Personen</h2>
        <p>Betroffene haben folgende Rechte gemäß DSGVO:</p>
        <ul>
            <li><strong>Auskunft</strong> (Art. 15 DSGVO): Recht auf Auskunft über die zu Ihrer Person gespeicherten Daten.</li>
            <li><strong>Berichtigung</strong> (Art. 16 DSGVO): Recht auf Berichtigung unrichtiger Daten.</li>
            <li><strong>Löschung</strong> (Art. 17 DSGVO): Recht auf Löschung der Daten, sofern die Voraussetzungen vorliegen.</li>
            <li><strong>Einschränkung der Verarbeitung</strong> (Art. 18 DSGVO): Recht auf Einschränkung der Verarbeitung unter bestimmten Voraussetzungen.</li>
            <li><strong>Datenübertragbarkeit</strong> (Art. 20 DSGVO): Recht auf Übermittlung der Daten in einem maschinell lesbaren Format.</li>
            <li><strong>Widerruf der Einwilligung</strong> (Art. 7 Abs. 3 DSGVO): Recht, die erteilte Einwilligung zur Datenverarbeitung jederzeit mit Wirkung für die Zukunft zu widerrufen.</li>
        </ul>
        <p>
            Zur Geltendmachung dieser Rechte wenden Sie sich bitte an den Verantwortlichen unter den oben genannten Kontaktdaten.
        </p>

        <h2>7. Beschwerderecht</h2>
        <p>
            Unbeschadet eines anderweitigen verwaltungsrechtlichen oder gerichtlichen Rechtsbehelfs haben Sie das Recht,
            bei einer Aufsichtsbehörde, insbesondere in dem Mitgliedstaat Ihres Aufenthaltsorts, Ihres Arbeitsplatzes
            oder des Orts des mutmaßlichen Verstoßes, Beschwerde einzulegen, wenn Sie der Ansicht sind,
            dass die Verarbeitung Ihrer personenbezogenen Daten gegen die DSGVO verstößt.
        </p>

        <h2>8. Datensicherheit</h2>
        <p>
            Es werden technische und organisatorische Maßnahmen ergriffen, um die gesammelten Daten vor Verlust,
            Zerstörung, Manipulation und unberechtigtem Zugriff zu schützen.
        </p>

        <h2>9. Aktualisierung dieser Datenschutzerklärung</h2>
        <p>
            Diese Datenschutzerklärung kann bei Änderungen der rechtlichen oder technischen Rahmenbedingungen aktualisiert werden.
            Die aktuelle Version ist stets an dieser Stelle einsehbar.
        </p>

        <p class="submit-area">
            <a href="javascript:history.back();" class="button button-secondary">Zurück</a>
        </p>
    </div>
</body>
</html>