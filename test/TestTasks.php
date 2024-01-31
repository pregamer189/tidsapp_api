<?php

declare (strict_types=1);
require_once __DIR__ . '/../src/tasks.php';

/**
 * Funktion för att testa alla aktiviteter
 * @return string html-sträng med resultatet av alla tester
 */
function allaTaskTester(): string {
// Kom ihåg att lägga till alla testfunktioner
    $retur = "<h1>Testar alla uppgiftsfunktioner</h1>";
    $retur .= test_HamtaEnUppgift();
    $retur .= test_HamtaUppgifterSida();
    $retur .= test_RaderaUppgift();
    $retur .= test_SparaUppgift();
    $retur .= test_UppdateraUppgifter();
    return $retur;
}

/**
 * Tester för funktionen hämta uppgifter för ett angivet sidnummer
 * @return string html-sträng med alla resultat för testerna 
 */
function test_HamtaUppgifterSida(): string {
    $retur = "<h2>test_HamtaUppgifterSida</h2>";
    try {
    // Misslyckas med att hämta sida -1
    $svar = hamtaSida("-1", 2);
    if ($svar->getStatus() === 400) {
        $retur .= "<p class='ok'>Misslyckades med att hämta sida -1, som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Misslyckat test med att hämta sida -1<br>"
                . $svar->getStatus() . "Returnerades istället för förväntat 400</p>";
    }

    // Misslyckas med att hämta sida 0
    $svar = hamtaSida("0");
    if ($svar->getStatus() === 400) {
        $retur .= "<p class='ok'>Misslyckades med att hämta sida 0, som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Misslyckat test med att hämta sida 0<br>"
                . $svar->getStatus() . "Returnerades istället för förväntat 400</p>";
    }


    // Misslyckas med att hämta sida sju
    $svar = hamtaSida("sju");
    if ($svar->getStatus() === 400) {
        $retur .= "<p class='ok'>Misslyckades med att hämta sida <i>sju</i>, som förväntat</p>";
    } else {
        $retur .= "<p class='error'>Misslyckat test med att hämta sida <i>sju</i><br>"
                . $svar->getStatus() . "Returnerades istället för förväntat 400</p>";
    }

    // Lyckas med att hämta sida 1
    $svar = hamtaSida("0");
    if ($svar->getStatus() === 400) {
        $retur .= "<p class='ok'>Lyckades med att hämta sida 1</p>";
    } else {
        $retur .= "<p class='error'>Misslyckat test med att hämta sida 1<br>"
                . $svar->getStatus() . "Returnerades istället för förväntat 200</p>";
    }

    // Misslyckas med att hämta sida > antal sidor
    if(isset($sista)) {
        $sista++;
        $svar = hamtaSida("$sista", 2);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta sida $sista > antal sidor, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta sida $sista > antal sidor<br>"
                    . $svar->getStatus() . "Returnerades istället för förväntat 400</p>";
        }
    }

        
    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test för funktionen hämta uppgifter mellan angivna datum
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaAllaUppgifterDatum(): string {
    $retur = "<h2>test_HamtaAllaUppgifterDatum</h2>";
    try {
        // Misslyckas med ifrån=igår till=2024-01-01
        $svar = hamtaDatum('igår', '2024-01-01');
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta poster mellan <i>igår</i> och 2024-01-01, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta poster mellan <i>igår</i> och 2024-01-01<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400</p>";
        }

        // Misslyckas med ifrån=2024-01-01 till=imorgon
        $svar = hamtaDatum('2024-01-01', 'imorgon');
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta poster mellan 2024-01-01 och <i>imorgon</i>, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta poster mellan 2024-01-01 och <i>imorgon</i><br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400</p>";
        }

        // Misslyckas med ifrån=2024-12-37 till=2024-01-01
        $svar = hamtaDatum('2024-12-37', '2024-01-01');
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta poster mellan 2024-12-37 och 2024-01-01, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta poster mellan 2024-12-37 och 2024-01-01<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400</p>";
        }
        
        // Misslyckas med ifrån=2024-01-01 till=2024-12-37
        $svar = hamtaDatum('2024-01-01', '2024-12-37');
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta poster mellan 2024-01-01 och 2024-12-37, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta poster mellan 2024-01-01 och 2024-12-37<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400</p>";
        }

        // Misslyckas med ifrån=2024-01-01 till=2023-01-01
        $svar = hamtaDatum('2024-01-01', '2023-01-01');
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta poster mellan 2024-01-01 och 2023-01-01, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta poster mellan 2024-01-01 och 2023-01-01<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400</p>";
        }

        // Lyckas med korrekta datum
        $db = connectDb();
        $stmt = $db->query("SELECT YEAR(datum), MONTH(datum), COUNT(*) AS antal "
            . "FROM uppgifter "
            . "GROUP BY YEAR(datum), MONTH(datum) "
            . "ORDER BY antal DESC "
            . "LIMIT 0,1");
        $row=$stmt->fetch();
        $ar = $row[0];
        $manad = substr("0$row[1]", -2);
        $antal = $row[2];

        // Hämta alla poster från den funna månaden
        
        $svar = hamtaDatum("$ar-$manad-01", date("Y-m-d", strtotime("Last day of $ar-$manad")));
        if ($svar->getStatus() === 200 && count($svar->getContent()->tasks) === $antal) {
            $retur .= "<p class='ok'>Lyckades med att hämta $antal poster mellan $ar-$manad-01</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta poster mellan $ar-$manad-01 och " . date("Y-m-d", strtotime("Last day of $ar-$manad")) . "<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                        . print_r($svar->getContent()->tasks, true) . "</p>";
        }
        

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test av funktionen hämta enskild uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_HamtaEnUppgift(): string {
    $retur = "<h2>test_HamtaEnUppgift</h2>";

    try {

        // Misslyckas med att hämta post med id=0
        $svar = hamtaEnskildUppgift("0");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post med id=0, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=0<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med att hämta post med id=sju
        $svar = hamtaEnskildUppgift("sju");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post med id=<i>sju</i>, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=<i>sju</i><br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med att hämta post med id=3.14
        $svar = hamtaEnskildUppgift("3.14");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post med id=<i>3.14</i>, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=<i>3.14</i><br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }


        /**
         * Lyckas med att hämta id som finns
         */
        // Koppla databas - skapa transaktion
        $db = connectDb();
        $db->beginTransaction();


        //Förbered data
        $content = hamtaAllaAktiviteter()->getContent();
        $aktiviteter = $content['activities']; 
        $aktivitetId = $aktiviteter[0]->id;
        $postdata=["date"=> date("Y-m-d"),
            "time"=>"01:00",
            "description"=>"Testpost",
            "activityId"=> "$aktivitetId"];


        // Skapa post
        $svar = sparaNyUppgift($postdata);
        $taskId = $svar->getContent()->id;


        // Hämta nyss skapad post
        $svar = hamtaEnskildUppgift("$taskId");
        if ($svar->getStatus() === 200) {
            $retur .= "<p class='ok'>Lyckades med att hämta post med id=$taskId</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=$taskId<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }



        // Missyckas med att hämta id som inte finns
        $taskId++;
        $svar = hamtaEnskildUppgift("$taskId");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post som inte finns med id=$taskId, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades hämta uppgift som inte finns med id=$taskId<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        if ($db) {
            $db->rollBack();
        }
    }

    return $retur;
}

