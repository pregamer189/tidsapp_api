<?php

declare (strict_types=1);
require_once __DIR__ . '/funktioner.php';
require_once __DIR__ . '/Route.php';
require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/RequestMethod.php';

/**
 * Läs av rutt-information och anropa funktion baserat på angiven rutt
 * @param Route $route Rutt-information
 * @param array $postData Indata för behandling i angiven rutt
 * @return Response
 */
function activities(Route $route, array $postData): Response {
    try {
        if (count($route->getParams()) === 0 && $route->getMethod() === RequestMethod::GET) {
            return hamtaAllaAktiviteter();
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::GET) {
            return hamtaEnskildAktivitet($route->getParams()[0]);
        }
        if (isset($postData["activity"]) && count($route->getParams()) === 0 &&
                $route->getMethod() === RequestMethod::POST) {
            return sparaNyAktivitet((string) $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::PUT) {
            return uppdateraAktivitet( $route->getParams()[0],  $postData["activity"]);
        }
        if (count($route->getParams()) === 1 && $route->getMethod() === RequestMethod::DELETE) {
            return raderaAktivitet($route->getParams()[0]);
        }
    } catch (Exception $exc) {
        return new Response($exc->getMessage(), 400);
    }

    return new Response("Okänt anrop", 400);
}

/**
 * Returnerar alla aktiviteter som finns i databasen
 * @return Response
 */
function hamtaAllaAktiviteter(): Response {
    // Koppla mot databas
    $db = connectDb();

    // Hämta alla aktiviteter
    $result = $db->query("SELECT id, Namn FROM aktiviteter");

    // Skapa returvärde
    $retur = [];
    foreach ($result as $item) {
        $post=new stdClass();
        $post->id = $item["id"];
        $post->activity = $item["Namn"];
        $retur[] = $post;
    }

    // Skicka svar
    return new Response(["activities"=>$retur]);
}

/**
 * Returnerar en enskild aktivitet som finns i databasen
 * @param string $id Id för aktiviteten
 * @return Response
 */
function hamtaEnskildAktivitet(string $id): Response {
    // Kontrollera imparametrar
    $kontrolleratId = filter_var($id, FILTER_VALIDATE_INT);

    if ($kontrolleratId === false || $kontrolleratId <1) {
        $retur = new stdClass();
        $retur->error = ['bad request', 'Ogiltigt id'];
        return new Response($retur, 400);
    }

    // Koppla mot databasen
    $db = connectDb();

    //Skicka fråga
    $stmt = $db->prepare("SELECT id, Namn FROM aktiviteter WHERE id = :id");
    $result = $stmt->execute([":id" => $kontrolleratId]);


    // Kontrollera svar
    if ($row = $stmt -> fetch(PDO::FETCH_ASSOC)) {
        $retur = new stdClass();
        $retur->$id = $row['id'];
        $retur->activity = $row['Namn'];
        return new Response($retur);
    } else {
        $retur = new stdClass();
        $retur->error = ['bad request', 'Angivet id ' . $kontrolleratId . ' hittades inte'];
        return new Response($retur, 400);
    }
}



/**
 * Lagrar en ny aktivitet i databasen
 * @param string $aktivitet Aktivitet som ska sparas
 * @return Response
 */

function sparaNyAktivitet(string $aktivitet): Response {
    // Kontrollera indata - resna bort onödiga teckan
    $kontrolleradAktivitet = filter_var($aktivitet, FILTER_SANITIZE_SPECIAL_CHARS);

    
    // Kontrollera att aktivitet inte är tom!
    if(trim($aktivitet) === "") {
        $retur = new stdClass();
        $retur->error = ['bad request', 'Aktivitet får inte vara tom'];
        return new Response($retur, 400);
    }

    try {
        // Koppla mot databas
        $db = connectDb();


        // Exekvera frågan
        $stmt = $db->prepare("INSERT INTO aktiviteter (Namn) VALUES (:aktivitet)");
        $svar= $stmt->execute([":aktivitet" => $kontrolleradAktivitet]);


        // Kontrollera svaret och returnera svar
    
        if ($svar===true) {
            $retur = new stdClass();
            $retur->id = $db->lastInsertId();
            $retur->meddelande = ['Spara lyckades', '1 post lades till'];
            return new Response($retur);
        } else {
            $retur = new stdClass();
            $retur->error = ['bad request', 'Något gick fel vid spara'];
            return new Response($retur, 400);
        }
        } catch (Exception $e) {
            $retur = new stdClass();
            $retur-> error = ['bad request', 'Fel vid spara', $e->getMessage()];
            return new Response($retur, 400);
    }
}

/**
 * Uppdaterar angivet id med ny text
 * @param string $id Id för posten som ska uppdateras
 * @param string $aktivitet Ny text
 * @return Response
 */

function uppdateraAktivitet(string $id, string $aktivitet): Response {
    // Kontrollera indata
    $kontrolleradId= filter_var($id, FILTER_VALIDATE_INT);
    $kontrolleradAktivitet = filter_var($aktivitet, FILTER_SANITIZE_SPECIAL_CHARS);
    $kontrolleradAktivitet = trim($kontrolleradAktivitet);

    if ($kontrolleradId === false || $kontrolleradId <1 || $kontrolleradAktivitet === "") {
        $retur = new stdClass();
        $retur->error = ['bad request', 'Felaktig indata till uppdatera aktivitet'];
        return new Response($retur, 400);
    }
    try {
        // Koppla databas
        $db = connectDb();

        // Förbered fråga
        $stmt = $db->prepare("UPDATE aktiviteter SET Namn = :aktivitet WHERE id = :id");
        $stmt->execute([":aktivitet" => $kontrolleradAktivitet, ":id" => $kontrolleradId]);

        // Hantera svar
        if ($stmt->rowCount() === 1) {
          echo "$kontrolleradId";
            $retur = new stdClass();
            $retur->result=true;
            $retur->message = ['Uppdatering lyckades', '1 post uppdaterades'];
            return new Response($retur);
        } else {
              $retur = new stdClass();
            $retur->result=false;
            $retur->message = ['Uppdatering misslyckades', 'ingen rad uppdaterades'];
            return new Response($retur, 200);
        }
        } catch (Exception $e) {
            $retur= new stdClass();
            $retur->error= ['bad request', 'Fel vid uppdatering', $e->getMessage()];
            return new Response($retur, 400);
            
        }
    }

/**
 * Raderar en aktivitet med angivet id
 * @param string $id Id för posten som ska raderas
 * @return Response
 */
function raderaAktivitet(string $id): Response {
    // Kontrollera indata
    $kontrolleradId = filter_var($id, FILTER_VALIDATE_INT);

    if ($kontrolleradId === false || $kontrolleradId <1) {
        $retur = new stdClass();
        $retur->error = ['bad request', 'Felaktigt angivet id'];
        return new Response($retur, 400);
    }

    try {
    // Koppla mot databas
        $db = connectDb();
       

    // Exekvera SQL
        $stmt = $db->prepare("DELETE FROM aktiviteter WHERE id = :id");
        $stmt->execute(["id" => $kontrolleradId]);


        if ($stmt->rowCount() === 1) {
            $retur = new stdClass();
            $retur->result=true;
            $retur->message = ['Radering lyckades', '1 post raderades från databasen'];
           
        } else {
            $retur = new stdClass();
            $retur->result=false;
            $retur->message = ['Radering misslyckades', 'ingen post raderades fråm databasen'];
        }
        return new Response($retur);
    } catch (Exception $e) {
            $retur = new stdClass();
            $retur->error = ['bad request', 'Fel vid radering', $e->getMessage()];
            return new Response($retur, 400);
        }
    }
