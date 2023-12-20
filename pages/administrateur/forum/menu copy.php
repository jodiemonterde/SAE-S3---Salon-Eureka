<?php 
    session_start();
    require("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
    $infoForum = infoForum($pdo); 
?>
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
        <h1 class="text-center"> navbar </h1>
        <div class="container">
            <div class="row mx-1">
                <div class=" col-12 text-center formulaire">
                    <div class="row">
                        <div class="col-12">
                            <label for="date">Date du forum :</label><br/>
                            <input type="date"   name="dateForum">
                        </div>
                        <div class="col-12">
                            <label for="date">Heure de début du forum :</label><br/>
                            <input type="time" name="heureDebut">
                        </div>
                        <div class="col-12">
                            <label for="date">Heure de fin du forum :</label><br/>
                            <input type="time" name="heureFin">
                        </div>
                        <div class="col-12">
                            <label for="date">durée par défaut d'un rendez-vous :</label><br/>
                            <input type="time" name="duree">
                        </div>
                        <div class="col-12">
                            <label for="date">durée secondaire d'un rendez-vous :</label><br/>
                            <input type="time" name="secDuree">
                        </div>
                        <div class="col-12">
                            <label for="date">Date limite avant la création du planning :</label><br/>
                            <input type="date" name="dateLim" >
                        </div>
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3 col-6 ">
                                <button type="submit">Annuler</button>
                            </div>
                            <div class="col-md-3 col-6 ">
                                <button type="submit">Valider</button>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-center">
                    <button class="bouton">Génerer le planning </button>
                </div>
                <div class="col-12 text-center">
                    <button class="bouton">Réinitialiser les données</button>
                </div>
            </div>
        </div>
    </body>
</html>