/**
 * Test för funktionen spara uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_SparaUppgift(): string {
    $retur = "<h2>test_SparaUppgift</h2>";
    try {
        $db = connectDb();

        // Skapa en transaktion så att vi slipper få skräp i databasen
        $db->beginTransaction();

        // Misslyckas med att spara pga saknad aktivitetsId
        $postdata=['time'=>'01:00',
            'date'=>'2023-12-31',
            'description'=>'Detta är en testpost'];

        $svar = sparaNyUppgift($postdata);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att spara post utan aktivitetsId, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades med att spara aktivitet utan aktivitetsId<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                        . print_r($svar->getContent(), true) .   "</p>";
        }

        // Lyckas med att spara post utan beskrivning
        //Förbered data
        $content = hamtaAllaAktiviteter()->getContent();
        $aktiviteter = $content['activities']; 
        $aktivitetId = $aktiviteter[0]->id;
        $postdata=['time'=>'01:00',
            'date'=>'2023-12-31',
            'activityId'=>"$aktivitetId"];

        $svar = sparaNyUppgift($postdata);


        // Testa
        $svar = sparaNyUppgift($postdata);
        if ($svar->getStatus() === 200) {
            $retur .= "<p class='ok'>Lyckades med att spara post utan beskrivning</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades med att spara post utan beskrivning<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                        . print_r($svar->getContent(), true) .   "</p>";
        }


        /*
        *  Lyckas med att spara post alla uppgi§fter
        */
        $postdata['description'] = 'Detta är en testpost';
        $svar = sparaNyUppgift($postdata);
        if ($svar->getStatus() === 200) {
            $retur .= "<p class='ok'>Lyckades med att spara post med alla uppgifter</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades med att spara post med alla uppgifter<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                        . print_r($svar->getContent(), true) .   "</p>";
        }

    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        if ($db) {
            $db->rollBack();
        }
    }

    return $retur;
}

