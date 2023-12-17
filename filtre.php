<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="lib/bootstrap-4.6.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="lib/fontawesome-free-6.2.1-web/css/all.css">
        <link rel="stylesheet" href="filtre.css">
        <title>pas dev</title>
    </head>
    <body>
        <?php
            include 'fonctions/baseDeDonnees.php';
            $pdo = connecteBD();
            $fields = getFields($pdo);
            $longestTextLength = 0;
            while ($ligne = $fields->fetch()) {
                $field = $ligne['name'];
                $textLength = strlen($field);
                if ($textLength > $longestTextLength) {
                    $longestTextLength = $textLength;
                }
                ?>
                <button class="btn btn-primary"><?php echo $field; ?></button>
            <?php } ?>
    </body>
</html>