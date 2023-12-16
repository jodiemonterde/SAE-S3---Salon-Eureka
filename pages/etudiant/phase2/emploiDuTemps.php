<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../css/emploiDuTemps.css">
        
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.2.1-web/css/all.css">
        
        <script src="../../../lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <link rel="stylesheet" href="../../../css/navbars.css">
        <title>pas dev</title>
    </head>
    <body>
        <nav class="navbar navbar-expand sticky-top border bg-white">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <img src="../../../ressources/logo_black.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="navbar-right">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown p-2 d-none d-sm-block fond_inactif_haut">
                            <a class="dropdown-toggle lien couleur_inactif_haut" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item" href="../../deconnexion.php"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-sm-none">
                            <a href="../../deconnexion.php">
                                <img src="../../../ressources/icone_deconnexion.png" alt="Se déconnecter">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php
            require('../../../fonctions/baseDeDonnees.php');
            $pdo = connecteBD();
            $planning = planningPerUser($pdo, 1);
            echo "<h1>Planning</h1>";
            foreach ($planning as $rdv) {
                echo $rdv['start'] . ' ' . $rdv['company_name'] . ' ' . $rdv['end'] . '<br>';
            }
            echo '<h1>Entreprises non listées</h1>';
            $unlistedCompany = unlistedCompanyPerUser($pdo, 1);
            while ($ligne = $unlistedCompany->fetch()) {
                echo $ligne['name'] . '<br>';
            }
        ?> 

    </body>
</html>