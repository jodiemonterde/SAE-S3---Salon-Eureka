<?php
    session_start();
    $user = 1;
    $field_id = 1;
    include("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./listeEntreprise.css">
    <title>Eureka - Liste des entreprises</title>
</head>
<body>
     <!-- Navbar du haut -->
     <nav class="navbar navbar-expand sticky-top border bg-white">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <img src="./ressources/logo_black.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="navbar-right">
                    <ul class="navbar-nav">
                        <li class="nav-item d-none d-md-block">
                            <!-- Si sur la liste des entreprises, mettre en jaune -->
                            <a class="actif_haut btn" role="button" href="#">
                                Liste des entreprises
                            </a>
                        </li>
                        <li class="nav-item d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                            <a class="inactif_haut btn" role="button" href="#">
                                Mes rendez-vous
                            </a>
                        </li>

                        <li class="nav-item d-none d-md-block dropdown">
                            <a class="inactif_haut btn dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-md-none">
                            <a href="#">
                                <img src="./ressources/icone_deconnexion.png" alt="Se déconnecter">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    <div class="container">
        <div class="row d-flex align-items-center h-100">
            <div class="form-outline order-md-2 col-md-4 col-12 order-1 align-middle" data-mdb-input-init>
                <input type="search" name="motDePasse" value="" placeholder=" &#xf002 Rechercher une entreprise" class="form-control zoneText"/>    
            </div>
            <div class="searchButton order-3 d-none d-md-block col-md-2"><button class="bouton">Rechercher</button></div>
            <div class="col-md-6 col-12 order-md-1 order-2">
                <h2> Prenez rendez-vous avec les entreprises qui vous correspondent. </h2>
                <p> Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le XX mois, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. </p>
            </div>
        </div>
        <?php
            $stmt = getEntreprisesPerField($pdo, $field_id);
            while ($ligne = $stmt->fetch()) { 
        ?>
        <!-- Accordéon Bootstrap -->
        <div class="accordion" id=<?php echo '"companyAccordion'.$ligne['company_id'].'"'?>>
            <div class="card">
                <div class="card-header bg-white" id=<?php echo '"heading'.$ligne['company_id'].'"'?>>
                    <h2 class="mb-0 d-flex">
                        <button class="btn btn-link text-start d-flex flex-fill align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['company_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['company_id']?>">
                            <div class="profil-det-img d-flex text-start">
                                <div class="dp"><img src="../../../ressources/no-photo.png" alt=""></div>
                                <div class="pd">
                                    <h2><?php echo $ligne["name"]?></h2>
                                    <ul class="text-left">
                                        <li><i class="fa-solid fa-briefcase text-left"></i> <?php echo $ligne["sector"]?></li>
                                        <li><i class="fa-solid fa-location-dot"></i> <?php echo $ligne["address"]?></li>
                                    </ul>
                                </div>
                            </div>
                            <img src="../../../ressources/arrow.png" alt="Flèche" class="toggle-image d-none d-md-block">
                        </button> 
                    </h2>
                </div>
                <div id=<?php echo '"collapse'.$ligne['company_id'].'"'?> class="collapse" aria-labelledby=<?php echo '"heading'.$ligne['company_id'].'"'?> data-bs-parent=<?php echo '"#companyAccordion'.$ligne['company_id'].'"'?>>
                    <div class="card-body">
                        <!-- Contenu de l'accordéon -->
                        <div class="row">
                            <div class="description"><?php echo $ligne["description"]?></div>
                            <?php
                            $stmtEtudiant = getStudentsPerCompany($pdo, $ligne["company_id"]);
                            while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                            ?>
                            <hr>
                            <h2 class="student"><?php echo $ligneEtudiant["username"]?></h2>
                            <p><?php echo $ligneEtudiant["name"]?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

     <!-- Navbar du bas -->
     <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item actif_bas_texte">
                        <!-- Si sur la liste des entreprises, mettre l'icone blanche et le fond en jaune -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="#">
                            <img src="./ressources/entreprise_white.png" alt="Liste des entreprises">
                        </a>
                        <!-- Si sur la liste des entreprises, mettre en jaune -->
                        Entreprises
                    </li>
                    <li class="nav-item inactif_bas">
                        <!-- Si sur la liste des rendez-vous, mettre l'icone blanche et le fond en jaune -->
                        <a class="d-flex justify-content-center" href="#">
                            <img src="./ressources/rendez-vous_black.png" alt="Mes rendez-vous">
                        </a>
                        <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                        Rendez-vous
                    </li>
                </ul>
            </div>
        </nav>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    
</body>
</html>
