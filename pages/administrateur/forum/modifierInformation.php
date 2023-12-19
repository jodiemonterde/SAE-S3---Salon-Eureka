<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../../outils/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../../outils/fontawesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="forum.css">
        <title>informations eureka</title>
    </head>
    <body>
        <h1 class="text-center">Informations sur Eureka </h1>
        <div class="container">
            <div class="row">
                <div class="col-md-3"></div>
                <div class="col-md-6 col-12">
                    <div class="row">
                        <div class="col-12">
                            <label for="date">Date du forum :</label><br/>
                            <input type="date" name="dateForum" placeholder="Choisir la date du forum">
                        </div>
                        <div class="col-12">
                            <label for="date">Heure de début du forum :</label><br/>
                            <input type="time" name="heureDebut" placeholder="Selectionner heure de début">
                        </div>
                        <div class="col-12">
                            <label for="date">Heure de fin du forum :</label><br/>
                            <input type="time" name="heureFin" placeholder="Selectionner heure de fin">
                        </div>
                        <div class="col-12">
                            <label for="date">durée par défaut d'un rendez-vous :</label><br/>
                            <input type="time" name="duree" placeholder="Selectionner durée par défaut">
                        </div>
                        <div class="col-12">
                            <label for="date">durée secondaire d'un rendez-vous :</label><br/>
                            <input type="time" name="secDuree" placeholder="Selectionner 2nde durée">
                        </div>
                        <div class="col-12">
                            <label for="date">Date limite avant la création du planning :</label><br/>
                            <input type="date" name="dateLim" placeholder="Selectionner date limite">
                        </div>
                        <div class="row">
                            <div class="col-3 ">
                                <button type="submit">Annuler</button>
                            </div>
                            <div class="col-3 ">
                                <button type="submit">Valider</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3"></div>
            </div>
        </div>
    </body>
</html>