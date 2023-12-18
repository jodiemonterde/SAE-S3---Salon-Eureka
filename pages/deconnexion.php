<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../outils/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../outils/fontawesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="../monStyleSaePhp.css">
        <title>pas dev</title>
    </head>
    <body>
        <div class="container">
            <div class="row mx-1">
                <div class="col-4"></div>
                <div class="col-md-4 col-12 centrer text-center">
                    <h1>DÉCONNEXION</h1>
                    <P>Êtes-vous sûr(e) de vouloir vous déconnecter</P>
                    <div class="row">
                        <div class="col-6">
                        <a href="connexion.php"><button>Retour </button>
                        </div>
                        <div class="col-6">
                        <a href="../fonctions/deconnecter.php"><button>Se déconnecter </button>
                        </div>
                    </div>
                </div>
                <div class="col-4"></div>
            </div>
        </div>
    </body>
</html>