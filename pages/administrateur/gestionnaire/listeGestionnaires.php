<?php
    try {
        session_start();

        // Stocke la valeur de $_POST['recherche'] dans $_SESSION['recherche'] si définie
        $_SESSION['recherche'] = $_POST['recherche'] ?? $_SESSION['recherche'] ?? null;

        include("../../../fonctions/baseDeDonnees.php");
        $pdo = connecteBD();

        // $_SESSION['filtre'] est un tableau qui contient les id des filtres selectionnes
        if (!isset($_SESSION['filtre']) || $_SESSION['filtre'] == null) {
            $_SESSION['filtre'] = array();
        }
        if (isset($_POST['nouveauFiltre'])) {
            if (in_array($_POST['nouveauFiltre'], $_SESSION['filtre'])) {
                $index = array_search($_POST['nouveauFiltre'], $_SESSION['filtre']);
                array_splice($_SESSION['filtre'], $index, 1);
            } else {
                array_push($_SESSION['filtre'], $_POST['nouveauFiltre']);
            }
            
            header("Location: listeGestionnaires.php");
            exit();
        }
        
        if (isset($_POST['nomGestionnaire'])) {
            $_POST['filieresGestionnaire'];
            addNewSupervisor($pdo, $_POST['prenomGestionnaire'], $_POST['nomGestionnaire'], $_POST['emailGestionnaire'], $_POST['motDePasseGestionnaire'], $_POST['filieresGestionnaire']);
            header("Location: listeGestionnaires.php");
            exit();
        }

        if (isset($_POST['supprimer'])) {
            deleteSupervisor($pdo, $_POST['supprimer']);
        }

        if (isset($_POST['modifyPassword'])) {
            modifyPassword($pdo, $_POST['modifyPassword'], $_POST['newPassword']);
        }
        $fields = getFields($pdo);
        $tmp = [];
        while ($ligne = $fields->fetch()) {
            $tmp[$ligne['field_id']] = $ligne['name'];
        }
        $fields = $tmp;
        $stmt = getInfosSupervisors($pdo, $_SESSION['recherche'], $_SESSION['filtre']);
        if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A'){
            header('Location: ../../connexion.php');
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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="../../../css/all.css">
    <link rel="stylesheet" href="../../../css/listeGestionnairesAdministrateur.css">
    <link rel="stylesheet" href="../../../css/navbars.css">
    <link rel="stylesheet" href="../../../css/filtre.css">
    <title>Eureka - Liste des entreprises</title>
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
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../etntreprises/listeEntreprises.php"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../etudiants/listeEtudiants.php"> Liste des étudiants </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Liste des gestionnaires </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur les paramètres du forum, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../forum/menu.php"> Paramètres du forum </a>
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

    <div class="container mt-2">
        <div class="row d-flex align-items-center h-100">
            <div class="col-12 col-md-6">
                <h2>Liste des gestionnaires</h2>
                <p>Voici tous les gestionnaires du forum Eureka de cette année. Cliquez sur l’un des gestionnaires pour pouvoir modifiers son mot de passe ou ses informations personnelles.</p>
            </div>
            <form action="listeGestionnaires.php" method="post" class="col-12 col-md-6 my-2">
                <div class="row">
                    <div class="col-8">
                        <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher un gestionnaire" class="zoneText"/>    
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
                        foreach ($fields as $key => $field) {
                    ?>
                    <form action="listeGestionnaires.php" method="post">
                        <input type="hidden" name="nouveauFiltre" value="<?php echo $key; ?>">
                        <button class="bouton-filtre <?php echo in_array($key, $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $field; ?></button>
                    </form>
                    <?php } ?>
                </div>
            </div>
        </div>
        <hr class="mb-4">
        <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddStudent">
            <i class="fa-solid fa-plus text-left justify-content-center"></i>
            <h2 class="text-center m-2">Ajouter un(e) gestionnaire</h2>
        </button>

        <div class="modal fade" id="modalAddStudent" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content  px-4 pb-4">
                    <div class="modal-header deco justify-content-start px-0">
                        <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                        <h2 class="modal-title" id="addStudentModalLabel">Nouveau gestionnaire</h2>
                    </div>
                    <h2>Informations sur le gestionnaire</h2>
                    <form action="listeGestionnaires.php" method="post">
                        <label for="email" class="modalLabel mb-0 mt-2">Adresse mail / Identifiant</label>
                        <input class="zoneText" type="email" name="emailGestionnaire" placeholder="Saisir l'adresse mail du gestionnaire" required/>
                        <label for="prenomGestionnaire" class="modalLabel mb-0 mt-2">Prénom</label>
                        <input class="zoneText" type="text" name="prenomGestionnaire" placeholder="Saisir le prénom du gestionnaire" required/>
                        <label for="nomGestionnaire" class="modalLabel mb-0 mt-2">Nom</label>
                        <input class="zoneText" type="text" name="nomGestionnaire" placeholder="Saisir le nom du gestionnaire" required/>
                        <label for="filiereGestionnaire" class="modalLabel mb-0 mt-2">Filière</label>
                        <div class="rowForChecks">
                            <?php
                            foreach ($fields as $key => $field) { ?>
                                <label class="buttonToCheck">
                                    <input type="checkbox" name="filieresGestionnaire[]" value="<?php echo $key;?>" />
                                    <div class="icon-box">
                                        <span><?php echo $field;?></span>
                                    </div>
                                </label>
                            <?php } ?>
                            </div>
                        <p class="modalLabel mb-0 mt-2">Mot de passe (8 caractères minimum dont au moins un symbole - à transmettre au gestionnaire !)</p>
                        <input class="zoneText mb-3" type="text" name="motDePasseGestionnaire" placeholder="Saisir le mot de passe" pattern="^(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$" required/>
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
        <div class="accordion" id="listeGestionnaires">
        <?php
            if (empty($_SESSION['filtre'])) {
                echo '<p>Aucune filière sélectionnée. Veuillez choisir au moins une filière.</p>';
            } elseif ($stmt->rowCount() === 0) {
                echo '<p>Aucun résultat trouvé.</p>';
            } else {
                while ($ligne = $stmt->fetch()) { 
        ?>
        <div class="accordion-item my-3">
            <h2 class="accordion-header" id="heading<?php echo $ligne['user_id']?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['user_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['user_id']?>">
                    <div class="profil-det-img d-flex text-start">
                        <div class="pd detailEtudiant">
                            <h2 class="title"><?php echo $ligne["username"]?></h2>
                            <?php echo $ligne["filieres"]?></br>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse<?php echo $ligne['user_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['user_id']?>" data-bs-parent="#listeEntreprise">
                <div class="accordion-body pb-1 pt-0">
                    <div class="row m-0">
                        <div class="row my-3">
                            <div class="col-md-6 py-2">
                                <button class="boutonNegatif" data-bs-toggle="modal" data-bs-target="#modalDeleteStudent<?php echo $ligne['user_id']; ?>">Supprimer</button>
                            </div>
                            <div class="col-md-6 py-2">
                                <button class="bouton col-md-6" data-bs-toggle="modal" data-bs-target="#modalModifyPassword<?php echo $ligne['user_id']; ?>">Modifier le mot de passe</button>
                            </div>
                        </div>
                            <div class="modal fade" id="modalDeleteStudent<?php echo $ligne['user_id']; ?>" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content px-4 pb-4">
                                        <div class="modal-header deco justify-content-start px-0">
                                            <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                            <h2 class="modal-title" id="deleteStudentModalLabel"><?php echo $ligne['username'];?></h2>
                                        </div>
                                        <div class="modal-body">
                                            <h2>Êtes-vous sûr(e) de vouloir supprimer ce gestionnaire ?</h2>
                                            <form action="listeGestionnaires.php" method="post">
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

                            <div class="modal fade" id="modalModifyPassword<?php echo $ligne['user_id']; ?>" tabindex="-1" aria-labelledby="modifyPasswordLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content px-4 pb-4">
                                        <div class="modal-header deco justify-content-start px-0">
                                            <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                            <h2 class="modal-title" id="modifyPassword"><?php echo $ligne['username'];?></h2>
                                        </div>
                                        <div class="modal-body">
                                            <form action="listeGestionnaires.php" method="post">
                                                <label for="newPassword"  class="modalLabel mb-0 mt-2">Choisir un nouveau mot de passe (à transmettre au gestionnaire !) :</label>
                                                <input type="text" class="zoneText mb-3" name="newPassword" pattern="^(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$" placeholder="Saisir un mot de passe" required>
                                                <input type="hidden" name="modifyPassword" value="<?php echo $ligne['user_id'];?>">
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
            </div>
        </div>
        <?php   } 
            } ?>
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
                <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center actif_bas_icone">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_gestionnaire_white.svg" alt="Liste des gestionnaires" class="icone">
                    </a>
                    Gestionnaires
                </li>
                <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                <li class="nav-item d-flex flex-column text-center inactif_bas">
                    <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center" href="../forum/menu.php">
                        <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_forum_black.svg" alt="Paramètres du forum" class="icone">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse" href="../forum/menu.php">
                    Forum
                    </a>
                </li>
            </ul>
        </div>
    </nav>
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
