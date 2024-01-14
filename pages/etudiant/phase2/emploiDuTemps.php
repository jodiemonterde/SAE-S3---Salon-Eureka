<?php 
    try {
        // Démarrage d'une session
        session_start(); 

        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require('../../../fonctions/baseDeDonnees.php');
        require('../../../fonctions/fonctionsImportationExportation.php');

        $pdo = connecteBD(); // accès à la Base de données

        if (isset($_POST["download"])) {
            exportEtudiant($_SESSION['idUtilisateur'], $pdo);
        }
        
        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un étudiant correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 2 || $_SESSION['type_utilisateur'] != 'E'){
            header('Location: ../../connexion.php');
            exit();
        }

        $planning = planningPerUser($pdo, $_SESSION['idUtilisateur']); // Obtention de l'emploi du temps de l'utilisateur connecté
        $unlistedCompany = unlistedCompanyPerUser($pdo, $_SESSION['idUtilisateur']); // Obtention des entreprises ne rentrant pas dans l'emploi du temps
    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
        header('Location: ../../maintenance.php');
        exit();
    }
    ?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Métadonnées et liens vers les feuilles de style -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/emploiDuTemps.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        
        <title>Eurêka - emploi du temps</title>
    </head>
    <body>
        <!-- Navbar du haut (pas de navbar du bas sur cette page !) -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    <span class="logo">Eureka</span>
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-item-haut dropdown p-0 h-100 d-none d-md-block">
                            <!-- Affichage du nom de l'utilisateur -->
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['prenom_utilisateur'] . ' ' .$_SESSION['nom_utilisateur']; ?>
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

        <!-- Contenu principal de la page -->
        <div class="container">
            <div class="row mx-1">
                <div class="col-12 col-md-8">
                    <p><h2>Vous pouvez consulter votre planning de rendez-vous !</h2></p>
                    <p>Il est désormais trop tard pour demander de nouveaux rendez-vous. </P>
                </div>
                <!-- Permet de télecharger sous format pdf le planning d'un étudiant -->
                <div class="col-4 d-md-block d-none my-auto text-center">
                    <form action="emploiDuTemps.php" method="post">
                        <input type="hidden" name="download" value="1">
                        <input type="submit" class="bouton" value="Télécharger l'emploi du temps">
                    </form>
                </div>
            </div>

            <!-- Affichage de tous les rendez-vous qui rentrent dans l'emploi du temps  -->
            <?php
                foreach ($planning as $rdv) {?>
                    <div class="row mx-1">
                        <div class="col-12 rendez-vous ">
                            <p class="text-center"><?php echo htmlspecialchars($rdv['start'])?> - <?php echo htmlspecialchars($rdv['end'])?></p>
                            <p class="text-center text-accent"><?php echo htmlspecialchars($rdv['company_name']); ?></p>
                        </div>
                    </div>
                <?php }

                // Affichage de tous les rendez-vous qui ne rentrent pas dans l'emploi du temps
                if ($unlistedCompany->rowCount() > 0) {?>
                    <div class="row mx-1">
                        <div class="col-12">
                            <p><h2>Consulter les rendez-vous non planifiables</h2></p>
                            <p>Attention, certaines entreprises que vous souhaitiez voir ont reçues trop de demandes : ils n’ont pas pu être intégrés à votre emploi du temps. Si vous souhaitez obtenir un rendez-vous avec eux, il va falloir les contacter directement. </P>
                        </div>
                    </div>
                <?php }
                while ($ligne = $unlistedCompany->fetch()) {?>
                    <div class="row mx-1">
                        <div class="col-12 rendez-vous">
                            <p class="text-center text-accent"><?php echo htmlspecialchars($ligne['name']); ?></p>
                        </div>
                    </div>
                <?php } ?>

                <!-- Bouton de téléchargement de l'emploi du temps en pdf (en format téléphone) -->
            <div class="row mx-1 fixed-bottom barre-bas">
                <div class="col-12 d-md-none d-block text-center">
                    <form action="emploiDuTemps.php" method="post">
                        <input type="hidden" name="download" value="">
                        <input type="submit" class="bouton boutonTelechargerBas" value="Télécharger l'emploi du temps">
                    </form>
                </div>
            </div>
        </div>


        <!-- Contenu de la modale de deconnexion permettant de se déconnecter et de retourner à la page d'accueil. -->
        <div class="modal fade " id="deconnexion" tabindex="-1" aria-labelledby="Sedeconnecter" aria-hidden="true">
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
                                    <button type="button" data-bs-dismiss="modal" class="bouton boutonDeconnexion">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <a href="../../../fonctions/deconnecter.php"><button type="button" class="bouton boutonDeconnexion" >Se déconnecter </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>