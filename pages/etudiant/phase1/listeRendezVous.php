<?php
    try {
        // Démarrage d'une session
        session_start();

        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require('../../../fonctions/baseDeDonnees.php');

        $pdo = connecteBD(); // accès à la Base de données

        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un étudiant correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) === 2 || $_SESSION['type_utilisateur'] != 'E'){
            header('Location: ../../connexion.php');
            exit();
        }

        // Suppression de l'entreprise de la liste des souhaits de l'étudiant si le formulaire de cette entreprise spécifique a été cliqué
        if (isset($_POST["entreprise_id"])) {
            removeWishStudent($pdo, $_SESSION['idUtilisateur'], $_POST["entreprise_id"]);
        }

        $stmt = getEntreprisesPerStudent($pdo, $_SESSION['idUtilisateur']); // Obtention des entreprises avec lesquelles l'étudiant souhaite prendre rendez-vous
        $phase = getPhase($pdo); // Obtention de la phase en cours
    }catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/listeRendezVous.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="../../../js/downloadPage.js"></script>
        
        <title>Eurêka - Liste des souhaits</title>
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
                            <!-- Affichage des éléments de navigation de la navbar du haut uniquement si la phase est à 1. Permet d'empêcher la navigation et donc de se diriger vers la liste des entreprises si l'emploi du temps est en cours de création (phase 1.5). -->
                            <?php
                            if ($phase === 1) {
                            ?>
                            <!-- Si sur la liste des entreprises, mettre en actif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEntreprises.php"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center inactiveLink"> Souhaits </a>
                        </li>
                        <li class="nav-item nav-item-haut dropdown p-0 h-100 d-none d-md-block">
                            <!-- Affichage du nom de l'utilisateur -->
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['prenom_utilisateur'] . ' ' .$_SESSION['nom_utilisateur']; ?>
                            </a>
                            <?php } ?>
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

        <!-- Affichage de la navbar du bas uniquement si la phase est à 1. Permet d'empêcher la navigation et donc de se diriger vers la liste des entreprises si l'emploi du temps est en cours de création. -->
        <?php
        if ($phase === 1) {
        ?>
        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <a class="d-flex justify-content-center" href="listeEntreprises.php">
                            <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="listeEntreprises.php">
                            Entreprises
                        </a>
                    </li>
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <img src="../../../ressources/icone_rdv_white.svg" alt="Mes rendez-vous" class="icone">
                        </a>
                        Souhaits
                    </li>
                </ul>
            </div>
        </nav>
        <?php
        }
        ?>

        <!-- Container principal de la page -->
        <div class="container">
            <div class="row mx-1">
                <div class="col-12">
                    <p><h2>Vos demandes de rendez-vous</h2></p>
                    <!-- Affichage d'un message différent selon la phase -->
                    <p><?php echo $phase === 1 ? "Au terme de la phase de prise de rendez-vous, Eureka vous proposera un planning avec les entreprises suivantes. Il est encore tout à fait temps de changer d’avis ! " : "La période de prise de rendez-vous est désormais terminé, vous pouvez seulement consulter vos souhaits le temps que le planning soit générée"; ?></P>
                </div>
            </div>
            <?php
            $vide = true; // Variable permettant de vérifier si l'étudiant a émis des souhaits et affichage d'un message en conséquence
            while ($ligne = $stmt->fetch()) { 
            $vide = false;?>
            <div class="row entreprise align-items-center">
                <div class="col-2 col-md-1">
                    <img src="../../../ressources/logosentreprises/<?php echo $ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "no-photo.png"?>" alt="logo" class="logoEntreprise" width="75px" height="75px"/>
                </div>
                <div class="col-8 col-md-6 col-lg-8 colEntreprise">
                    <span class="text-accent"><?php echo $ligne["name"]?></span></br>
                    <i class="fa-solid fa-briefcase"></i>&nbsp;&nbsp;&nbsp;<?php echo $ligne["sector"]?><br/>
                    <i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ligne["address"]?>
                </div>
                <div class="col-2 d-none p-0 m-0 d-md-block">
                    <?php if ($phase === 1) { ?><input type="button" class="bouton" value="supprimer l'entreprise" data-bs-toggle="modal" data-bs-target="#modal"/> <?php } ?>
                </div>
                <div class="col-1 d-block d-md-none">
                    <?php if ($phase === 1) { ?><input type="button" class="boutonSupprimerMd"data-bs-toggle="modal" data-bs-target="#modal"/><?php } ?>
                </div>

                <!-- Contenu de la modale de deconnexion permettant de suppression d'une entreprise -->
                <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Êtes-vous sûr(e) de vouloir supprimer cette entreprise de votre liste de souhaits ?
                            </div>
                            <div class="modal-footer">
                                <form action="listeRendezVous.php" method="post">
                                    <input type="hidden" name="entreprise_id" value="<?php echo $ligne["company_id"]?>"/>
                                    <input type="submit" class="bouton confirmation" value="Oui"/>
                                    <input type="button" class="boutonNegatif confirmation" data-bs-dismiss="modal" value="Non"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php };
            // Affichage d'un message à la place de la liste des entreprises s'il n'y a aucune entreprise
            if ($vide) { ?>
                <div class="row">
                    <div class="col-12">
                        <p class="erreur">Vous n'avez pas encore demandé de rendez-vous !</p>
                    </div>
                </div>
            <?php } ?>
        </div>

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