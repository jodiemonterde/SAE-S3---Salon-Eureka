<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.2.1-web/css/all.css">
        <link rel="stylesheet" href="jsp.css">
        <title> Eureka - Liste des entreprises </title>
    </head>
    <body>
        <?php
            // Récupérer le nom de la page
            $currentPage = basename(__FILE__);
        ?>

        <div class="navbar navbar-default sticky-top d-none d-sm-block bg-secondary" role="navigation">
            <div class="container">
                <div class="navbar-brand">
                    Logo Eureka
                </div>
                <div class="navbar-right">
                    <input type="submit" class="btn btn-light" value="Logo Bouton Déconnexion">
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <input type="search" id="recherche" placeholder="Rechercher une entreprise"/>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2> Prenez rendez-vous avec les entreprises qui vous correspondent. </h2>
                    <p> Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le XX mois, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="row">
                        <div class="col-4">
                            <div class="bg-secondary rounded-circle"> <p> Image de l'entreprise </p> </div>
                        </div>
                        <div class="col-8">
                            <h2> Nom de l'entreprise </h2>
                            <h4> Logo Secteur d'activité </h4>
                            <h4> Logo Adresse de l'entreprise </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <p> Description de l'entreprise </p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="row">
                        <div class="col-4">
                            <div class="bg-secondary rounded-circle"> <p> Image de l'entreprise </p> </div>
                        </div>
                        <div class="col-8">
                            <h2> Nom de l'entreprise </h2>
                            <h4> Logo Secteur d'activité </h4>
                            <h4> Logo Adresse de l'entreprise </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <p> Description de l'entreprise </p>
                        </div>
                    </div>
                </div><div class="col-12 col-sm-6 col-md-4">
                    <div class="row">
                        <div class="col-4">
                            <div class="bg-secondary rounded-circle"> <p> Image de l'entreprise </p> </div>
                        </div>
                        <div class="col-8">
                            <h2> Nom de l'entreprise </h2>
                            <h4> Logo Secteur d'activité </h4>
                            <h4> Logo Adresse de l'entreprise </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 text-center">
                            <p> Description de l'entreprise </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-3">
                <input type="submit" class="btn btn-light" value="Logo Bouton Entreprises">
            </div>
            <div class="col-2"></div>
            <div class="col-3">
                <input type="submit" class="btn btn-light" value="Logo Bouton Rendez-vous">
            </div>
            <div class="col-2"></div>
        </div>
    </div>

    <div class="navbar navbar-default sticky-bottom d-sm-none bg-secondary" role="navigation">
            <div class="container">
                <div class="navbar-brand">
                    Logo Eureka
                </div>
                <div class="navbar-right">
                    <input type="submit" class="btn btn-light" value="Logo Bouton Déconnexion">
                </div>
            </div>
        </div>
</html>