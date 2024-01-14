<?php
    // Démarrage d'une session
    session_start();
    try {
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

        // Ajout d'une nouvelle filière si le formulaire en question a été correctement rempli
        if (isset($_POST['nom'])) {
            newField($pdo, htmlspecialchars($_POST['nom']));
            header('Location: filieres.php');
            exit();
        }

        // Suppression d'une filière si le formulaire en question a été correctement rempli
        if (isset($_POST['supprimer'])) {
            deleteField($pdo, htmlspecialchars($_POST['supprimer']));
            header('Location: filieres.php');
            exit();
        }

        // Modification d'une filière si le formulaire en question a été correctement rempli
        if (isset($_POST['modify'])) {
            modifyField($pdo, htmlspecialchars($_POST['modify']), htmlspecialchars($_POST['newName']));
            header('Location: filieres.php');
            exit();
        }

        $fields = getFields($pdo); // Obtention de toutes les filières de la base de données
    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
        header('Location: ../../maintenance.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head><!-- Métadonnées et liens vers les feuilles de style -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <link rel="stylesheet" href="../../../css/listeGestionnairesAdministrateur.css">
        
        <title>Eurêka - Gestion des filières</title>
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
            <div class="row">
                <div class="col-12">
                    <h2>Liste des filières</h2>
                    <p>Voici toutes les filières. Vous pouvez ajouter de nouvelles filières, modifier et supprimer les filières existantes. Vous pouvez les supprimer uniquement si aucun étudiant et aucun intervenant n'ont cette filière.</p>
                    
                    <!-- Bouton qui ouvre une modale afin d'ajouter une nouvelle filière -->
                    <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modaladdField">
                        <i class="fa-solid fa-plus text-left justify-content-center"></i>
                        <h2 class="text-center m-2">Ajouter une filière</h2>
                    </button>
                </div>

                <!-- Contenu de la modale d'ajout d'une filière -->
                <div class="modal fade" id="modaladdField" tabindex="-1" aria-labelledby="addFieldModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                        <div class="modal-content  px-4 pb-4">
                            <div class="modal-header deco justify-content-start px-0">
                                <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                <h2 class="modal-title" id="addFieldModalLabel">Nouvelle filière</h2>
                            </div>
                            <h2>Informations sur la filière</h2>
                            <form action="filieres.php" method="post">
                                <label for="nom" class="modalLabel mt-2">Nom</label>
                                <input class="zoneText" type="text" name="nom" id="nom" placeholder="Saisir le nom" required/>
                                <div class="row pt-3">
                                    <div class="col-6">
                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="bouton confirmation col-6" value="Valider">Valider</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <!-- Accordéon Bootstrap -->
                    <div class="accordion" id="listeGestionnaires">
                        <?php
                            // Affichage de toutes les filières stockées dans la BD
                            if ($fields->rowCount() === 0) { // Vérifie si aucune filière n'est actuellement stocké et affiche un message en conséquence.
                                echo '<p>Le site ne contient aucune filière.</p>';
                            } else {
                                while ($ligne = $fields->fetch()) { 
                        ?>

                        <!-- Element de l'accordéon dépendant de la boucle while permettant d'afficher toutes les filières. -->
                        <div class="accordion-item my-3">
                            <h2 class="accordion-header" id="heading<?php echo $ligne['field_id']?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['field_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['field_id']?>">
                                    <div class="profil-det-img d-flex text-start">
                                        <div class="pd detailEtudiant">
                                            <h2 class="title"><?php echo $ligne["name"]?></h2>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $ligne['field_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['field_id']?>" data-bs-parent="#listeEntreprise">
                                <div class="accordion-body pb-1 pt-0">
                                    <div class="row m-0">
                                        <div class="row my-3">
                                            <!-- Bouton de déclenchement de la modale permettant de supprimer une filière -->
                                            <div class="col-md-6 py-2">
                                                <button class="boutonNegatif" data-bs-toggle="modal" data-bs-target="#modalDeleteField<?php echo $ligne['field_id']; ?>" <?php echo isFieldInUse($pdo, $ligne['field_id']) ? "disabled" : ""; ?>>Supprimer</button>
                                            </div>
                                            <!-- Bouton de déclenchement de la modale permettant de modifier une filière -->
                                            <div class="col-md-6 py-2">
                                                <button class="bouton col-md-6" data-bs-toggle="modal" data-bs-target="#modalmodify<?php echo $ligne['field_id']; ?>">Modifier</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Contenu de la modale permettant de supprimer une filière -->
                                <div class="modal fade" id="modalDeleteField<?php echo $ligne['field_id']; ?>" tabindex="-1" aria-labelledby="DeleteFieldModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="DeleteFieldModalLabel"><?php echo $ligne['name'];?></h2>
                                            </div>
                                            <div class="modal-body">
                                                <h2>Êtes-vous sûr(e) de vouloir supprimer ce gestionnaire ?</h2>
                                                <form action="filieres.php" method="post">
                                                    <input type="hidden" name="supprimer" value="<?php echo $ligne['field_id'];?>">
                                                    <div class="row mt-3">
                                                        <div class="col-6">
                                                            <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="submit" class="bouton confirmation col-6" value="Valider">Valider</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenu de la modale permettant de modifier une filière -->
                                <div class="modal fade" id="modalmodify<?php echo $ligne['field_id']; ?>" tabindex="-1" aria-labelledby="modifyLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="modify"><?php echo $ligne['name'];?></h2>
                                            </div>
                                            <div class="modal-body">
                                                <form action="filieres.php" method="post">
                                                    <label for="newName"  class="modalLabel mb-0 mt-2">Choisir un nouveau nom pour la filière :</label>
                                                    <input type="text" class="zoneText mb-3" name="newName" maxlength="50" placeholder="Saisir le nom" required>
                                                    <input type="hidden" name="modify" value="<?php echo $ligne['field_id'];?>">
                                                    <div class="row mt-3">
                                                        <div class="col-6">
                                                            <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="submit" class="bouton confirmation col-6" value="Valider">Valider</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php   } 
                            } ?>
                    </div>
                </div>
            </div>
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