<?php session_start(); 
      require('../../../fonctions/baseDeDonnees.php');
      //$pdo = connecteBD();?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../css/emploiDuTemps.css">
        
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="../../../lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <link rel="stylesheet" href="../../../css/navbars.css">
        <title>Planning</title>
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
                $planning = planningPerUser($pdo, 2);
                foreach ($planning as $rdv) {?>
                    <div class="row mx-1">
                        <div class="col-12 rendez-vous ">
                            <p class="text-center"><?php echo $rdv['start']?> - <?php echo $rdv['end']?></p>
                            <p class="text-center text-jaune"><?php echo $rdv['company_name']; ?></p>
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
                            <p class="text-center text-jaune"><?php echo $ligne['name']; ?></p>
                        </div>
                    </div>
                <?php } ?>
            <div class="row mx-1">
                <div class="col-12 d-md-none d-block text-center" id="element-to-hide" data-html2canvas-ignore="true">
                    <button class="bouton boutonDeconnecterBas" id="downloadDown">Télécharger l'emploi du temps</button>
                </div>
            </div>
        </div>
        <?php if (isset($_POST['telecharger'])) {?>
            
        <?php } ?>
    </body>
    <script src="../../../js/downloadpage.js"></script>
</html>