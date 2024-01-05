<?php
    session_start();
    $user = 1;

    // Stocke la valeur de $_POST['recherche'] dans $_SESSION['recherche'] si définie
    $_SESSION['recherche'] = $_POST['recherche'] ?? $_SESSION['recherche'] ?? null;

    // $_SESSION['filtre'] est un tableau qui contient les id des filtres selectionnes
    if (!isset($_SESSION['filtre']) || $_SESSION['filtre'] == null) {
        $_SESSION['filtre'] = array();
    }
    if (isset($_POST['nouveauFiltre'])) {
        if (in_array($_POST['nouveauFiltre'], $_SESSION['filtre'])) {
            $index = array_search($_POST['nouveauFiltre'], $_SESSION['filtre']);
            unset($_SESSION['filtre'][$index]);
        } else {
            array_push($_SESSION['filtre'], $_POST['nouveauFiltre']);
        }
        
        header("Location: detailEntreprise.php");
        exit();
    }
    include("../../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();

    if (isset($_POST["suppression_entreprise_id"])) {
        supprimerEntreprise($pdo, $_POST["suppression_entreprise_id"]);
    }

    //TODO réussir à récupérer toutes les informations des différents intervenants pour la fonction
    if (isset($_POST["modification_entreprise_id"])) {
        modifierEntreprise($pdo, $_POST["modification_entreprise_id"], $_POST["nom_entreprise"], $_POST["secteur_activite"], $_POST["lieu"], $_POST["description"]);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../lib/fontawesome-free-6.5.1-web/css/all.css">
    <script src="../../../../lib/bootstrap-5.3.2-dist/js/bootstrap.js"></script>
    <script src="../../../../lib/jquery/jquery-3.3.1.js"></script>
    <link rel="stylesheet" href="./listeEntreprise.css">
    <link rel="stylesheet" href="../../../../css/navbars.css">
    <link rel="stylesheet" href="./filtre.css">
    <title>Eureka - Liste des entreprises</title>
</head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../../ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    Eureka
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-link p-0 d-none d-md-block h-100 lien_inactif">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des entreprises </a>
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
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Paramètres du forum </a>
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
                                <img src="../../../../ressources/icone_deconnexion.svg" alt="Se déconnecter" class="logo">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container mt-2">
            <div class="row d-flex align-items-center h-100">
                <div class="col-12 col-md-6">
                    <h2>Liste des entreprises</h2>
                    <p>Voici toutes les entreprises présentes au salon Euréka cette année. Cliquez sur l’une d’elle pour voir tous les étudiants qui veulent un rendez-vous avec celle-ci ! Vous pouvez également filtrer quelles filières vous intéressent grâce à la liste de filtres ci-dessous.</p>
                </div>
                <form action="detailEntreprise.php" method="post" class="col-12 col-md-6 my-2">
                    <div class="row">
                        <div class="col-8">
                            <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher une entreprise" class="zoneText"/>    
                        </div>
                        <div class="col-4">
                            <input type="submit" class="bouton" value="Rechercher"/>
                        </div>
                    </div>
                </form>
            </div>
            <div class="container p-0">
                <div class="row">
                    <div class="col-12">
                        <h2>Filières</h2>
                        <?php
                            $fields = getFields($pdo);
                            while ($ligne = $fields->fetch()) {
                        ?>
                        <form action="detailEntreprise.php" method="post">
                            <input type="hidden" name="nouveauFiltre" value="<?php echo $ligne['field_id']; ?>">
                            <button class="bouton-filtre <?php echo in_array($ligne['field_id'], $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $ligne['name']; ?></button>
                        </form>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <hr class="mb-4">
            <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddStudent">
                <i class="fa-solid fa-plus text-left justify-content-center"></i>
                <h2 class="text-center m-2">Ajouter une entreprise</h2>
            </button>
            
            <div class="modal fade" id="modalAddStudent" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                    <div class="modal-content  px-4 pb-4">
                        <div class="modal-header deco justify-content-start px-0">
                            <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                            <h2 class="modal-title" id="addStudentModalLabel">Nouvelle entreprise</h2>
                        </div>
                        <h2>Informations sur l’entreprise</h2>
                        <form action="detailEtudiant.php" method="post">
                            <label for="email" class="modalLabel mb-0 mt-2">Adresse mail / Identifiant</label>
                            <input class="zoneText" type="email" name="emailEtudiant" placeholder="Saisir l'adresse mail de l'étudiant" required/>
                            <label for="prenomEtudiant" class="modalLabel mb-0 mt-2">Prénom</label>
                            <input class="zoneText" type="text" name="prenomEtudiant" placeholder="Saisir le prénom de l’étudiant" required/>
                            <label for="nomEtudiant" class="modalLabel mb-0 mt-2">Nom</label>
                            <input class="zoneText" type="text" name="nomEtudiant" placeholder="Saisir le nom de l’étudiant" required/>
                            <label for="filiereEtudiant" class="modalLabel mb-0 mt-2">Filière</label>
                            <select id="FieldValues" name="filiereEtudiant" class="zoneText" required/>
                                <option value="" disabled selected>Sélectionner la filière de l’étudiant</option>
                                <?php 
                                $fields = getFields($pdo);
                                while ($ligne = $fields->fetch()) { ?>
                                    <option value="<?php echo $ligne['field_id'];?>"><?php echo $ligne['name'];?></option>

                                <?php } ?>
                            </select>
                            <p class="modalLabel mb-0 mt-2">Mot de passe (à transmettre à l’étudiant !)</p>
                            <input class="zoneText" type="text" name="motDePasseEtudiant" placeholder="Saisir le mot de passe" pattern="^(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$" required/>
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

            <!-- Accordéon Bootstrap -->
            <div class="accordion" id="listeEntreprise">
            <?php
                $stmt = getEntreprises($pdo, $_SESSION['filtre'], $_SESSION['recherche']);

                if (empty($_SESSION['filtre'])) {
                    echo '<p>Aucune filière sélectionnée. Veuillez choisir au moins une filière.</p>';
                } elseif ($stmt->rowCount() === 0) {
                    echo '<p>Aucun résultat trouvé.</p>';
                } else {
                    while ($ligne = $stmt->fetch()) { 
            ?>
            <div class="accordion-item my-3">
                <h2 class="accordion-header" id="heading<?php echo $ligne['company_id']?>">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['company_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['company_id']?>">
                        <div class="profil-det-img d-flex text-start">
                            <div class="dp"><img src="../../../../ressources/no-photo.png" alt=""></div>
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
                            $stmtIntervenant = getSpeakersPerCompany($pdo, $ligne["company_id"]);
                            while ($ligneIntervenant = $stmtIntervenant->fetch()) { 
                            ?>
                            <hr>
                            <h2 class="student"><?php echo $ligneIntervenant["name"]?></h2>
                            <p> Fonction de l'intervenant </p>
                            <div class="row d-flex align-text-top">
                            <?php
                                $stmtFiliere = getFieldsPerSpeakers($pdo, $ligneIntervenant["speaker_id"]);
                                while ($ligneFiliere = $stmtFiliere->fetch()) {
                                    echo '<span class="filiere">'.$ligneFiliere["name"].'</span>';
                                }
                            ?>
                            </div>
                            <?php } ?>
                        </div>
                        <hr>
                        <div class="row d-flex justify-content-evenly">
                            <div class="col-4">
                                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#modification"> Modifier </button>
                            </div>
                            <div class="col-4">
                                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#suppression"> Supprimer </button>
                            </div>
                        </div>
                        <div class="row">
                            <?php
                                $stmtEtudiant = getStudentsPerCompany($pdo, $ligne["company_id"]);
                                while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                            ?>
                            <hr>
                            <h2 class="student"><?php echo $ligneEtudiant["username"]?></h2>
                            <p><?php echo $ligneEtudiant["name"]?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php   } 
                } ?>
        </div>
        
        <!-- TODO finir cette modal de merde -->
        <div class="modal fade" id="modification" tabindex="-1" aria-labelledby="Modifier" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header deco">
                        <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <form action="detailEntreprise.php" method="post">
                                <div class="row">
                                    <div class="col-12">
                                        <h1>Information sur l'entreprise</h1>
                                    </div>
                                    <div class="col-12">
                                        <label for="nom_entreprise"> Nom de l'entreprise </label> <br/>
                                        <input type="text" name="nom_entreprise" value=<?php echo $ligne["name"] ?>>
                                    </div>
                                    <hr/>
                                    <div class="col-12">
                                        <label for="secteur_activite"> Secteur d'activité </label> <br/>
                                        <input type="text" name="secteur_activite" value=<?php echo $ligne["sector"] ?>>
                                    </div>
                                    <hr/>
                                    <div class="col-12">
                                        <label for="lieu"> Lieu </label> <br/>
                                        <input type="text" name="lieu" value=<?php echo $ligne["address"] ?>>
                                    </div>
                                    <hr/>
                                    <div class="col-12">
                                        <label for="description"> Description </label> <br/>
                                        <input type="textarea" name="description" value=<?php echo $ligne["description"] ?>>
                                    </div>
                                    <hr/>
                                </div>
                                <?php $i = 1;
                                $count = getSpeakersPerCompany($pdo, $ligne["company_id"]);
                                while ($speaker = $count->fetch()) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <h1> Intervenant <?php echo $i ?> </h1>
                                    </div>
                                    <div class="col-12">
                                        <label for="nom_intervenant"> Nom de l'intervenant </label> <br/>
                                        <input type="text" name="nom_intervenant" value="Saisir le nom de l'intervenant">
                                    </div>
                                    <hr/>
                                    <div class="col-12">
                                        <label for="fonction_intervenant"> Fonction de l'intervenant </label> <br/>
                                        <input type="text" name="fonction_intervenant" value="Saisir la fonction de l'intervenant">
                                    </div>
                                    <hr/>
                                    <div class="col-12">
                                        <h5> Filières d'intérêt </h5> <br/>
                                        <?php $liste_filieres = getFields($pdo);
                                        while ($filiere = $liste_filieres->fetch()) { ?>
                                        <button class="bouton"> <?php $filiere["name"] ?> </button>
                                        <?php } ?>
                                    </div>
                                    <hr/>
                                </div>
                                <?php $i += 1;
                                } ?>
                                <div class="row">
                                    <div class="col-6 d-flex justify-content-evenly">
                                        <button type="button" data-bs-dismiss="modal"> Annuler </button>
                                    </div>
                                    <div class="col-6 d-flex justify-content-evenly">
                                        <input type="hidden" name="modification_entreprise_id" value="<?php echo $ligne["company_id"]?>"/>
                                        <input type="submit" class="bouton" value="Modifier"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="suppression" tabindex="-1" aria-labelledby="Supprimer" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header deco">
                        <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left"></i></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-center"> Êtes-vous sûr(e) de vouloir supprimer cette entreprise ? </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-dismiss="modal"> Annuler </button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <form action="detailEntreprise.php" method="post">
                                        <input type="hidden" name="suppression_entreprise_id" value="<?php echo $ligne["company_id"]?>"/>
                                        <input type="submit" class="bouton" value="Supprimer"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte lien_inactif">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="#">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../../ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        Entreprises
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        Etudiants
                    </li>
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone">
                        </a>
                        Gestionnaires
                    </li>
                    <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../../ressources/icone_forum_black.svg" alt="Paramètres du forum" class="icone">
                        </a>
                        Forum
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>
