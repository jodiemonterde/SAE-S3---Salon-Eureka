<?php
    try {
        // Démarrage d'une session
        session_start();

        // Stocke la valeur de $_POST['recherche'] dans $_SESSION['recherche'] si définie
        $_SESSION['recherche'] = isset($_POST['recherche']) ? htmlspecialchars($_POST['recherche']) : ($_SESSION['recherche'] ?? null);
        
        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require("../../../fonctions/baseDeDonnees.php");

        $pdo = connecteBD(); // accès à la Base de données

        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un gestionnaire correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 1 || $_SESSION['type_utilisateur'] != 'E'){
            header('Location: ../../connexion.php');
            exit();
        }

        // Ajout ou suppression de l'entreprise de la liste des souhaits de l'étudiant lors du clic 
        if (isset($_POST["entreprise_id"]) && isset($_POST["mode"])) {
            if (htmlspecialchars($_POST["mode"]) == 'add') {
                addWishStudent($pdo, $_SESSION['idUtilisateur'], htmlspecialchars($_POST["entreprise_id"]));
            } else {
                deleteWishStudent($pdo, $_SESSION['idUtilisateur'], htmlspecialchars($_POST["entreprise_id"]));
            }
            
            header("location: listeEntreprises.php");
            exit();
        }
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
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/listeEntrepriseEtudiant.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <title> Eurêka - Liste des entreprises </title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    <span class="logo">Eureka</span>
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-item-haut nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center inactiveLink"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeRendezVous.php"> Souhaits </a>
                        </li>
                        <li class="nav-item nav-item-haut dropdown p-0 h-100 d-none d-md-block">
                            <!-- Affichage du nom de l'utilisateur -->
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['prenom_utilisateur'] . ' ' .$_SESSION['nom_utilisateur']; ?>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deconnexion"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item nav-item-haut d-md-none d-flex justify-content-end">
                            <a data-bs-toggle="modal" data-bs-target="#deconnexion">
                                <img src="../../../ressources/icone_deconnexion.svg" alt="Se déconnecter" class="logo">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <img src="../../../ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        Entreprises
                    </li>
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
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

        <!-- Container principal de la page -->
        <div class="container mt-2">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12 col-md-6">
                    <!-- Obtention de la date de fin de la prise des rdv afin de la transmettre aux étudiants -->
                    <?php 
                        $date = getDatePeriodEnd($pdo);
                        $dateFinSouhait = $date->fetch();
                    ?>

                    <h2>Prenez rendez-vous avec les entreprises qui vous correspondent.</h2>
                    <p>Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le <?php  echo $dateFinSouhait["dateFin"]; ?>, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. Si vous souhaitez annuler l'un de vos rendez-vous, il suffit de cliquer à nouveau.</p>
                </div>

                <!-- Formulaire permettant d'entrer une recherche personnalisé qui filtrera l'affichage selon celle-ci -->
                <form action="listeEntreprises.php" method="post" class="col-12 col-md-6 my-2">
                    <div class="row">
                        <div class="col-12 col-md-7 p-0">
                            <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher une entreprise" class="entreeUtilisateur"/>    
                        </div>
                        <div class="col-5 d-none d-md-block">
                            <input type="submit" class="bouton" value="Rechercher"/>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Affichage de la liste des entreprises qui s'intéressent à la filière de cet étudiant -->
            <div class="row mx-1">
                <?php 
                $pdo = connecteBD(); // accès à la Base de données
                $stmt = getEntreprisesForStudent($pdo, $_SESSION['idUtilisateur'], $_SESSION['recherche']); // Obtention des entreprises à afficher en fonction de la filière de l'étudiant, mais également de la recherche potentielle de l'utilisateur
                
                if ($stmt->rowCount() === 0) { // Vérification du nombre d'entreprises trouvées et gestion de l'affichage en fonction 
                    echo '<h2>Aucune entreprise trouvée avec cette recherche.</h2>';
                } else {
                    while ($ligne = $stmt->fetch()) {
                ?>

                <!-- Formulaire permettant de cliquer sur une entreprise afin de l'ajouter ou de la supprimer de sa liste de souhaits -->
                <form action="listeEntreprises.php" method="post">
                    <button type="submit" class="col-12 pb-0 company dl-search-result-title-container <?php echo $ligne['wish'] != null ? 'inWishList' : 'notInWishList';?>">
                        
                            <input type="hidden" name="entreprise_id" value="<?php echo $ligne['company_id']?>"/>
                            <input type="hidden" name="mode" value="<?php if ($ligne['wish'] != null) { echo 'delete';} else { echo 'add';}?>"/>                                         
                                    <div class="profil-det-img d-flex">
                                        <div class="dp">
                                        <img class="logoEntrerise" src="../../../ressources/logosentreprises/<?php echo $ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "no-photo.png"?>" alt="">
                                        </div>
                                        <div class="pd">
                                            <h2 class="text-accent"><?php echo $ligne["name"]?></h2>
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


        <!-- Contenu de la modale de deconnexion permettant de se déconnecter et de retourner à la page d'accueil. -->
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