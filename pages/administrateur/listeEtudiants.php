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
        require("../../fonctions/baseDeDonnees.php");
        require("../../fonctions/fonctions.php");

        $pdo = connecteBD(); // accès à la Base de données

        // $_SESSION['filtre'] est un tableau qui contient les id des filtres selectionnes
        if (!isset($_SESSION['filtre']) || $_SESSION['filtre'] == null) {
            $_SESSION['filtre'] = array();
        }

        // Gère l'affichage des étudiants en fonction des filtres
        if (isset($_POST['nouveauFiltre'])) {
            if (in_array(htmlspecialchars($_POST['nouveauFiltre']), $_SESSION['filtre'])) {
                $index = array_search(htmlspecialchars($_POST['nouveauFiltre']), $_SESSION['filtre']);
                unset($_SESSION['filtre'][$index]);
            } else {
                array_push($_SESSION['filtre'], htmlspecialchars($_POST['nouveauFiltre']));
            }
            
            header("Location: listeEtudiants.php");
            exit();
        }
        
        // Ajout d'un nouvel étudiant si le formulaire en question a été correctement rempli
        if (isset($_POST['nomEtudiant'])) {
            addNewStudent($pdo, htmlspecialchars($_POST['prenomEtudiant']), htmlspecialchars($_POST['nomEtudiant']), htmlspecialchars($_POST['emailEtudiant']), htmlspecialchars($_POST['motDePasseEtudiant']), htmlspecialchars($_POST['filiereEtudiant']));
            header("Location: listeEtudiants.php");
            exit();
        }

        // Suppression d'un étudiant si le formulaire de suppression a été enclenché
        if (isset($_POST['supprimer'])) {
            deleteStudent($pdo, htmlspecialchars($_POST['supprimer']));
            header("Location: listeEtudiants.php");
            exit();
        }

        // Modification du mot de passe d'un étudiant selon les données entrées dans le formulaire en question
        if (isset($_POST['modifyPassword'])) {
            modifyPassword($pdo, htmlspecialchars($_POST['modifyPassword']), htmlspecialchars($_POST['newPassword']));
            header("Location: listeEtudiants.php");
            exit();
        }

        // Vérification de si le planning est généré ou pas
        $generated = isPlanningGenerated($pdo);

        // Attribue comme valeur par défaut l'ordre alphabétique comme tri
        if (!isset($_SESSION['triPar'])) {
            $_SESSION['triPar'] = "alpha";
        }

        // Change l'ordre d'affichage des étudiants selon l'option de tri choisie par l'utilisateur
        if (isset($_POST['triPar'])) {
            $_SESSION['triPar'] = htmlspecialchars($_POST['triPar']);
            header("Location: listeEtudiants.php");
            exit();
        }

        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un administrateur correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A'){
            header('Location: ../cnnexion.php');
            exit();
        }

        $fields = getFields($pdo); // Obtention de toutes les filières de la base de données
        
        // Permet d'attribuer à $fields toutes les filières trouvées dans la BD
        $tmp = [];
        while ($ligne = $fields->fetch()) {
            $tmp[$ligne['field_id']] = $ligne['name'];
        }
        $fields = $tmp;

        // Obtention des étudiants selon les filtres (filières et recherche)
        $stmt = getInfoStudentsSort($pdo, $_SESSION['recherche'], $_SESSION['filtre'], $_SESSION['triPar']);
    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
        header("Location: ../maintenance.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Métadonnées et liens vers les feuilles de style -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <link rel="stylesheet" href="../../css/listeEtudiantAdministrateur.css">
        <link rel="stylesheet" href="../../css/all.css">
        <link rel="stylesheet" href="../../css/navbars.css">
        <link rel="stylesheet" href="../../css/filtre.css">

        <title>Eurêka - Liste des étudiants</title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../ressources/logo_black.svg" alt="Logo Eureka" class="logoDisplay me-2">
                    <span class="logoDisplay">Eureka</span>
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-item-haut nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEntreprises.php"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block lien_inactif">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Étudiants </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeAdministrateurs.php"> Administrateurs </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeGestionnaires.php"> Gestionnaires </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur les paramètres du forum, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="forum/menu.php"> Forum </a>
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
                                <img src="../../ressources/icone_deconnexion.svg" alt="Se déconnecter" class="logo">
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
                    <a class="d-flex justify-content-center inactif_bas_icone" href="listeEntreprises.php">
                        <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="listeEntreprises.php">
                        Entreprises
                    </a>
                </li>
                <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center actif_bas_texte actif_bas_texte_admin">
                    <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center actif_bas_icone">
                        <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../ressources/icone_etudiant_white.svg" alt="Liste des étudiants" class="icone_admin">
                    </a>
                    Etudiants
                </li>
                <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="listeAdministrateurs.php">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="listeAdministrateurs.php">
                        Admins
                    </a>
                </li>
                <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas_texte">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone" href="listeGestionnaires.php">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="listeGestionnaires.php">
                        Gestionnaires
                    </a>
                </li>
                <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone" href="forum/menu.php">
                        <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../ressources/icone_forum_black.svg" alt="Paramètres du forum" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="forum/menu.php">
                    Forum
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Container principal de la page --> 
    <div class="container mt-2">
        <div class="row d-flex align-items-center h-100">
            <div class="col-12 col-md-6">
                <h2>Liste des étudiants</h2>
                <p>Voici tous les étudiants inscrits au forum Eurêka de cette année. Cliquez sur l’un d’eux pour voir la liste des entreprises auprès desquelles il souhaite obtenir un rendez-vous ! Vous pouvez également créer un nouvel étudiant, en supprimer un ou modifier le mot de passe d'un étudiant.</p>
            </div>

            <!-- Formulaire permettant d'entrer une recherche personnalisé qui filtrera l'affichage selon celle-ci -->
            <form action="listeEtudiants.php" method="post" class="col-12 col-md-6 my-2">
                <div class="row">
                    <div class="col-12 col-md-7 p-0">
                        <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher un etudiant" class="zoneText"/>    
                    </div>
                    <div class="col-5 d-none d-md-block">
                        <input type="submit" class="bouton" value="Rechercher"/>
                    </div>
                </div>
            </form>
        </div>

        <!-- Boutons permettant le filtre des entreprises selon les filières -->
        <div class="container p-0">
            <div class="row">
                <div class="col-12">
                    <h2>Filières</h2>
                    <?php
                        foreach ($fields as $key => $field) {
                    ?>
                    <form action="listeEtudiants.php" method="post">
                        <input type="hidden" name="nouveauFiltre" value="<?php echo $key; ?>">
                        <button class="bouton-filtre <?php echo in_array($key, $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $field; ?></button>
                    </form>
                    <?php } ?>
                </div>
            </div> 
        </div>
        <hr class="m-0">

        <!-- Formulaire permettant de trier les étudiants : par ordre alphabétique, par ordre de souhait croissant, par ordre de souhait décroissant -->
        <div class="d-flex flex-row-reverse">
            <form action="listeEtudiants.php" method="post">
                <select id="triPar" name="triPar" class="form-control sort text-end" onchange="this.form.submit()">
                    <option value="default" disabled selected>&#x21C5; TRIER PAR</option>
                    <option value="alpha">Ordre alphabétique</option>
                    <option value="croissant">Nombre de <?php echo !$generated ? "souhaits" : "rencontres" ?> croissant</option>
                    <option value="decroissant">Nombre de <?php echo !$generated ? "souhaits" : "rencontres" ?> décroissant</option>
                </select>
            </form>
        </div>

        <!-- Bouton qui ouvre une modale afin d'ajouter un nouvel étudiant -->
        <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddStudent" <?php echo $generated ? "disabled" : ""; ?>>
            <i class="fa-solid fa-plus text-left justify-content-center"></i>
            <h2 class="text-center m-2">Ajouter un(e) étudiant(e)</h2>
        </button>

        <!-- Modale d'ajout d'un étudiant -->
        <div class="modal fade" id="modalAddStudent" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content  px-4 pb-4">
                    <div class="modal-header deco justify-content-start px-0">
                        <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                        <h2 class="modal-title" id="addStudentModalLabel">Nouvel(le) étudiant(e)</h2>
                    </div>
                    <h2 class="mt-2">Informations sur l’étudiant</h2>
                    <form action="listeEtudiants.php" method="post">
                        <label for="email" class="modalLabel mb-0 mt-2">Adresse mail / Identifiant</label>
                        <input class="zoneText" type="email" id="email" name="emailEtudiant" placeholder="Saisir l'adresse mail de l'étudiant" required/>
                        <label for="prenomEtudiant" class="modalLabel mb-0 mt-2">Prénom</label>
                        <input class="zoneText" type="text" id="prenomEtudiant" name="prenomEtudiant" placeholder="Saisir le prénom de l’étudiant" required/>
                        <label for="nomEtudiant" class="modalLabel mb-0 mt-2">Nom</label>
                        <input class="zoneText" type="text" id="nomEtudiant" name="nomEtudiant" placeholder="Saisir le nom de l’étudiant" required/>
                        <label class="modalLabel mb-0 mt-2">Filière</label>
                        <select id="FieldValues" name="filiereEtudiant" class="zoneText" required/>
                            <option value="" disabled selected>Sélectionner la filière de l’étudiant</option>
                            <?php 
                            foreach ($fields as $key => $field) { ?>
                                <option value="<?php echo $key;?>"><?php echo $field;?></option>

                            <?php } ?>
                        </select>
                        <p class="modalLabel mb-0 mt-2">Mot de passe (8 caractères minimum dont au moins un symbole et un chiffre - à transmettre à l’étudiant !)</p>
                        <input class="zoneText mb-3" type="text" name="motDePasseEtudiant" placeholder="Saisir le mot de passe" pattern="^(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$" required/>
                        <div class="row">
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

        <!-- Accordéon Bootstrap -->
        <div class="accordion" id="listeEntreprise">

        <!-- Gestion de l'affichage en fonction des filtres sélectionnée et de la recherche saisie -->
        <?php
            if (empty($_SESSION['filtre'])) {
                echo '<p>Aucune filière sélectionnée. Veuillez choisir au moins une filière.</p>';
            } elseif ($stmt->rowCount() === 0) {
                echo '<p>Aucun résultat trouvé.</p>';
            } else {
                while ($ligne = $stmt->fetch()) { 
        ?>

        <!-- Element de l'accordéon dépendant de la boucle while permettant d'afficher tous les étudiants. -->
        <div class="accordion-item my-3">
            <h2 class="accordion-header" id="heading<?php echo $ligne['user_id']?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['user_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['user_id']?>">
                    <div class="profil-det-img d-flex text-start">
                        <div class="pd detailEtudiant">
                            <h2 class="title"><?php echo $ligne["firstname"] . ' ' . $ligne["lastname"];?></h2>
                            <?php echo $ligne["filiere"]?></br>
                            <span class="<?php echo $ligne["nbSouhait"] < 1 ? "erreur" : ""?>"> <?php echo $ligne["nbSouhait"]?> <?php echo !$generated ? "souhaits" : "rencontres" ?> </span>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse<?php echo $ligne['user_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['user_id']?>" data-bs-parent="#listeEntreprise">
                <div class="accordion-body pb-1 pt-0">
                    <div class="row m-0">
                        <div class="row my-3">
                            <div class="col-md-6 py-2">
                                <!-- Bouton de déclenchement de la modale permettant de supprimer un étudiant -->
                                <button class="boutonNegatif" data-bs-toggle="modal" data-bs-target="#modalDeleteStudent<?php echo $ligne['user_id']; ?>">Supprimer</button>
                            </div>
                            <div class="col-md-6 py-2">
                                <!-- Bouton de déclenchement de la modale permettant de modifier le mot de passe d'un gestionnaire -->
                                <button class="bouton col-md-6" data-bs-toggle="modal" data-bs-target="#modalModifyStudentPassword<?php echo $ligne['user_id']; ?>">Modifier le mot de passe</button>
                            </div>
                        </div>
                            <!-- Contenu de la modale permettant de supprimer un étudiant -->
                            <div class="modal fade" id="modalDeleteStudent<?php echo $ligne['user_id']; ?>" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content px-4 pb-4">
                                        <div class="modal-header deco justify-content-start px-0">
                                            <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                            <h2 class="modal-title" id="deleteStudentModalLabel"><?php echo $ligne['firstname']. ' ' . $ligne['lastname'];?></h2>
                                        </div>
                                        <div class="modal-body">
                                            <h2>Êtes-vous sûr(e) de vouloir supprimer cet(te) étudiant(e) ?</h2>
                                            <form action="listeEtudiants.php" method="post">
                                            <?php if ($ligne["nbSouhait"] >= 1) { ?>
                                                <input type="hidden" name="souhaits" value="<?php echo $ligne["nbSouhait"];?>"/>
                                                <p class="text-danger">Attention, cet(te) étudiant(e) souhaite rencontrer des entreprises ! </p>
                                                <?php } ?>
                                                <input type="hidden" name="supprimer" value="<?php echo $ligne['user_id'];?>">
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

                            <!-- Contenu de la modale permettant de modifier le mot de passe d'un étudiant -->
                            <div class="modal fade" id="modalModifyStudentPassword<?php echo $ligne['user_id']; ?>" tabindex="-1" aria-labelledby="modifyStudentPasswordLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content px-4 pb-4">
                                        <div class="modal-header deco justify-content-start px-0">
                                            <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                            <h2 class="modal-title" id="modifyStudentPassword"><?php echo $ligne['firstname']. ' ' . $ligne['lastname'];?></h2>
                                        </div>
                                        <div class="modal-body">
                                            <form action="listeEtudiants.php" method="post">
                                                <label for="newPassword"  class="modalLabel mb-0 mt-2">Choisir un nouveau mot de passe (8 caractères minimum dont au moins un symbole et un chiffre - à transmettre à l'étudiant !) :</label>
                                                <input type="text" class="zoneText mb-3" name="newPassword" id="newPassword" pattern="^(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$" placeholder="Saisir un mot de passe" required>
                                                <input type="hidden" name="modifyPassword" value="<?php echo $ligne['user_id'];?>">
                                                <div class="row">
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

                            <!-- Affichage du contenu de chaque item de l'accordéon dépendant de la phase et du fait que l'étudiant ait émis des souhaits ou non -->
                            <?php if ($ligne["nbSouhait"] < 1 && !$generated) {
                                echo "<p class='erreur text-center'>Cet(te) étudiant(e) n'a pris aucun rendez-vous pour l'instant !</p>";
                            }
                            if (!$generated) {
                            try {
                                $stmt2 = getEntreprisesPerStudent($pdo, $ligne['user_id']); // Obtention de la liste des entreprises que souhaite rencontrer cet étudiant
                            } catch (Exception $e) {
                                redirect("./../maintenance.php"); // En cas d'erreur de connection à la BD, redirection vers la page de site en maintenance
                            }
                            $rowNumber = 0; // Stocke le nombre de souhaits de cet étudiant
                            // Affichage de toutes les entreprises que souhaite rencontrer l'étudiant
                            while ($ligne2 = $stmt2->fetch()) {
                                $rowNumber++;
                                echo '<hr>';
                                ?> 
                                <div>
                                    <div class="profil-det-img d-flex text-start">
                                        <!-- Affichage du logo de l'entreprise s'il y en a un stocké dans la base de donnée, d'une image par défaut sinon -->
                                        <div class="dp"><img src="../../ressources/logosentreprises/<?php echo $ligne2["logo_file_name"] != "" ? $ligne2["logo_file_name"] : "no-photo.png"?>" alt="Logo de l'entreprise"></div>
                                        <div class="pd">
                                            <h2 class="title"><?php echo $ligne2["name"]?></h2>
                                            <ul class="text-left">
                                                <li><i class="fa-solid fa-briefcase text-left"></i> <?php echo $ligne2["sector"]?></li>
                                                <li><i class="fa-solid fa-location-dot"></i> <?php echo $ligne2["address"]?></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                            } else { // Si le planning a déjà été généré             
                                try {
                                    $planning = planningPerUser($pdo, $ligne['user_id']); // Obtention du planning de l'utilisateur
                                    $unlistedCompany = unlistedCompanyPerUser($pdo, $ligne['user_id']); // Obtention des entreprises qui n'entre pas dans l'emploi du temps de cet étudiant
                                } catch (Exception $e) {
                                    redirect("./../maintenance.php"); // Redirection vers la page de maintenance s'il y a eu une erreur lors de l'échange avec la base de données
                                }
                                // Vérification que l'étudiant souhaite doit rencontrer au moins une entreprise
                                if (Count($planning) > 0 || $unlistedCompany->rowCount() > 0) {
                                    // Affichage de toutes les entreprises qui rentre dans le planning
                                    foreach ($planning as $rdv) {?>
                                        <div class="row mx-1">
                                            <div class="col-12">
                                                <hr>
                                                <p class="text-center"><?php echo htmlspecialchars($rdv['start'])?> - <?php echo htmlspecialchars($rdv['end'])?></p>
                                                <p class="text-center text-accent"><?php echo htmlspecialchars($rdv['company_name']); ?></p>
                                            </div>
                                        </div>
                                    <?php }
                                    // Puis de celle qui ne rentrent pas dans le planning - s'il y en a uniquement
                                    if ($unlistedCompany->rowCount() > 0) {?>
                                        <div class="row mx-1">
                                            <div class="col-12">
                                                <hr>
                                                <p><h2>Consulter les rendez-vous non planifiables</h2>
                                                Attention, certaines entreprises ont reçues trop de demandes : elles n’ont pas pu être intégrées à l'emploi du temps des étudiant. Si vous souhaitez obtenir un rendez-vous avec celles-ci, il faudra les contacter directement. </p>
                                            </div>
                                        </div>
                                    <?php }
                                    while ($ligne3 = $unlistedCompany->fetch()) {?>
                                        <div class="row mx-1">
                                            <div class="col-12">
                                                <hr>
                                                <p class="text-center text-accent"><?php echo htmlspecialchars($ligne3['name']); ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="row mx-1 fixed-bottom barre-bas">
                                    </div>
                                <?php
                                } else { // Affichage d'un message si l'étudiant n'a pris aucun rendez-vous.
                                ?>
                                    <div class="row mx-1">
                                            <div class="col-12">
                                                <h5 class="erreur">L'étudiant n'a pas de rendez-vous</h5>
                                            </div>
                                    </div>
                                    <div class="row mx-1 fixed-bottom barre-bas">
                                    </div>
                                <?php
                                }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php   } 
                } ?>
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
                                    <a href="../../fonctions/deconnecter.php"><button type="button" class="bouton boutonDeconnexion">Se déconnecter </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </body>
</html>
