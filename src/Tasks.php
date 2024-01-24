<?php

declare (strict_types=1);
require_once __DIR__ . '/activities.php';

/**
 * Hämtar en lista med alla uppgifter och tillhörande aktiviteter 
 * Beroende på indata returneras en sida eller ett datumintervall
 * @param Route $route indata med information om vad som ska hämtas
 * @return Response
 */
function tasklists(Route $route): Response {
    try {
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaSida($route->getParams()[0]);
        }
        if (count($route->getParams()) === 2 && $route->getMethod() === RequestMethod::GET) {
            return hamtaDatum($route->getParams()[0], $route->getParams()[1]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function tasks(Route $route, array $postData): Response {
    try {
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskildUppgift($route->getParams()[0]);
        }
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::POST) {
            return sparaNyUppgift($postData);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdateraUppgift( $route->getParams()[0], $postData);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return raderaUppgift($route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }
}

/**
 * Hämtar alla uppgifter för en angiven sida
 * @param string $sida
 * @return Response
 */
function hamtaSida(string $sida): Response {
    $posterPerSida=2;

    // Kontrollera indata
    $sidnummer= filter_var($sida, FILTER_VALIDATE_INT);
    if ($sidnummer === false || $sidnummer < 1){
        $retur = new stdClass();
        $retur->error=["Bad request", "Felaktigt sidnummer"];
        return new Response($retur, 400);
    }

    // Koppla mot databas
    $db = connectDb();

    // Hämta poster
    $stmt = $db->query("SELECT count(*) FROM uppgifter");
    $antalPoster=$stmt->fetchColumn();

    if (!$antalPoster){
        $retur = new stdClass();
        $retur->error=["Inga poster hittades"];
        return new Response($retur, 400);
    }

    $antalSidor=ceil($antalPoster/$posterPerSida);
        if ($sidnummer > $antalSidor){
            $retur = new stdClass();
            $retur->error=["Bad request", "Felaktigt sidnummer", "Det finns bara $antalSidor sidor"];
            return new Response($retur, 400);
        }

        $forstaPost=($sidnummer-1)*$posterPerSida;
        $stmt = $db->query("SELECT u.id, datum, tid, beskrivning, aktivitetId, namn "
            . "FROM uppgifter u INNER JOIN aktiviteter a ON aktivitetId=aktivitetid=a.id "
            . "ORDER BY datum "
            . "LIMIT $forstaPost, $posterPerSida");
        $result=$stmt->fetchAll();


        $uppgifter = [];
        foreach ($result as $row) {
            $rad = new stdClass();
            $rad->id = $row["id"];
            $rad->aktivitetId = $row["aktivitetId"];
            $rad->date = $row["datum"];
            $tid = new DateTime($row["tid"]);
            $rad->time = $tid->format("H:i");
            $rad->activity = $row["namn"];
            $rad->description = $row["beskrivning"];
            $uppgifter[] = $rad;
        }
    // Returnera svar
    $retur = new stdClass();
    $retur->pages = $antalSidor;
    $retur->tasks = $uppgifter;

    return new Response($retur);
}

/**
 * Hämtar alla poster mellan angivna datum
 * @param string $from
 * @param string $tom
 * @return Response
 */
function hamtaDatum(string $from, string $tom): Response {
    // Kontrollera indata
    $fromDate = DateTimeImmutable::createFromFormat("Y-m-d", $from);
    $tomDate = DateTimeImmutable::createFromFormat("Y-m-d", $tom);
    $datumFel = [];

    if ($fromDate === false) {
        $datumFel[] = "Ogiltigt från-datum";
    }

    if ($tomDate === false) {
        $datumFel[] = "Ogiltigt till-datum";
    }


    if($fromDate && $fromDate->format("Y-m-d")!==$from){
        $datumFel[] = "Ogiltigt angivet från-datum";
    }

    if($tomDate && $tomDate->format("Y-m-d")!==$tom){
        $datumFel[] = "Ogiltigt angivet till-datum";
    }


    if ($fromDate && $tomDate && $fromDate->format("Y-m-d") > $tomDate->format("Y-m-d")) {
        $datumFel[] = "Från-datum får inte vara större än till-datum";
    }


    if (count($datumFel) > 0) {
        $retur = new stdClass();
        $retur->error = $datumFel;
        array_unshift($retur->error, 'bad request');
        return new Response($retur, 400);
    }

    // Koppla databas
    $db = connectDb();

    // Exekvera SQL
    $stmt = $db->prepare("SELECT u.id, datum, tid, beskrivning, aktivitetId, namn "
            . "FROM uppgifter u INNER JOIN aktiviteter a ON aktivitetId=aktivitetid=a.id "
            . "WHERE datum BETWEEN :from AND :to "
            . "ORDER BY datum");
        $stmt->execute(["from" => $fromDate->format("Y-m-d"), "to" => $tomDate->format("Y-m-d")]);

        $result=$stmt->fetchAll();


        $uppgifter = [];
        foreach ($result as $row) {
            $rad = new stdClass();
            $rad->id = $row["id"];
            $rad->aktivitetId = $row["aktivitetId"];
            $rad->date = $row["datum"];
            $tid = new DateTime($row["tid"]);
            $rad->time = $tid->format("H:i");
            $rad->activity = $row["namn"];
            $rad->description = $row["beskrivning"];
            $uppgifter[] = $rad;
        }

    // Returnera svar
    $retur = new stdClass();
    $retur->tasks = $uppgifter;
    return new Response($retur);

}

/**
 * Hämtar en enskild uppgiftspost
 * @param string $id Id för post som ska hämtas
 * @return Response
 */
function hamtaEnskildUppgift(string $id): Response {
    
}

/**
 * Sparar en ny uppgiftspost
 * @param array $postData indata för uppgiften
 * @return Response
 */
function sparaNyUppgift(array $postData): Response {
    
}

/**
 * Uppdaterar en angiven uppgiftspost med ny information 
 * @param string $id id för posten som ska uppdateras
 * @param array $postData ny data att sparas
 * @return Response
 */
function uppdateraUppgift(string $id, array $postData): Response {
    
}

/**
 * Raderar en uppgiftspost
 * @param string $id Id för posten som ska raderas
 * @return Response
 */
function raderaUppgift(string $id): Response {
    
}
