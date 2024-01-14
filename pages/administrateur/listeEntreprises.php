<?php
    try {
        // Démarrage d'une session
        session_start();

        // Stocke la valeur de $_POST['recherche'] dans $_SESSION['recherche'] si définie
        $_SESSION['recherche'] = isset($_POST['recherche']) ? htmlspecialchars($_POST['recherche']) : ($_SESSION['recherche'] ?? null);

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
            
            header("Location: listeEntreprises.php");
            exit();
        }

        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require("../../fonctions/fonctions.php");
        require("../../fonctions/baseDeDonnees.php");

        $pdo = connecteBD(); // accès à la Base de données

        $generated = isPlanningGenerated($pdo); // Vérification de si le planning a été généré
        
        // Modification d'une entreprise existante si le formulaire en question a été correctement rempli : seuls les champs non vide font un changement
        if (isset($_POST["modifyCompany"])) {
            modifyCompany($pdo, htmlspecialchars($_POST["companyID"]), htmlspecialchars($_POST["nomEntreprise"]), htmlspecialchars($_POST["descriptionEntreprise"]), htmlspecialchars($_POST["secteurEntreprise"]), htmlspecialchars($_POST["adresseEntreprise"]), htmlspecialchars($_POST["codePostalEntreprise"]), htmlspecialchars($_POST["villeEntreprise"]), $_FILES["logoEntreprise"], htmlspecialchars($_POST['ancienNom']));
            header("Location: listeEntreprises.php");
            exit();
        }

        // Suppression d'une entreprise si le formulaire de suppression a été enclenché
        if (isset($_POST["deleteCompany"])) {
            deleteCompany($pdo, htmlspecialchars($_POST["companyID"]));
            header("Location: listeEntreprises.php");
            exit();
        }

        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un administrateur correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A'){
            header('Location: ../connexion.php');
            exit();
        }

        // Ajout d'une nouvelle entreprise si le formulaire en question a été correctement rempli
        if (isset($_POST["addCompany"])) {
            $intervenants_array = array();
        
            // Le premier intervenant est toujours présent
            $intervenant_1 = array(
                'nom' => htmlspecialchars($_POST['nomIntervenant']),
                'role' => htmlspecialchars($_POST['roleIntervenant']),
                'filieres' => $_POST['filieresIntervenant']
            );
            $intervenants_array[] = $intervenant_1;

            if (isset($_POST['nomIntervenant_2'])) {
                // Commence à partir de l'indice 2
                $cpt = 2;
                
                while (isset($_POST['nomIntervenant_' . $cpt])) {
                    $intervenant = array(
                        'nom' => htmlspecialchars($_POST['nomIntervenant_' . $cpt]),
                        'role' => htmlspecialchars($_POST['roleIntervenant_' . $cpt]),
                        'filieres' => $_POST['filieresIntervenant_' . $cpt]
                    );
                
                    $intervenants_array[] = $intervenant;
                    $cpt++;
                }
        
            }
            $isOk = true;
            foreach ($intervenants_array as $intervenant) {
                if (empty($intervenant['filieres'])) {
                    echo "insertion echouée, un intervenant n'a pas de filière";
                    $isOk = false;
                }
            }
            if ($isOk) {
                addCompany($pdo, htmlspecialchars($_POST["nomEntreprise"]), htmlspecialchars($_POST["descriptionEntreprise"]), htmlspecialchars($_POST["adresseEntreprise"]), htmlspecialchars($_POST["codePostalEntreprise"]), htmlspecialchars($_POST["villeEntreprise"]), htmlspecialchars($_POST["secteurEntreprise"]), $_FILES["logoEntreprise"], $intervenants_array);
                header("Location: listeEntreprises.php");
                exit();
            }
        } 

        // Modification de l'intervenant d'une entreprise. Seuls les champs remplis sont modifiés
        if (isset($_POST['modifySpeaker'])) {
            if (!empty($_POST['filieresIntervenant'])) {
                modifySpeaker($pdo, htmlspecialchars($_POST['nomIntervenantEdit']), htmlspecialchars($_POST['roleIntervenantEdit']), $_POST['filieresIntervenant'], htmlspecialchars($_POST['intervenantID']));
                header("Location: listeEntreprises.php");
                exit();
            }
        }

        // Suppression de l'intervenant d'une entreprise
        if (isset($_POST['deleteSpeaker'])) {
            deleteSpeaker($pdo, htmlspecialchars($_POST['intervenantID']), htmlspecialchars($_POST['companyID']));
            header("Location: listeEntreprises.php");
            exit();
        }

        // Ajout d'un nouvel intervenant lié à une entreprise
        if (isset($_POST['addSpeaker'])) {
            if (!empty($_POST['filieresIntervenant'])) {
                addSpeaker($pdo, htmlspecialchars($_POST['companyID']), htmlspecialchars($_POST['nomIntervenantAdd']), htmlspecialchars($_POST['roleIntervenantAdd']), htmlspecialchars($_POST['filieresIntervenant']));
                header("Location: listeEntreprises.php");
                exit();
            }
        }


        if (isset($_SESSION['error'])) {
            echo "<script>alert('".$_SESSION['error']."');</script>";
            unset($_SESSION['error']);
        }

        $fields = getFields($pdo); // Obtention de toutes les filières de la base de données
        
        // Permet d'attribuer à $fields toutes les filières trouvées dans la BD
        $tmp = [];
        while ($ligne = $fields->fetch()) {
            $tmp[$ligne['field_id']] = $ligne['name'];
        }
        $fields = $tmp;

         // Obtention des entreprises selon les filtres (filières et recherche)
        $stmt = getEntreprisesAdministrateur($pdo, $_SESSION['filtre'], $_SESSION['recherche']);
    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
        header('Location: ../maintenance.php');
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="../../css/all.css">
    <link rel="stylesheet" href="../../css/listeEntrepriseAdministrateur.css">
    <link rel="stylesheet" href="../../css/navbars.css">
    <link rel="stylesheet" href="../../css/filtre.css">

    <title>Eurêka - Liste des entreprises</title>
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
                        <li class="nav-item nav-item-haut nav-link p-0 d-none d-md-block h-100 lien_inactif">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEtudiants.php"> Étudiants </a>
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
                    <li class="nav-item nav-item-bas d-flex flex-column text-center actif_bas_texte actif_bas_texte_admin">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone_admin">
                        </a>
                        Entreprises
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center inactif_bas_icone" href="listeEtudiants.php">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone_admin">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="listeEtudiants.php">
                            Etudiants
                        </a>
                    </li>
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas_texte">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center inactif_bas_icone" href="listeAdministrateurs.php">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../ressources/icone_gestionnaire_black.svg" alt="Liste des administrateurs" class="icone_admin">
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
                    <h2>Liste des entreprises</h2>
                    <p>Voici toutes les entreprises présentes au salon Eurêka cette année. Cliquez sur l’une d’elle pour voir tous les étudiants qui veulent un rendez-vous avec celle-ci ! Vous pouvez également filtrer quelles filières vous intéressent grâce à la liste de filtres ci-dessous.</p>
                </div>

                <!-- Formulaire permettant d'entrer une recherche personnalisé qui filtrera l'affichage selon celle-ci -->
                <form action="listeEntreprises.php" method="post" class="col-12 col-md-6 my-2">
                    <div class="row">
                        <div class="col-12 col-md-7 p-0">
                            <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher une entreprise" class="entreeUtilisateur">    
                        </div>
                        <div class="col-5 d-none d-md-block">
                            <input type="submit" class="bouton" value="Rechercher">
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
                        <form action="listeEntreprises.php" method="post">
                            <input type="hidden" name="nouveauFiltre" value="<?php echo $key; ?>">
                            <button class="bouton-filtre <?php echo in_array($key, $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $field; ?></button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr class="mb-4">

            <!-- Bouton qui ouvre une modale afin d'ajouter une nouvelle entreprise -->
            <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddCompany" <?php echo $generated ? "disabled" : ""; ?>>
                <i class="fa-solid fa-plus text-left justify-content-center"></i>
                <h2 class="text-center m-2">Ajouter une entreprise</h2>
            </button>
            
            <!-- Modale d'ajout d'une entreprise -->
            <div class="modal fade" id="modalAddCompany" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                    <div class="modal-content  px-4 pb-4">
                        <div class="modal-header deco justify-content-start px-0">
                            <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                            <h2 class="modal-title" id="addCompanyModalLabel">Nouvelle entreprise</h2>
                        </div>
                        <div class="modal-body navigue">
                            <form action="listeEntreprises.php" method="post" enctype="multipart/form-data" class="formToCheckField">
                                <label for="nomEntreprise" class="modalLabel mb-0 mt-2">Nom</label>
                                <input class="entreeUtilisateur" type="text" name="nomEntreprise" id="nomEntreprise" placeholder="Saisir le nom de l’entreprise" maxlength="50" required>
                                <label for="descriptionEntreprise" class="modalLabel mb-0 mt-2">Description</label>
                                <input class="entreeUtilisateur" type="text" name="descriptionEntreprise" id="descriptionEntreprise" placeholder="Saisir un texte permettant de décrire en détail l'entreprise">
                                <label for="adresseEntreprise" class="modalLabel mb-0 mt-2">Adresse</label>
                                <input class="entreeUtilisateur" type="text" name="adresseEntreprise" id="adresseEntreprise" placeholder="Saisir l'adresse complète de l'entreprise" maxlength="150" required>
                                <label for="codePostalEntreprise" class="modalLabel mb-0 mt-2">Code Postal</label>
                                <input class="entreeUtilisateur" type="text" name="codePostalEntreprise" id="codePostalEntreprise" placeholder="Saisir le code postal de l'entreprise" maxlength="5" required>
                                <label for="villeEntreprise" class="modalLabel mb-0 mt-2">Ville</label>
                                <input class="entreeUtilisateur" type="text" name="villeEntreprise" id="villeEntreprise" placeholder="Saisir la ville où se situe l'entreprise" maxlength="45" required>
                                <label for="secteurEntreprise" class="modalLabel mb-0 mt-2">Secteur d'activité</label>
                                <input class="entreeUtilisateur" type="text" name="secteurEntreprise" id="secteurEntreprise" placeholder="Saisir le secteur d'activité" maxlength="50" required>
                                <label for="logo" class="modalLabel mb-0 mt-2 w-100">Logo de l'entreprise</label>
                                <input type="file" name="logoEntreprise" id="logo" accept="image/*">
                                <hr>

                                <!-- Modèle de création d'intervenant qui se duplique lors du clic sur le bouton d'ajout d'un intervenant -->
                                <div id="intervenantsContainer">
                                    <div id="intervenantTemplate" style="display: block;">
                                        <div class="intervenantContainer">
                                            <div class="d-flex flex-wrap">
                                                <h2>Intervenant &nbsp;<h2 id="numeroIntervenant">1</h2></h2>
                                                <button type ="button" class="border-0 icon-title trash" hidden><i class="fa-solid fa-trash icon"></i></button>
                                            </div>
                                            <label for="nomIntervenant" class="modalLabel mb-0 mt-2">Nom</label>
                                            <input class="entreeUtilisateur" type="text" name="nomIntervenant" id="nomIntervenant" placeholder="Saisir le nom de l’intervenant" value="Intervenant 1" maxlength="50" required>
                                            <label for="roleIntervenant" class="modalLabel mb-0 mt-2">Role</label>
                                            <input class="entreeUtilisateur" type="textarea" name="roleIntervenant" id="roleIntervenant" placeholder="Saisir un rôle pour cet intervenant (facultatif)" maxlength="80" >
                                            <div class="rowForChecks d-flex flex-wrap">
                                                <?php
                                                    // Obtentions de toutes les filières disponibles sur la BD
                                                    foreach ($fields as $key => $field) {?>
                                                        <label class="buttonToCheck me-2">
                                                            <input type="checkbox" name="filieresIntervenant[]" value="<?php echo $key;?>" >
                                                            <div class="icon-box">
                                                                <span><?php echo $field;?></span>
                                                            </div>
                                                        </label>
                                                <?php } ?>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Bouton d'ajout d'un intervenant au sein de la modale : permet d'ajouter un nombre indéfini d'intervenants grâce à du javascript -->
                                <button type="button" class="addStudent d-flex w-100 text-center align-items-center justify-content-center" onclick="ajouterIntervenant(event)">
                                    <i class="fa-solid fa-plus text-left justify-content-center"></i>
                                    <h2 class="text-center m-2">Ajouter un intervenant</h2>
                                </button>
                                <div class="row pt-2">
                                    <div class="col-6">
                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler">
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" name="addCompany" class="bouton confirmation col-6" value="Valider">Valider</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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

            <!-- Element de l'accordéon dépendant de la boucle while permettant d'afficher toutes les entreprises. -->
            <div class="accordion-item my-3">
                <h2 class="accordion-header" id="heading<?php echo $ligne['company_id']?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['company_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['company_id']?>">
                        <div class="profil-det-img d-flex text-start">
                            <div class="dp"><img src="../../ressources/logosentreprises/<?php echo $ligne['logo'] ?? 'no-photo.png'; ?>" alt=""></div>
                            <div class="pd">
                                <h2 class="title"><?php echo $ligne["name"]?></h2>
                                <ul class="text-left">
                                    <li><i class="fa-solid fa-briefcase text-left"></i> <?php echo $ligne["sector"]?></li>
                                    <li><i class="fa-solid fa-location-dot"></i> <?php echo $ligne["address"]?></li>
                                </ul>
                            </div>
                        </div>
                    </button>
                </h2>
                <div id="collapse<?php echo $ligne['company_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['company_id']?>" data-bs-parent="#listeEntreprise">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="description"><?php echo $ligne["description"]?></div>
                            <?php
                            $intervenants = getSpeakersPerCompanyAdministrateur($ligne["intervenants_roles"]); // Obtention des intervenants pour chaque entreprises
                            ?>
                            <hr>
                            <!-- Affichage de tous les intervenants d'une entreprise avec les filières qui lui sont attribués -->
                            <?php foreach ($intervenants as $intervenant) { ?>
                            <div class="my-1">
                            
                            <span class="speakerName m-0"><?php echo $intervenant["nom"];?></span>
                            <?php if ($intervenant["fonction"] != null) { ?>
                                <span class="speakerRole m-0"> <?php echo '- '.$intervenant["fonction"];?> </span>
                            <?php } ?>
                            <div class="d-flex">
                                <div class="d-flex flex-wrap w-100">
                                    <!-- Affichage des filières -->
                                    <?php foreach ($intervenant["fields"] as $field) {
                                        echo '<div class="tag text-center mb-2">'.$field.'</div>';
                                    } ?>
                                </div>
                                <div class="d-flex">
                                    <!-- Bouton permettant d'accéder à la modale pour modifier les informations concernant un intervenant -->
                                    <button class="border-0 icon-title" data-bs-toggle="modal" data-bs-target="#edit<?php echo $intervenant['id']; ?>"><i class="fa-solid fa-pen icon m-0"></i></button>
                                    <!-- Bouton permettant d'accéder à la modale pour supprimer un intervenant (seulement s'il y a plus d'un intervenant, il est impossible de supprimer le dernier intervenant) -->
                                    <button class="border-0 icon-title" data-bs-toggle="modal" data-bs-target="#delete<?php echo $intervenant['id']; ?>" <?php echo count($intervenants) === 1 ? "hidden" : ""; ?>><i class="fa-solid fa-trash icon m-0"></i></button>
                                </div>
                            </div>

                                <!-- Contenu de la modale permettant de modifier un intervenant -->
                                <div class="modal fade" id="edit<?php echo $intervenant['id'];?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content  px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="editModalLabel"><?php echo $intervenant["nom"]; ?></h2>
                                            </div>
                                            <form action="listeEntreprises.php" method="post" enctype="multipart/form-data" class="formToCheckField">
                                                <label for="nomIntervenantEdit" class="modalLabel mb-0 mt-2">Nom</label>
                                                <input class="entreeUtilisateur" type="text" name="nomIntervenantEdit" id="nomIntervenantEdit" value="<?php echo $intervenant["nom"]; ?>" maxlength="50">
                                                <label for="roleIntervenantEdit" class="modalLabel mb-0 mt-2">Role</label>
                                                <input class="entreeUtilisateur" type="textarea" name="roleIntervenantEdit" id="roleIntervenantEdit" value="<?php if (empty($intervenant["fonction"])) { echo '" placeholder="Saisir un rôle"'; } else { echo $intervenant["fonction"];} ?>" maxlength="80" >
                                                <div class="rowForChecks d-flex flex-wrap intervenantContainer">
                                                    <?php
                                                        // Affichage de toutes les filières disponibles dans la BD. Celle qui étaient préalablement selectionnées pour cette intervenant sont pré-selectionnées.
                                                        foreach ($fields as $key => $field) {?>
                                                            <label class="buttonToCheck me-2">
                                                                <input type="checkbox" name="filieresIntervenant[]" value="<?php echo $key.'"';
                                                                foreach ($intervenant["fields"] as $fieldIntervenant) {
                                                                    if ($fieldIntervenant == $field) {
                                                                        echo 'checked';
                                                                    }
                                                                }
                                                                ?> >
                                                                <div class="icon-box">
                                                                    <span><?php echo $field;?></span>
                                                                </div>
                                                            </label>
                                                    <?php } ?>
                                                </div>
                                                <input type="hidden" name="intervenantID" value="<?php echo $intervenant['id'];?>">
                                                <hr>      
                                                <div class="row mt-3">
                                                    <div class="col-6">
                                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler">
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="submit" name="modifySpeaker" class="bouton confirmation col-6" value="Valider">Valider</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenu de la modale permettant de supprimer un intervenant -->
                                <div class="modal fade" id="delete<?php echo $intervenant['id'];?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content  px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="editModalLabel"><?php echo $intervenant["nom"]; ?></h2>
                                            </div>
                                            <p>Êtes-vous sûr(e) de vouloir supprimer cet intervenant ?</p>
                                            <form action="listeEntreprises.php" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="companyID" value="<?php echo $ligne['company_id'];?>">
                                                <input type="hidden" name="intervenantID" value="<?php echo $intervenant['id'];?>">         
                                                <div class="row mt-3">
                                                    <div class="col-6">
                                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler">
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="submit" name="deleteSpeaker" class="bouton confirmation col-6" value="Valider">Valider</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                            <?php } ?>
                            <!-- Bouton permettant d'accéder à la modale pour ajouter un intervenant -->
                            <div class="col-12">
                                <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddSpeaker<?php echo $ligne['company_id']?>" <?php echo $generated ? "disabled" : ""; ?>>
                                    <i class="fa-solid fa-plus text-left justify-content-center"></i>
                                    <h2 class="text-center m-2">Ajouter un(e) intervenant(e)</h2>
                                </button>
                            </div>

                            <!-- Contenu de la modale permettant d'ajouter un nouvel intervenant -->
                            <div class="modal fade" id="modalAddSpeaker<?php echo $ligne['company_id'];?>" tabindex="-1" aria-labelledby="addSpeakerModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content  px-4 pb-4">
                                        <div class="modal-header deco justify-content-start px-0">
                                            <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                            <h2 class="modal-title" id="addSpeakerModalLabel">Nouvel intervenant</h2>
                                        </div>
                                        <form action="listeEntreprises.php" method="post" enctype="multipart/form-data" class="formToCheckField">
                                            <input type="hidden" name="companyID" value="<?php echo $ligne['company_id']?>">
                                            <label for="nomIntervenantAdd" class="modalLabel mb-0 mt-2">Nom</label>
                                            <input class="entreeUtilisateur" type="text" name="nomIntervenantAdd" id="nomIntervenantAdd" placeholder="Saisissez le nom de l'intervenant" maxlength="50" required>
                                            <label for="roleIntervenantAdd" class="modalLabel mb-0 mt-2">Role</label>
                                            <input class="entreeUtilisateur" type="textarea" name="roleIntervenantAdd" id="roleIntervenantAdd" placeholder="Saisir un rôle pour cet intervenant (facultatif)"  maxlength="80" >
                                            <div class="rowForChecks d-flex flex-wrap intervenantContainer">
                                                <?php
                                                    // Obtention de toutes les filières disponibles dans la BD
                                                    foreach ($fields as $key => $field) {?>
                                                        <label class="buttonToCheck me-2">
                                                            <input type="checkbox" name="filieresIntervenant[]" value="<?php echo $key;?>" >
                                                            <div class="icon-box">
                                                                <span><?php echo $field;?></span>
                                                            </div>
                                                        </label>
                                                <?php } ?>
                                            </div>
                                            <hr>    
                                            <div class="row mt-3">
                                                <div class="col-6">
                                                    <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler">
                                                </div>
                                                <div class="col-6">
                                                    <button type="submit" name="addSpeaker" class="bouton confirmation col-6" value="Valider">Valider</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gestion de l'affichage des étudiants souhaitants rencontrer les entreprises selon la phase -->
                        <div class="row d-flex justify-content-evenly pt-4">
                            <div class="col-12">
                                <!-- Si la phase est à 1.5 ou 2 (un planning est généré) -->
                                <?php if ($generated) {
                                    try {
                                        // Si l'entreprise est exclu (trop d'étudiants souhaitent la rencontrer)
                                        if ($ligne["excluded"] === 1) { ?>
                                        
                                            <?php $stmtEtudiant = getStudentsPerCompanyWishList($pdo, $ligne['company_id']); // Obtention des étudiants en se basant sur la table des souhaits uniquement
                                            // Vérification du nombre de ligne : si la requête est vide, aucun étudiant ne souhaite rencontrer cette entreprise
                                            if ($stmtEtudiant->rowCount() === 0) { ?>
                                                <h2 class="text-center erreur">Aucun étudiant ne souhaite rencontrer cette entreprise</h2>
                                            <?php } else { ?>
                                                <h2 class="text-center erreur">Le planning de l'entreprise <?php echo $ligne["name"]; ?> ne peut pas être généré : trop d’étudiants souhaitent la rencontrer ! Ci-dessous, la liste des étudiants intéressés par <?php echo $ligne["name"]; ?>.</h2>
                                            <?php }
                                            // Affichage de tous les étudiants qui souhaitent rencontrer cette entreprise
                                            while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                                                ?>
                                                <hr>
                                                <h2 class="student"><?php echo $ligneEtudiant["firstname"] . ' ' . $ligneEtudiant['lastname']?></h2>
                                                <p><?php echo $ligneEtudiant["name"]?></p>
                                            <?php }
                                        } else { 
                                            $speakers = getSpeakersPerCompany($pdo, $ligne['company_id']); // Obtention des intervenants d'une entreprise
                                            ?> <h2 class="student text-center">Voici le planning de cette entreprise par intervenant :</h2><br> <?php
                                            // Affichage des emplois du temps des étudiants en fonction de chaque intervenant
                                            while ($speaker = $speakers->fetch()) {
                                                
                                                echo '<h5 class="fw-bold text-center">Intervenant : '.$speaker['name'].' - '.$speaker['role'].'</h5>';
                                                $stmtEtudiant = getAppointmentPerSpeaker($pdo, $speaker['speaker_id']); // Obtention des rendez-vous en fonction des intervenants
                                                if ($stmtEtudiant->rowCount() === 0) {
                                                    echo '<hr><p class="fw-bold text-danger">Aucun étudiant ne souhaite rencontrer cette entreprise avec cet intervenant.</p>';
                                                }
                                                while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                                                ?>
                                                <hr>
                                                <p class="text-center fw-bold fs-5"><?php echo $ligneEtudiant["start"].'-'.$ligneEtudiant["end"]?></p>
                                                <h2 class="student"><?php echo $ligneEtudiant["firstname"]. ' ' . $ligneEtudiant['lastname']?></h2>
                                                <p><?php echo $ligneEtudiant["name"]?></p>
                                            <?php } }  
                                        }
                                    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
                                        redirect("../maintenance.php");
                                    }?>
                                <?php } else { // Le planning n'est pas généré
                                    try {
                                        $stmtEtudiant = getStudentsPerCompany($pdo, $ligne["company_id"]); // Obtention de tous les souhaits des étudiants par entreprise
                                    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
                                        redirect("../maintenance.php");
                                    }
                                    if ($stmtEtudiant->rowCount() === 0) { // Si la requête est vide (aucun étudiant ne souhaite rencontrer cette entreprise)
                                        echo '<h2 class="text-center erreur">Aucun étudiant n\'a encore sélectionné cette entreprise</h2>';
                                    } else {
                                        echo '<h2 class="student text-center">Voici la liste des étudiants souhaitant rencontrer cette entreprise :</h2>';
                                    }
                                    
                                    // Affichage des étudiants par entreprise
                                    while ($ligneEtudiant = $stmtEtudiant->fetch()) {
                                    ?>
                                    <hr>
                                    <h2 class="student"><?php echo $ligneEtudiant["firstname"]. ' ' . $ligneEtudiant['lastname']?></h2>
                                    <p><?php echo $ligneEtudiant["name"]?></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row d-flex justify-content-evenly mx-2">
                            <div class="col-12 mx-2">
                                <hr>
                            </div>
                            <!-- Bouton de déclenchement de la modale permettant de modifier une entreprise -->
                            <div class="col-4">
                                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#modification<?php echo $ligne['company_id'];?>"> Modifier l'entreprise </button>
                            </div>
                            <!-- Bouton de déclenchement de la modale permettant de supprimer une entreprise -->
                            <div class="col-4">
                                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#suppression<?php echo $ligne['company_id'];?>"> Supprimer l'entreprise </button>
                            </div>
                        </div>

                        <!-- Contenu de la modale permettant de modifier une entreprise -->
                        <div class="modal fade" id="modification<?php echo $ligne['company_id'];?>" tabindex="-1" aria-labelledby="modificationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                <div class="modal-content  px-4 pb-4">
                                     <div class="modal-header deco justify-content-start px-0">
                                        <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                        <h2 class="modal-title" id="modificationModalLabel"><?php echo $ligne['name']; ?></h2>
                                    </div>
                                    <!-- Formulaire de modification d'une entreprise -->
                                    <form action="listeEntreprises.php" method="post" enctype="multipart/form-data">
                                        <label for="nomEntreprise" class="modalLabel mb-0 mt-2">Nom</label>
                                        <input class="entreeUtilisateur" type="text" name="nomEntreprise" id="nomEntreprise" placeholder="<?php echo $ligne["name"]; ?>">
                                        <label for="descriptionEntreprise" class="modalLabel mb-0 mt-2">Description</label>
                                        <input class="entreeUtilisateur" type="textarea" name="descriptionEntreprise" id="descriptionEntreprise" placeholder="<?php if (empty($ligne["description"])) { echo 'Saisir une description'; } else { echo $ligne["description"];} ?>">
                                        <label for="adresseEntreprise" class="modalLabel mb-0 mt-2">Adresse</label>
                                        <input class="entreeUtilisateur" type="text" name="adresseEntreprise" id="adresseEntreprise" placeholder="<?php echo $ligne["address"]; ?>">
                                        <label for="codePostalEntreprise" class="modalLabel mb-0 mt-2">Code Postal</label>
                                        <input class="entreeUtilisateur" type="text" name="codePostalEntreprise" id="codePostalEntreprise" placeholder="<?php echo $ligne["address"]; ?>">
                                        <label for="villeEntreprise" class="modalLabel mb-0 mt-2">Ville</label>
                                        <input class="entreeUtilisateur" type="text" name="villeEntreprise" id="villeEntreprise" placeholder="<?php echo $ligne["address"]; ?>">
                                        <label for="secteurEntreprise" class="modalLabel mb-0 mt-2">Secteur d'activité</label>
                                        <input class="entreeUtilisateur" type="text" name="secteurEntreprise" id="secteurEntreprise" placeholder="<?php echo $ligne["sector"]; ?>">
                                        <label for="logo" class="modalLabel mb-0 mt-2 w-100">Logo de l'entreprise</label>
                                        <input type="file" name="logoEntreprise" id="logo" accept="image/*">
                                        <hr>                         
                                        
                                        <input type="hidden" name="companyID" value="<?php echo $ligne['company_id'];?>">
                                        <input type="hidden" name="ancienNom" value="<?php echo $ligne['name'];?>">
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler">
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" name="modifyCompany" class="bouton confirmation col-6" value="Valider">Valider</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Contenu de la modale permettant de supprimer une entreprise -->
                        <div class="modal fade" id="suppression<?php echo $ligne['company_id'];?>" tabindex="-1" aria-labelledby="suppressionModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                <div class="modal-content  px-4 pb-4">
                                     <div class="modal-header deco justify-content-start px-0">
                                        <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                        <h2 class="modal-title" id="suppressionModalLabel"><?php echo $ligne['name']; ?></h2>
                                    </div>
                                    <p>Êtes-vous sûr(e) de vouloir supprimer cette entreprise ?</p>
                                    <form action="listeEntreprises.php" method="post" enctype="multipart/form-data">                  
                                        <input type="hidden" name="companyID" value="<?php echo $ligne['company_id'];?>">
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler">
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" name="deleteCompany" class="bouton confirmation col-6" value="Valider">Valider</button>
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
    <script src="../../js/detailEntrepriseAsministrateur.js"></script>
</html>
