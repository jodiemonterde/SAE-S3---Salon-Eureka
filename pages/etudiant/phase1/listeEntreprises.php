<?php 
    session_start();
    if(!isset($_SESSION['idUtilisateur'])){
        header('Location: ../../connexion.php');
    }
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
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
        <link rel="stylesheet" href="./listeEntreprise.css">
        <title> Eureka - Liste des entreprises </title>
    </head>
    <body>
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
                        <?php echo htmlspecialchars($ligne["description"])?>
                    </div>
                </div>
            </div>
                <?php }?>
        </div>
    </body>
</html>