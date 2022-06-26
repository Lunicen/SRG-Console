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
    $line =  filter_input(INPUT_GET, 'route', FILTER_SANITIZE_NUMBER_INT);
    $brigade =  filter_input(INPUT_GET, 'brigade', FILTER_SANITIZE_NUMBER_INT);
    $type =  filter_input(INPUT_GET, 'type', FILTER_SANITIZE_NUMBER_INT);

    $tableName = "$line-$brigade-$type";
    $currentHour = date('H');
    $currentMinute = date('i');

    // Previous stop
    $query = $pdo->query(
        "SELECT * FROM `$tableName` WHERE 
        $currentHour >= `hour` AND $currentMinute > `minute` 
        ORDER BY hour DESC, minute DESC LIMIT 1;");
    $result = $query->fetchAll();

    $previousStopTime = $result[0]['minute'];
    $previousStopName = $result[0]['stopName'];

    // Current stop
    $query = $pdo->query(
        "SELECT * FROM `$tableName` WHERE 
        $currentHour <= `hour` AND $currentMinute < `minute` 
        ORDER BY hour ASC, minute ASC LIMIT 1;");
    $result = $query->fetchAll();

    $currentStopTime = $result[0]['minute'];
    $currentStopName = $result[0]['stopName'];

    // Next stop
    $query = $pdo->query(
        "SELECT * FROM `$tableName` WHERE 
        $currentHour <= `hour` AND $currentMinute < `minute` 
        ORDER BY hour ASC, minute ASC LIMIT 1 OFFSET 1;");
    $result = $query->fetchAll();
    
    $nextStopTime = $result[0]['minute'];
    $nextStopName = $result[0]['stopName'];

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
                <p style="margin-top: 3rem;">Linia  <?= safe($line.'/'.$brigade) ?></p>    
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
                        <td><div id="timeToTheNextStop" /></td>
                        <td><?= safe($nextStopName) ?></td>
                    </tr>
                    <tr>
                        <td><div id="timeToTheCurrentStop" /></td>
                        <td><?= safe($currentStopName) ?></td>
                    </tr>
                    <tr>
                        <td><div id="timeFromThePreviousStop" /></td>
                        <td><?= safe($previousStopName) ?></td>
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

            setTimeout("launchClock()", 1000);
        }

        function timeDistanceFrom(stopTime)
        {
            if (stopTime === undefined) {
                return "---";
            }

            var currMinute = new Date().getMinutes();
            var currSecond = new Date().getSeconds();

            var sign = (stopTime - currMinute - 1) >= 0 ? "+" : "";

            var secondsDistance = sign == "+" ? (60 - currSecond) : currSecond;
            var secondsOnTwoDigits = secondsDistance > 9 ? secondsDistance : "0" + secondsDistance

            return new String(sign + (stopTime - currMinute - 1) + ":" + secondsOnTwoDigits);
        }

        setInterval(function updateTimetables()
        {


            document.getElementById("timeToTheNextStop").innerHTML          = timeDistanceFrom(<?= safe($nextStopTime) ?>);
            document.getElementById("timeToTheCurrentStop").innerHTML       = timeDistanceFrom(<?= safe($currentStopTime) ?>);
            document.getElementById("timeFromThePreviousStop").innerHTML    = timeDistanceFrom(<?= safe($previousStopTime) ?>);
            
            var currentStopTime = <?= empty($currentStopTime) ? -1 : $currentStopTime ?>;
            var currMinute = new Date().getMinutes();
            
            if (currentStopTime - currMinute == 0)
            {
                window.top.location.reload();
            }
            
        }, 1000);
    </script>
</body>
</html>
