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
        <div class="container-fluid">
            <div class="row ligneHaut">
                <div class="col-4 align-items-center justify-content-center">
                    <Button class="btn"><img src="../../../ressources/logo.png"/><span class="h2 texte">Eureka</spawn></Button>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <p><h2>Vos demandes de rendez-vous</h2></p>
                    <p>Pour le moment, au terme de la phase de prise de rendez-vous, Eureka vous proposera un planning avec les entreprises suivantes. Vous pouvez tout à fait changer d’avis !</P>
                </div>
            </div>
            <?php for ($i = 0; $i < 57; $i++) { ?>
            <div class="row entreprise align-items-center">
                <div class="col-2 col-lg-1">
                    <img src="../../../ressources/test.png" alt="logo" class="logoEntreprise" width="75px" height="75px"/>
                </div>
                <div class="col-8 col-md-6 col-lg-8 col-xxl-9">
                    <span class="nomEntreprise">Nom de l'entreprise</span></br>
                    <i class="fa-solid fa-briefcase"></i>&nbsp;&nbsp;&nbsp;Secteur d'activité<br/>
                    <i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;&nbsp;123 route de la route, 12345 Ville
                </div>
                <div class="col-2 d-none d-md-block">
                    <input type=submit class="bouton" value="supprimer l'entreprise"/>
                </div>
                <div class="col-1 d-block d-md-none">
                <Button class="btn"><img src="../../../ressources/supprimer.png"/></Button>
                </div>
            </div>
            <?php }; ?>
        </div>
    </body>
</html>