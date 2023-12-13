<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link  rel="stylesheet" href="../../../css/listeRendezVous.css">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
        <title>Eureka - Liste des shouaits</title>
    </head>
    <body>
        <div class="row ligneHaut">
            <div class="col-4 align-items-center justify-content-center box">
                <Button class="btn"><img src="../../../ressources/logo.png"/><span class="h2 texte">Eureka</spawn></Button>
            </div>
            <div class="col-8 d-md-none ">
                <form action="../../deconnexion.php" method="post">
                    <button type="submit" class="btn"><img src="../../../ressources/deconnexion.png"/></button>
                </form>
            </div>
            <div class="col-8 d-none d-md-block test align-items-end">
                <Button class="btn btn-secondary boutonColonneGauche">ENTREPRISES</Button>
                <Button class="btn btn-primary boutonColonneGauche">RENDEZ-VOUS</Button>
            </div>
                <!-- <div class="col-xl-2 col-sm-8 align-items-center justify-content-center box">
                <Button class="btn"><img src="../../../ressources/logo.png"/><span class="h2 texte">Eureka</spawn></Button>
            </div>
            <div class="col-2 d-lg-none">
                <form action="../../deconnexion.php" method="post">
                    <button type="submit" class="btn"><img src="../../../ressources/deconnexion.png"/></button>
                </form>
            </div>
            <div class="col-xl-8 d-none d-lg-block test">
                <Button class="btn btn-secondary boutonColonneGauche">ENTREPRISES</Button>
                <Button class="btn btn-primary boutonColonneGauche">RENDEZ-VOUS</Button>
            </div> -->
        </div>
    </body>
</html>