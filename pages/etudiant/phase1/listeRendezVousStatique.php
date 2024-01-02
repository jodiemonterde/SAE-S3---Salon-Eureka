<?php 
    session_start();
    require('../../../fonctions/baseDeDonnees.php');
    $pdo = connecteBD();
    if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 1.5 || $_SESSION['type_utilisateur'] != 'E'){
        header('Location: ../../connexion.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../lib/fontAwesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="../../../lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <script src="../../../js/downloadPage.js"></script>
        <link rel="stylesheet" href="../../../css/navbars.css">
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/listeRendezVousStatique.css">
        <title>Eureka - Liste des shouaits</title>
    </head>
    <body>
        <!-- barre de nvaigation du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    Eureka
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item dropdown p-0 h-100 d-none d-md-block">
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION['nom_utilisateur'])?>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deconnexion"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-md-none d-flex justify-content-end">
                            <a data-bs-toggle="modal" data-bs-target="#deconnexion">
                                <img src="../../../ressources/icone_deconnexion.svg" alt="Se déconnecter" class="logo">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Contenu principal -->
        <div class="container">
            <div class="row mx-1">
                <div class="col-12">
                    <p><h2>Période de prise de rendez-vous terminer ! </h2></p>
                    <p>Il n'est plus possible de prendre de rendez-vous ! En attendant que le planning soit réalisé voici la liste de vos shouaits</P>
                </div>
            </div>
            <!-- Button trigger modal -->
            <?php
            $pdo = connecteBD();
            $stmt = getEntreprisesPerStudent($pdo, $_SESSION['idUtilisateur']);
            $vide = true;
            while ($ligne = $stmt->fetch()) { 
            $vide = false;?>
            <div class="row entreprise align-items-center ">
                <div class="col-2 col-md-1">
                    <img src="../../../ressources/<?php echo htmlspecialchars($ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "no-photo.png")?>" alt="logo" class="logoEntreprise" width="75px" height="75px"/>
                </div>
                <div class="col-9 col-md-10 colEntreprise">
                    <span class="text-jaune"><?php echo htmlspecialchars($ligne["name"])?></span></br>
                    <i class="fa-solid fa-briefcase"></i>&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($ligne["sector"])?><br/>
                    <i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($ligne["address"])?>
                </div>
            </div>
            <?php };
            if ($vide) { ?>
                <div class="row">
                    <div class="col-12">
                        <p class="erreur">Vous n'avez demander aucun rendez-vous !</p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- Modal pour la deconnexion-->
        <div class="modal fade" id="deconnexion" tabindex="-1" aria-labelledby="Sedeconnecter" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header deco">
                        <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class = "row">
                                <div class="col-12">
                                    <h1 class="text-center" id="Sedeconnecter">DÉCONNEXION</h1>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-12">
                                    <P class="text-center">Êtes-vous sûr(e) de vouloir vous déconnecter ?</P>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-dismiss="modal" class="bouton">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <a href="../../../fonctions/deconnecter.php"><button type="button" class="bouton">Se déconnecter </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>