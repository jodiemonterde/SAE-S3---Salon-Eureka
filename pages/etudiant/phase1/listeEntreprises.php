<?php
    try {
        session_start();
        // Stocke la valeur de $_POST['recherche'] dans $_SESSION['recherche'] si définie
        $_SESSION['recherche'] = $_POST['recherche'] ?? $_SESSION['recherche'] ?? null;
        require("../../../fonctions/baseDeDonnees.php");
        $pdo = connecteBD();
        if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 1 || $_SESSION['type_utilisateur'] != 'E'){
            header('Location: ../../connexion.php');
            exit();
        }
        if (isset($_POST["entreprise_id"]) && isset($_POST["mode"])) {
            if ($_POST["mode"] == 'add') {
                addWishStudent($pdo, $_SESSION['idUtilisateur'], $_POST["entreprise_id"]);
            } else {
                deleteWishStudent($pdo, $_SESSION['idUtilisateur'], $_POST["entreprise_id"]);
            }
            
            header("location: listeEntreprises.php");
            exit();
        }
    } catch (Exception $e) {
        header('Location: ../../maintenance.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/listeEntrepriseEtudiant.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
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
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center inactiveLink"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeRendezVous.php"> Souhaits </a>
                        </li>
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
                                <img src="../../../ressources/icone_deconnexion.png" alt="Se déconnecter">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item d-flex flex-column text-center actif_bas">
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <img src="../../../ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        Entreprises
                    </li>
                    <li class="nav-item d-flex flex-column text-center inactif_bas_texte">
                        <a class="d-flex justify-content-center" href="listeRendezVous.php">
                            <img src="../../../ressources/icone_rdv_black.svg" alt="Mes rendez-vous" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="listeRendezVous.php">
                            Rendez-vous
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container mt-2">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12 col-md-6">
                    <h2>Prenez rendez-vous avec les entreprises qui vous correspondent.</h2>
                    <p>Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le XX mois, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. Si vous souhaitez annuler l'un de vos rendez-vous, il suffit de cliquer à nouveau.</p>
                </div>
                <form action="listeEntreprises.php" method="post" class="col-12 col-md-6 my-2">
                    <div class="row">
                        <div class="col-8">
                            <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher une entreprise" class="entreeUtilisateur"/>    
                        </div>
                        <div class="col-4">
                            <input type="submit" class="bouton" value="Rechercher"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row mx-1">
                <?php 
                $pdo = connecteBD();
                $stmt = getEntreprisesForStudent($pdo, $_SESSION['idUtilisateur'], $_SESSION['recherche']);
                if ($stmt->rowCount() === 0) {
                    echo '<h2>Aucune entreprise trouvée avec cette recherche.</h2>';
                } else {
                    while ($ligne = $stmt->fetch()) {
                ?>
                <form action="listeEntreprises.php" method="post">
                    <button type="submit" class="col-12 pb-0 company dl-search-result-title-container <?php echo $ligne['wish'] != null ? 'inWishList' : 'notInWishList';?>">
                        
                            <input type="hidden" name="entreprise_id" value="<?php echo $ligne['company_id']?>"/>
                            <input type="hidden" name="mode" value="<?php if ($ligne['wish'] != null) { echo 'delete';} else { echo 'add';}?>"/>                                         
                                    <div class="profil-det-img d-flex">
                                        <div class="dp">
                                        <img class="logoEntrerise" src="../../../ressources/logosentreprises/<?php echo htmlspecialchars($ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "no-photo.png")?>" alt="">
                                        </div>
                                        <div class="pd">
                                            <h2 class="text-jaune"><?php echo $ligne["name"]?></h2>
                                            <ul class="listeEntreprise">
                                                <li><i class="fa-solid fa-briefcase"></i> <?php echo $ligne["sector"]?></li>
                                                <li><i class="fa-solid fa-location-dot"></i>  <?php echo $ligne["address"]?></li>
                                            </ul>
                                        </div>
                                    </div>
                                
                            <hr>
                            <div class="row">
                            <div class="col-12 pb-2">
                                <?php echo $ligne["description"]?>
                            </div>
                            <?php if ($ligne['wish'] != null) { ?>
                                <div class="textInWishList">
                                    Vous souhaitez prendre rendez-vous avec cette entreprise !
                                </div>
                            <?php } ?>
                        </div>
                    </button>
                </form>
                <?php   }
                    }
                ?>
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
                                    <button type="button" data-bs-dismiss="modal" class="bouton boutonDeconnexion">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <a href="../../../fonctions/deconnecter.php"><button type="button" class="bouton boutonDeconnexion">Se déconnecter </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>