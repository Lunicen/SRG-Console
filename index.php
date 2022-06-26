<!DOCTYPE html>

<html lang="pl-PL" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8" />
    <title>SRG Console</title>
    <link href="http://fonts.cdnfonts.com/css/bahnschrift" rel="stylesheet">
    <link href="styles/styles.css" rel="stylesheet" />
</head>
<body>
<div id="container">
    <div class="form-container">
        <form action="panel.php" method="get">
            <div class="column">
                <label for="linia">Linia</label>
                <label for="brygada">Brygada</label>
                <label for="typ">Typ dnia</label>
            </div>
            <div class="column">
                <input class="input" type="text" id="linia" name="linia">
                <input class="input" type="text" id="brygada" name="brygada">
                <input class="input" type="text" id="typ" name="typ">
            </div>
                
                <!--<input type="submit" value="Zatwierdź" class="actual">-->
            
        </form>
    </div>
</div>
</body>
</html>