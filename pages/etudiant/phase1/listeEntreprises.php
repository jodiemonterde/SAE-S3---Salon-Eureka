<?php 
    session_start();
    $user = 1;
    include("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
    if (isset($_POST["entreprise_id"])) {
        removeWishStudent($pdo, $user, $_POST["entreprise_id"]);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="../../../css/listeEntreprise.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="./lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        
        <title> Eureka - Liste des entreprises </title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.png" alt="Logo Eureka" class="logo me-2">
                    Eureka
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center inactiveLink"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeRendezVous.php"> Mes rendez-vous </a>
                        </li>
                        <li class="nav-item dropdown p-0 h-100 d-none d-md-block">
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item" href="#"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-md-none d-flex justify-content-end">
                            <a href="#">
                                <img src="../../../ressources/icone_deconnexion.png" alt="Se déconnecter">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row d-flex align-items-center h-100">
                <div class="form-outline order-md-2 col-md-4 col-12 order-1 align-middle" data-mdb-input-init>
                    <input type="search" id="recherche" class="form-control" placeholder="&#xf002 Rechercher une entreprise" aria-label="Search" />    
                </div>
                <div class="searchButton order-3 d-none d-md-block col-md-2""><button>Rechercher</button></div>
                <div class="col-md-6 col-12 order-md-1 order-2">
                    <h2> Prenez rendez-vous avec les entreprises qui vous correspondent. </h2>
                    <p> Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le XX mois, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. </p>
                </div>
            </div>
            <div class="row mx-1">
                <?php 
                $pdo = connecteBD();
                $stmt = getEntreprisesForStudent($pdo, $user);
                while ($ligne = $stmt->fetch()) { 
                ?>
                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2><?php echo $ligne["name"]?></h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i><?php echo $ligne["sector"]?></li>
                                            <li><i class="fa-solid fa-location-dot"></i> <?php echo $ligne["address"]?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                        <?php echo $ligne["description"]?>
                        </div>
                    </div>
                </div>
                <?php }?>
                
    </body>
</html>