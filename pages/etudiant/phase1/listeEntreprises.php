<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../bootstrap-4.6.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../fontawesome-free-6.2.1-web/css/all.css">
        <title>pas dev</title>
    </head>
    <body>
        <?php
            
            // Récupérer le nom de la page
            $currentPage = basename(__FILE__);


            // Afficher le nom de la page
            echo "<h1>Page: $currentPage</h1>";

            echo "<p>Cette page n'est pas développer.</p>";


            // Afficher les variables de session
            echo "<h2>Variables de session :</h2>";
            var_dump($_SESSION);

            // Afficher les variables POST
            echo "<h2>Variables POST :</h2>";
            var_dump($_POST);

            // Afficher les variables GET
            echo "<h2>Variables GET :</h2>";
            var_dump($_GET);
        ?>
    </body>
</html>