<?php

declare (strict_types=1);
require_once __DIR__ . '/Settings.php'; // Settings;

function connectDb(): PDO {
    static $db = null;

    if ($db === null) {
        // Hämta settings 
        $settings = new Settings();
        // Koppla mot databasen
        $dsn = $settings->dsn;
        $dbUser = $settings->dbUser;
        $dbPassword = $settings->dbPassword;
        $db = new PDO($dsn, $dbUser, $dbPassword);
    }

    return $db;
}

function kontrolleraIndata(array $postdata):array {
    $retur = [];

    // Kontrollera datum $postdata["date"]
    $datum = DateTimeImmutable::createFromFormat("Y-m-d", $postdata['date']??'');
    if (!$datum) {
        $retur[] = "Ogiltigt angivet datum";
    }

    if ($datum && $datum->format("Y-m-d") !== $postdata["date"]) {
        $retur[] = "Felaktigt formaterat datum";
    } 

    if ($datum && $datum->format("Y-m-d") > date("Y-m-d")) {
        $retur[] = "Datumet får inte vara framåt i tiden";
    }

    // Kontrollera tid $postdata["time"]
    $tid = DateTimeImmutable::createFromFormat("H:i", $postdata["time"]??'');
    if (!$tid) {
        $retur[] = "Ogiltigt angiven tid";
    }

    if ($tid && $tid->format("H:i") !== $postdata["time"]) {
        $retur[] = "Felaktigt angiven tid";
    }

    if ($tid && $tid->format("H:i") > "08:00") {
        $retur[] = "Du får inte raportera mer än 8 timmar per aktivitet åt gången";
    }

    // Kontrollera aktivitetsId $postdata["activityId"]
    $aktivitet = hamtaEnskildAktivitet($postdata["activityId"]??'');
    if ($aktivitet->getStatus() === 400) {
        $retur[] = "Angivet aktivitets id saknas";
    }

    // Ta bort konstiga tecken från beskrivning $postdata["description"]
    if (trim(filter_var($postdata["description"], FILTER_SANITIZE_SPECIAL_CHARS)) === "") {
        $retur[] = "Beskrivning saknas";
    }


    return $retur;
}
