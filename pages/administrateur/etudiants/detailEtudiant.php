<?php
    session_start();
    $user = 1;

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
            unset($_SESSION['filtre'][$index]);
        } else {
            array_push($_SESSION['filtre'], $_POST['nouveauFiltre']);
        }
        
        header("Location: detailEtudiant.php");
        exit();
    }
    
    if (isset($_POST['nomEtudiant'])) {
        addNewStudent($pdo, $_POST['prenomEtudiant'], $_POST['nomEtudiant'], $_POST['emailEtudiant'], $_POST['motDePasseEtudiant'], $_POST['filiereEtudiant']);
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="../../../css/listeEtudiant.css">
    <link rel="stylesheet" href="../../../css/navbars.css">
    <link rel="stylesheet" href="../../../css/filtre.css">
    <title>Eureka - Liste des entreprises</title>
</head>
<body>
     <!-- Navbar du haut -->
     <nav class="navbar navbar-expand sticky-top border bg-white">
        <div class="container-fluid h-100">
            <div class="navbar-brand d-flex align-items-center h-100">
                <img src="../../../ressources/logo_black.png" alt="Logo Eureka" class="d-inline-block align-text-top me-2">
                Eureka
            </div>
            <div class="navbar-right h-100">
                <ul class="navbar-nav">
                    <li class="nav-item nav-link p-0 d-none d-md-block h-100">
                        <!-- Si sur la liste des entreprises, mettre en jaune -->
                        <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEntreprises.php"> Liste des entreprises </a>
                    </li>
                    <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                        <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                        <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Mes rendez-vous </a>
                    </li>
                    <li class="nav-item dropdown p-0 h-100 d-none d-md-block">
                        <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Pseudo Utilisateur
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li> <a class="dropdown-item" href="../../deconnexion.php"> Se déconnecter </a> </li>
                        </ul>
                    </li>
                    <li class="nav-item d-md-none d-flex justify-content-end">
                        <a href="../../deconnexion.php">
                            <img src="../../../ressources/icone_deconnexion.png" alt="Se déconnecter">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-2">
        <div class="row d-flex align-items-center h-100">
            <div class="col-12 col-md-6">
                <h2>Liste des étudiants</h2>
                <p>Voici tous les étudiants inscrits au forum Eureka de cette année. Cliquez sur l’un d’eux pour voir la liste des entreprises auprès desquels il souhaite obtenir un rendez-vous ! Vous pouvez également créer un nouvel étudiant, en supprimer un ou modifier le mot de passe d'un étudiant.</p>
            </div>
            <form action="detailEtudiant.php" method="post" class="col-12 col-md-6 my-2">
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
                    <form action="detailEtudiant.php" method="post">
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
            <h2 class="text-center m-2">Ajouter un(e) étudiant(e)</h2>
        </button>

        <div class="modal fade" id="modalAddStudent" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content  px-4 pb-4">
                    <div class="modal-header deco justify-content-start px-0">
                        <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                        <h2 class="modal-title" id="addStudentModalLabel">Nouvel(le) étudiant(e)</h2>
                    </div>
                    <h2>Informations sur l’étudiant</h2>
                    <form action="detailEtudiant.php" method="post">
                        <label for="email" class="modalLabel mb-0 mt-2">Adresse mail / Identifiant</label>
                        <input class="zoneText" type="email" name="emailEtudiant" placeholder="Saisir l'adresse mail de l'étudiant" required/>
                        <label for="nomEtudiant" class="modalLabel mb-0 mt-2">Nom</label>
                        <input class="zoneText" type="text" name="nomEtudiant" placeholder="Saisir le nom de l’étudiant" required/>
                        <label for="prenomEtudiant" class="modalLabel mb-0 mt-2">Prénom</label>
                        <input class="zoneText" type="text" name="prenomEtudiant" placeholder="Saisir le prénom de l’étudiant" required/>
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
            $stmt = getInfoStudents($pdo, $_SESSION['recherche'], $_SESSION['filtre']);
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
                            <?php echo $ligne["filiere"]?></br>
                            <span class="<?php echo $ligne["nbSouhait"] < 1 ? "erreur" : ""?>"> <?php echo $ligne["nbSouhait"]?> souhaits </span>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse<?php echo $ligne['user_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['user_id']?>" data-bs-parent="#listeEntreprise">
                <div class="accordion-body pb-1 pt-0">
                    <div class="row m-0">
                        <div class="row my-3">
                            <div class="col-md-6 py-2">
                                <button class="boutonNegatif" data-bs-toggle="modal" data-bs-target="#modalDeleteStudent">Supprimer</button>
                            </div>
                            <div class="modal fade" id="modalDeleteStudent" tabindex="-1" aria-labelledby="deleteStudentModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                    <div class="modal-content  px-4 pb-4">
                                        <div class="modal-header deco justify-content-start px-0">
                                            <button type="button" class="blanc" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                            <h2 class="modal-title" id="deleteStudentModalLabel"><?php echo $ligne['username'];?></h2>
                                        </div>
                                        <h2>Êtes-vous sûr(e) de vouloir supprimer cet(te) étudiant(e) ?</h2>
                                        <form action="detailEtudiant.php" method="post">
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
                            <div class="col-md-6 py-2">
                                <button class="bouton col-md-6" data-bs-toggle="modal" data-bs-target="#modalModifyPassword">Modifier le mot de passe</button>
                            </div>
                        </div>
                        <hr>
                        <?php
                            $stmt2 = getEntreprisesPerStudent($pdo, $ligne['user_id']);
                            if ($ligne["nbSouhait"] < 1) {
                                echo "<p class='erreur text-center'>Cet(te) étudiant(e) n'a pris aucun rendez-vous pour l'instant !</p>";
                            }
                            $rowNumber = 0;
                            while ($ligne2 = $stmt2->fetch()) {
                             $rowNumber++;
                             if ($rowNumber != 1) {
                                echo '<hr>';
                             }
                             ?> 
                                <div>
                                    <div class="profil-det-img d-flex text-start">
                                        <div class="dp"><img src="../../.../../../ressources/<?php echo $ligne2["logo_file_name"] != "" ? $ligne2["logo_file_name"] : "no-photo.png"?>" alt="Logo de l'entreprise"></div>
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
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php   } 
            } ?>
    </div>

    

     <!-- Navbar du bas -->
    <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
        <div class="container-fluid">
            <ul class="navbar-nav w-100 justify-content-evenly">
                <li class="nav-item actif_bas_texte">
                    <!-- Si sur la liste des entreprises, mettre l'icone blanche et le fond en jaune -->
                    <a class="d-flex justify-content-center actif_bas_icone" href="#">
                        <img src="../../../ressources/entreprise_white.png" alt="Liste des entreprises">
                    </a>
                    <!-- Si sur la liste des entreprises, mettre en jaune -->
                    Entreprises
                </li>
                <li class="nav-item inactif_bas">
                    <!-- Si sur la liste des rendez-vous, mettre l'icone blanche et le fond en jaune -->
                    <a class="d-flex justify-content-center" href="#">
                        <img src="../../../ressources/rendez-vous_black.png" alt="Mes rendez-vous">
                    </a>
                    <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                    Rendez-vous
                </li>
            </ul>
        </div>
    </nav>   
</body>
</html>