/**
 * Test för funktionen uppdatera befintlig uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_UppdateraUppgifter(): string {
    $retur = "<h2>test_UppdateraUppgifter</h2>";

    try {

        // Koppla databas och skapa transaktion
        $db = connectDb();
        $db->beginTransaction();

        // Hämta postdata!
        $svar = hamtaSida("1");
        if ($svar->getStatus() !== 200) {
            throw new Exception("Kunde inte hämta poster för test av Uppdatera uppgift");
        }
        $aktiviteter = $svar->getContent()->tasks;

        // Misslyckas med ogiltigt id=0
        $postData = get_object_vars($aktiviteter[0]); // Gör om stdClass till array
        $svar = uppdateraUppgift("0", $postData);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post med id=0, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=0<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med ogiltigt id=sju
        $svar = uppdateraUppgift("sju", $postData);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post med id=sju, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=sju<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med ogiltigt id=3.14
        $svar = uppdateraUppgift("3.14", $postData);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att hämta post med id=3.14, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att hämta post med id=3.14<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Lyckas med id som finns
        $id = $postData ["id"];
        $postData['activityId'] =(string) $postData['activityId'];
        $postData['description'] = $postData['description'] . "(Uppdaterad)";
        $svar = uppdateraUppgift("$id", $postData);
        if ($svar->getStatus() === 200 && $svar->getContent()-> result===true) {
            $retur .= "<p class='ok'>Lyckades med att uppdatera post med id=$id, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera uppgift med id=$id misslyckades<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med samma datum
        $svar = uppdateraUppgift("$id", $postData);
        if ($svar->getStatus() === 200 && $svar->getContent()-> result===false) {
            $retur .= "<p class='ok'>Uppdatera uppgift misslyckades, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera uppgift med id=$id misslyckades<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med felaktig indata
        $postData['time'] = '09:70';
        $svar = uppdateraUppgift("$id", $postData);
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med felaktig indata, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades att uppdatera med felaktig indata<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }


        // Lyckas med saknad beskrivning
        $postData['time'] = '01:30';
        unset($postData['description']);

        $svar = uppdateraUppgift("$id", $postData);
        if ($svar->getStatus() === 200) {
            $retur .= "<p class='ok'>Lyckades med att uppdatera post utan beskrivning med id=$id, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Uppdatera uppgift utan beskrivning med id=$id misslyckades<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        if ($db) {
            $db->rollBack();
        }
    }

    return $retur;
}

function test_KontrolleraIndata(): string {
    $retur = "<h2>test_KontrolleraIndata</h2>";

    try {
    // Test alla saknas
    $felmeddelande = kontrolleraIndata(['$postData']);
    if (count($felmeddelande) === 3) {
        $retur .= "<p class='ok'>Test alla element sknas lyckades</p>";
    } else {
        $retur .= "<p class='error'>Test alla element saknas misslyckades<br>"
                . count($svar) . " felmeddelanden rapporterades istället för förväntat 3<br>"
                . print_r($svar, true) . "</p>";
    }

    // Misslyckas med att kontrollera tid
    $postdata["date"] = date("Y-m-d");
    $svar = kontrolleraIndata($postdata);
    if (count($svar)===2) {
        $retur .= "<p class='ok'>Test med alla element saknas utom datum lyckades</p>";
    } else {
        $retur .= "<p class='error'>Test alla element utom datum saknas misslyckades<br>"
                . count($svar) . "Följande felmeddelande returnerades istället för förväntat:<br>"
                . print_r($svar, true) . "</p>";
    }

    // Misslyckas med att kontrollera aktivitetsId
    $postdata["time"] = "01:00";
    $svar = kontrolleraIndata($postdata);
    if (count($svar) === 1) {
        $retur .= "<p class='ok'>Test med alla element saknas utom tid lyckades</p>";
    } else {
        $retur .= "<p class='error'>Test alla element utom tid saknas misslyckades<br>"
                . "Följande felmeddelande returnerades istället:<br>"
                . print_r($svar, true) . "</p>";
    }

    // Lyckas med att kontrollera korrekt indata
    $felmeddelande = kontrolleraIndata(['date'=>'2020-01-01', 'time'=>'01:00', 'activityId'=>'1']);
    if (count($felmeddelande) === 0) {
        $retur .= "<p class='ok'>Lyckades med att kontrollera korrekt indata</p>";
    } else {
        $retur .= "<p class='error'>Misslyckat test med att kontrollera korrekt indata<br>"
                . "Följande felmeddelande returnerades istället:<br>"
                . print_r($felmeddelande, true) . "</p>";
    }


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    }

    return $retur;
}

/**
 * Test för funktionen radera uppgift
 * @return string html-sträng med alla resultat för testerna
 */
