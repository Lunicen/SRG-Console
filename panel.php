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

    date_default_timezone_set('Europe/Warsaw');

    // FILTER_SANITIZE_NUMBER_INT
    $line =  filter_input(INPUT_GET, 'linia', FILTER_SANITIZE_NUMBER_INT);
    $brigade =  filter_input(INPUT_GET, 'brygada', FILTER_SANITIZE_NUMBER_INT);
    $type =  filter_input(INPUT_GET, 'typ', FILTER_SANITIZE_NUMBER_INT);

    $tableName = "$line-$brigade-$type";
    $hourTimes100plusMinutes = date('Hi'); // TODO: change to current date, now only for tests

    // http://localhost/panel.php?linia=13&brygada=1&typ=2`;%20DROP%20TABLE%20`13-1-2`;%20--

    $stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE hour*100+minute < ? ORDER BY hour DESC, minute DESC LIMIT 1");
    $stmt->execute([$hourTimes100plusMinutes]);
    $result = $stmt->fetchAll();

    print_r("Poprzednia stacja: ".$result[0]['hammerTime']. " Kierunek: ".$result[0]['direction'] ." Godzina: " . $result[0]['hour'] .":".$result[0]['minute'] );
    echo ("<br>");

    $stmt = $pdo->prepare("SELECT * FROM `$tableName` WHERE hour*100+minute >= ? ORDER BY hour ASC, minute ASC LIMIT 2");
    $stmt->execute([$hourTimes100plusMinutes]);
    $result = $stmt->fetchAll();

    print_r("Obecna stacja: ".$result[0]['hammerTime']. " Kierunek: ".$result[0]['direction'] ." Godzina: " . $result[0]['hour'] .":".$result[0]['minute'] );



    $direction = $result[0]['direction']; //TODO: insert direction from database
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

</head>
<body onload="launchClock();">
    <div id="clock"></div>

    <div id="container">
        <div class="form-container">
            <form>
                <p>Linia  <?= safe($line.'/'.$brigade) ?></p>    
                <div class="row">
                    <div class="column">
                        <label for="kierunek">Kierunek</label>
                    </div>
                    <div class="column">
                        <input class="input" style="text-align: center;" type="text" value="<?= safe($direction) ?>" id="kierunek" name="kierunek" readonly><br />
                    </div>
                </div>
                
                <table>
                    <colgroup>
                        <col span="1" style="width: 30%;">
                        <col span="1" style="width: 70%;">
                    </colgroup>

                    <tr>
                        <th>Czas</th>
                        <th>Przystanek</th>
                    </tr>
                    <tr>
                        <td>+<div id="timeToTheNextStop" /></td>
                        <td><div id="nameOfTheNextStop" /></td>
                    </tr>
                    <tr>
                        <td>+<div id="timeToTheCurrentStop" /></td>
                        <td><div id="nameOfTheCurrentStop" /></td>
                    </tr>
                    <tr>
                        <td>-<div id="timeFromThePreviousStop" /></td>
                        <td><div id="nameOfThePreviousStop" /></td>
                    </tr>
                </table>
                <input type="hidden" value="<?= safe($line) ?>" name="linia"><br />
                <input type="hidden" value="<?= safe($brigade) ?>" name="brygada"><br />
                <input type="hidden" value="<?= safe($type) ?>" name="typ"><br />
            </form>

            <!--<?php if ($shouldDisplayRoute): ?>
                <p>Przystanek:</p><br />
            <?php endif; ?>-->

        </div>
    </div>

    <script>
        function launchClock()
        {
            var date = new Date();

            var hour 	= date.getHours();
            var minute 	= date.getMinutes();
            var second 	= date.getSeconds();

            if (hour < 10) 	 hour = "0" + hour;
            if (minute < 10) minute = "0" + minute;
            if (second < 10) second = "0" + second;

            document.getElementById("clock").innerHTML = hour + ":" + minute + ":" + second;

            if (second == 0) {
                window.top.location.reload();
            }

            setTimeout("launchClock()", 1000);
        }

        function updateTimetable()
        {
            
        }
    </script>
</body>
</html>
