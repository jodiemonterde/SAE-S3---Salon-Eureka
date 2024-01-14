<?php 
    try {
        // Démarrage d'une session
        session_start();

        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require("../../../fonctions/baseDeDonnees.php");

        $pdo = connecteBD(); // accès à la Base de données

        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un administrateur correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A'){
            header('Location: ../../connexion.php');
            exit();
        }

        if(isset($_POST['dateForum']) && isset($_POST['heureDebut']) && isset($_POST['heureFin']) && isset($_POST['duree']) && isset($_POST['secDuree']) && isset($_POST['dateLim'])){
            updateForum($pdo, htmlspecialchars($_POST['dateForum']), htmlspecialchars($_POST['heureDebut']), htmlspecialchars($_POST['heureFin']), htmlspecialchars($_POST['duree']), htmlspecialchars($_POST['secDuree']), htmlspecialchars($_POST['dateLim']));
        }
        if(isset($_POST['confirmation']) && $_POST['confirmation'] == "Je reinitialise les données du site"){
            reinitialiserDonnees($pdo);
        }
        $infoForum = infoForum($pdo);
        $phase = getPhase($pdo);
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
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <link rel="stylesheet" href="../../../css/forum.css">
        <title>Eurêka - paramétrage du forum</title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logoDisplay me-2">
                    <span class="logoDisplay">Eureka</span>
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-item-haut nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeEntreprises.php"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeEtudiants.php"> Étudiants </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeAdministrateurs.php"> Administrateurs </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"  href="../listeGestionnaires.php"> Gestionnaires </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block lien_inactif">
                            <!-- Si sur les paramètres du forum, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Forum </a>
                        </li>
                        <li class="nav-item nav-item-haut dropdown p-0 h-100 d-none d-md-block">
                            <!-- Affichage du nom de l'utilisateur -->
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $_SESSION['prenom_utilisateur'] . ' ' . $_SESSION['nom_utilisateur']?>
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
                <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone" href="../listeEntreprises.php">
                        <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeEntreprises.php">
                        Entreprises
                    </a>
                </li>
                <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone" href="../listeEtudiants.php">
                        <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeEtudiants.php">
                        Etudiants
                    </a>
                </li>
                <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="../listeAdministrateurs.php">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeAdministrateurs.php">
                        Admins
                    </a>
                </li>
                <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="../listeGestionnaires.php">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeGestionnaires.php">
                        Gestionnaires
                    </a>
                </li>
                <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center actif_bas_texte actif_bas_texte_admin">
                    <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center actif_bas_icone">
                        <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_forum_white.svg" alt="Paramètres du forum" class="icone_admin">
                    </a>
                    Forum
                </li>
            </ul>
        </nav>

        <!-- Container principal de la page -->        
        <div class="container">
            <div class="row mx-1">
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center formulaire">
                    <form action="menu.php" method="POST">
                        <?php $ligne = $infoForum->fetch();?>
                        <div class="row p-0">
                            <div class="col-12">
                                <label for="dateForum">Date du forum :</label><br/>
                                <input type="date" value=<?php echo $ligne['date'];?>   name="dateForum">
                            </div>
                            <div class="col-12">
                                <label for="heureDebut">Heure de début du forum :</label><br/>
                                <input type="time" value=<?php echo $ligne['start'];?> name="heureDebut">
                            </div>
                            <div class="col-12">
                                <label for="heureFin">Heure de fin du forum :</label><br/>
                                <input type="time" value=<?php echo $ligne['end'];?> name="heureFin">
                            </div>
                            <div class="col-12">
                                <label for="duree">durée par défaut d'un rendez-vous :</label><br/>
                                <input type="time" value=<?php echo $ligne['primary_appointment_duration'];?> name="duree">
                            </div>
                            <div class="col-12">
                                <label for="secDuree">durée secondaire d'un rendez-vous :</label><br/>
                                <input type="time" value=<?php echo $ligne['secondary_appointment_duration'];?> name="secDuree">
                            </div>
                            <div class="col-12">
                                <label for="dateLim">Date limite avant la création du planning :</label><br/>
                                <input type="date" value=<?php echo $ligne['wish_period_end'];?> name="dateLim" >
                            </div>
                            <div class="row text-center p-0 m-0 ">
                                <div class="col-6">
                                    <button class="bouton">Annuler</button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="bouton">Valider</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                    <a href="generationPlanning.php">
                        <button class="bouton boutonBas" <?php echo $phase != 1.5 ? "disabled" : ""; ?>> Génerer le planning </button>
                    </a>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                <button class="bouton boutonBas" type="button" data-bs-toggle="modal" data-bs-target="#réinitialisation">Réinitialiser les données</button>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                    <a href="importExport.php">
                        <button class="bouton boutonBas">Importation / Exportation</button>
                    </a>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                    <a href="filieres.php">
                        <button class="bouton boutonBas">Gérer les filières</button>
                    </a>
                </div>
            </div>
        </div>
        <div class="modal fade " id="réinitialisation" tabindex="-1" aria-labelledby="réinitialiser" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header ">
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body  ">
                        <div class="container">
                            <div class = "row">
                                <div class="col-12">
                                    <h1 class="text-center text-accent" id="réinitialiser">Réinitialiser les données</h1>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-12">
                                    <P class="text-center">Êtes-vous sûr(e) de vouloir réinitialiser les données ? <span class="erreur">Cela supprimera TOUTES les données du site SAUF les administrateurs</span></P>
                                    <P class="text-center">Taper "Je reinitialise les données du site" pour confirmer</P>
                                </div>
                            </div>
                            <form action="menu.php" method="POST">
                                <div class = "row">
                                    <div class="col-12 p-2">
                                        <input type="text" name="confirmation" placeholder="Taper 'Je reinitialise les données du site'" class="form-control">
                                    </div>
                                    <div class="col-6 d-flex justify-content-evenly">
                                        <button type="button" class="boutonNegatif" data-bs-dismiss="modal">Retour</button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-evenly">
                                        <button type="submit" class="bouton">Réinitialiser</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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