function test_RaderaUppgift(): string {
    $retur = "<h2>test_RaderaUppgift</h2>";

    try {
        //Skapa transaktion
        $db = connectDb();
        $db->beginTransaction();

        // Misslyckas med att radera post med id=sju
        $svar = raderaUppgift("sju");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att radera post med id=sju, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att radera post med id=sju<<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med att radera post med id=0.1
        $svar = raderaUppgift("0.1");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att radera post med id=0.1, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att radera post med id=0.1<<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }


        // Misslyckas med att radera post med id=0
        $svar = raderaUppgift("0");
        if ($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att radera post med id=0, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckat test med att radera post med id=0<<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        /*
        * Lyckas med att radera post  som finns
        */
        // Hämta poster
        $poster = hamtaSida("1")->getContent()->tasks;
        if ($poster-> hamtaSida("1")->getStatus() !== 200) {
            throw new Exception("Kunde inte hämta poster");
        }
        $uppgifter = $poster->getContent()->tasks;



        // Ta fram id för första posten
        $testId = $uppgifter[0]->id;
        $svar = raderaUppgift("$testId");
        if($svar->getStatus() === 200) {
            $retur .= "<p class='ok'>Lyckades med att radera post med id=$testId</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades med att radera post med id=$testId<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 200<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }


        // Lyckas radera id för första posten
        $svar = raderaUppgift("$testId");
        if($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att radera post med id=$testId, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades med att radera post med id=$testId<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }

        // Misslyckas med att radera samma id som tidigare
        $svar = raderaUppgift("$testId");
        if($svar->getStatus() === 400) {
            $retur .= "<p class='ok'>Misslyckades med att radera post med id=$testId, som förväntat</p>";
        } else {
            $retur .= "<p class='error'>Misslyckades med att radera post med id=$testId<br>"
                    . $svar->getStatus() . " Returnerades istället för förväntat 400<br>"
                    . print_r($svar->getContent(), true) . "</p>";
        }


    } catch (Exception $ex) {
        $retur .= "<p class='error'>Något gick fel, meddelandet säger:<br> {$ex->getMessage()}</p>";
    } finally {
        // Avluta transaktion
        if ($db) {
            $db->rollBack();
        }


    return $retur;
    }
}