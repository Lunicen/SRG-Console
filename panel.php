<?php

use function htmlspecialchars as safe;


$host = 'db';
$db   = 'srg';
$user = 'root';
$pass = 'r00tadmin';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$pdo = new PDO($dsn, $user, $pass, $options);

// FILTER_SANITIZE_NUMBER_INT
$line =  filter_input(INPUT_GET, 'linia', FILTER_SANITIZE_NUMBER_INT);
$brigade =  filter_input(INPUT_GET, 'brygada', FILTER_SANITIZE_NUMBER_INT);
$type =  filter_input(INPUT_GET, 'typ', FILTER_SANITIZE_NUMBER_INT);

$tableName = "$line-$brigade-$type";
$hourTimes100plusMinutes = 1050; // TODO: change to current date, now only for tests

// http://localhost/panel.php?linia=13&brygada=1&typ=2`;%20DROP%20TABLE%20`13-1-2`;%20--

$stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE hour*100+minute < ?
ORDER BY hour DESC, minute DESC
LIMIT 1");
$stmt->execute([$hourTimes100plusMinutes]);
$result = $stmt->fetchAll();
//print_r($result);
print_r("Poprzednia stacja: ".$result[0]['hammerTime']. " Kierunek: ".$result[0]['direction'] ." Godzina: " . $result[0]['hour'] .":".$result[0]['minute'] );
echo ("<br>");
$stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE hour*100+minute >= ?
ORDER BY hour ASC, minute ASC
LIMIT 2");
$stmt->execute([$hourTimes100plusMinutes]);
$result = $stmt->fetchAll();
//print_r($result);
print_r("Obecna stacja: ".$result[0]['hammerTime']. " Kierunek: ".$result[0]['direction'] ." Godzina: " . $result[0]['hour'] .":".$result[0]['minute'] );



$direction = $_GET['kierunek'] ?? ''; //TODO: insert direction from database
$shouldDisplayRoute = $direction !== '';


?>
<!DOCTYPE html>

<html lang="pl" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>SRG Console</title>
    <link href="styles/styles.css" rel="stylesheet" />
    <link href="http://fonts.cdnfonts.com/css/bahnschrift" rel="stylesheet">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
    <!--Tram icon created by Good Ware - Flaticon-->
    <script type="text/javascript" src="scripts/time.js"></script>

</head>
<body onload="odliczanie();">



<div id="zegar"></div>

<div class="page">
    <div id="panel">
        <form>
            <label for="kierunek">Kierunek</label>
            <input class="input" type="text" value="<?= safe($direction) ?>" id="kierunek" name="kierunek" readonly><br />
            <input type="hidden" value="<?= safe($line) ?>" name="linia"><br />
            <input type="hidden" value="<?= safe($brigade) ?>" name="brygada"><br />
            <input type="hidden" value="<?= safe($type) ?>" name="typ"><br />

        </form>

        <p>Linia: <?= safe($line.'/'.$brigade) ?></p>

        <?php if ($shouldDisplayRoute): ?>
            <p>Przystanek:</p><br />
        <?php endif; ?>

    </div>
</div>

</body>
</html>
