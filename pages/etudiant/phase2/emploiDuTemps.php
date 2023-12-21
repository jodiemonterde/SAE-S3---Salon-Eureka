<?php session_start(); 
    require('../../../fonctions/baseDeDonnees.php');
    $pdo = connecteBD();
    if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 2 || $_SESSION['type_utilisateur'] != 'E'){
        header('Location: ../../connexion.php');
    }
    ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="../../../lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/emploiDuTemps.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <title>Planning</title>
    </head>
    <body>
        <!-- Navbar du haut -->
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
        <!-- Contenu principal de la page -->
        <div class="container" id="toPrint">
            <div class="row mx-1">
                <div class="col-12 col-md-8">
                    <p><h2>Vous pouvez consulter votre planning de rendez-vous !</h2></p>
                    <p>Il est désormais trop tard pour demander de nouveaux rendez-vous. </P>
                </div>
                <div class="col-4 d-md-block d-none my-auto text-center" id="element-to-hide" data-html2canvas-ignore="true">
                    <button class="bouton" id="downloadUp">Télécharger l'emploi du temps</button>
                </div>
            </div>
            <?php
                $pdo = connecteBD();
                $planning = planningPerUser($pdo, $_SESSION['idUtilisateur']);
                foreach ($planning as $rdv) {?>
                    <div class="row mx-1">
                        <div class="col-12 rendez-vous ">
                            <p class="text-center"><?php echo htmlspecialchars($rdv['start'])?> - <?php echo htmlspecialchars($rdv['end'])?></p>
                            <p class="text-center text-jaune"><?php echo htmlspecialchars($rdv['company_name']); ?></p>
                        </div>
                    </div>
                <?php }
                $unlistedCompany = unlistedCompanyPerUser($pdo, 1);
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
                            <p class="text-center text-jaune"><?php echo htmlspecialchars($ligne['name']); ?></p>
                        </div>
                    </div>
                <?php } ?>
            <div class="row mx-1 fixed-bottom barre-bas">
                <div class="col-12 d-md-none d-block text-center" id="element-to-hide" data-html2canvas-ignore="true">
                    <button class="bouton boutonTelechargerBas" id="downloadDown">Télécharger l'emploi du temps</button>
                </div>
            </div>
        </div>
        <!-- modal de deconnexion -->
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
                                    <button type="button" data-bs-dismiss="modal" class="bouton">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <a href="../../../fonctions/deconnecter.php"><button type="button" class="bouton" >Se déconnecter </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="../../../js/downloadpage.js"></script>
    </body>
</html>