<?php 
    session_start();
    require("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
    if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A'){
        header('Location: ../../connexion.php');
    }
    if(isset($_POST['dateForum']) && isset($_POST['heureDebut']) && isset($_POST['heureFin']) && isset($_POST['duree']) && isset($_POST['secDuree']) && isset($_POST['dateLim'])){
        updateForum($pdo,$_POST['dateForum'],$_POST['heureDebut'],$_POST['heureFin'],$_POST['duree'],$_POST['secDuree'],$_POST['dateLim']);
    }
    $infoForum = infoForum($pdo); 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../../outils/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../../outils/fontawesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="../../../../outils/bootstrap-5.3.2-dist/js/bootstrap.js"></script>
        <script src="../../../../outils/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <link rel="stylesheet" href="forum.css">
        <title>informations eureka</title>
    </head>
    <body>
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    Eureka
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des étudiants </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des gestionnaires </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur les paramètres du forum, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Paramètres du forum </a>
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
                                <img src="../../../ressources/icone_deconnexion.svg" alt="Se déconnecter" class="logo">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
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
                    <button class="bouton boutonBas">Génerer le planning </button>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                <button class="bouton boutonBas" type="button" data-bs-toggle="modal" data-bs-target="#réinitialisation">Réinitialiser les données</button>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="../entreprises/listeEntreprises.php">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="../entreprises/listeEntreprises.php">
                        Entreprises
                        </a>
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="../etudiants/listeEtudiants.php">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="../etudiants/listeEtudiants.php">
                        Etudiants
                        </a>
                    </li>
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="../gestionnaire/listeGestionnaires.php">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="../gestionnaire/listeGestionnaires.php">
                        Gestionnaires
                        </a>
                    </li>
                    <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_forum_white.svg" alt="Paramètres du forum" class="icone">
                        </a>
                        Forum
                    </li>
                </ul>
            </div>
        </nav>
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
                                    <h1 class="text-center" id="réinitialiser">Réinitialiser les données</h1>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-12">
                                    <P class="text-center">Êtes-vous sûr(e) de vouloir réinitialiser les données ?</P>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-dismiss="modal">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#donnéeSupprimer" >Réinitialiser</button>
                                </div>
                            </div>
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
                                    <button type="button" data-bs-dismiss="modal" class="bouton">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <a href="../../../fonctions/deconnecter.php"><button type="button" class="bouton">Se déconnecter </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>