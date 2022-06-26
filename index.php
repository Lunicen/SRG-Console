<!DOCTYPE html>

<html lang="pl-PL" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>SRG Console</title>
    <link href="http://fonts.cdnfonts.com/css/bahnschrift" rel="stylesheet">
    <link href="styles/styles.css" rel="stylesheet">
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon">
    <!--Tram icon created by Good Ware - Flaticon-->
</head>
<body>
<div id="container">
    <div class="form-container">
        <form action="panel.php" method="get">
            <div class="row">
                <div class="column">
                    <label for="route">Linia</label>
                    <label for="brigade">Brygada</label>
                    <label for="type">Typ dnia</label>
                </div>
                <div class="column">
                    <input class="input" type="text" name="route">
                    <input class="input" type="text" name="brigade">
                    <input class="input" type="text" name="type">
                </div>
            </div>  
            <input type="submit" value="Zatwierdź">
        </form>
    </div>
</div>
</body>
</html>