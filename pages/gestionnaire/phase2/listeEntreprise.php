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
        
        header("Location: listeEntreprise.php");
        exit();
    }
    try {
        require("../../../fonctions/baseDeDonnees.php");
        $pdo = connecteBD();
        $fields = getFieldsPerUsers($pdo, $_SESSION['idUtilisateur']);
        $stmt = getEntreprisesPhase2($pdo, $_SESSION['filtre'], $_SESSION['recherche']);
        if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 2 || $_SESSION['type_utilisateur'] != 'G'){
            header('Location: ../../connexion.php');
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
    <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="../../../css/all.css">
    <link rel="stylesheet" href="../../../css/listeEntrepriseGestionnaire2.css">
    <link rel="stylesheet" href="../../../css/navbars.css">
    <link rel="stylesheet" href="../../../css/filtre.css">
    <title>Eureka - Liste des entreprises</title>
</head>
    <body>
        <!-- Navbar du haut -->
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
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEtudiant.php"> Liste des étudiants </a>
                        </li>
                        <li class="nav-item dropdown p-0 h-100 d-none d-md-block">
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION['nom_utilisateur'])?>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#deconnexion"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-md-none d-flex justify-content-end">
                            <a  data-bs-toggle="modal" data-bs-target="#deconnexion">
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
                    <h2>Liste des entreprises</h2>
                    <p>Voici toutes les entreprises présentes au salon Euréka cette année. Cliquez sur l’une d’elle pour voir tous les étudiants qui veulent un rendez-vous avec celle-ci ! Vous pouvez également filtrer quelles filières vous intéressent grâce à la liste de filtres ci-dessous.</p>
                </div>
                <form action="listeEntreprise.php" method="post" class="col-12 col-md-6 my-2">
                    <div class="row">
                        <div class="col-8">
                            <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher une entreprise" class="entreeUtilisateur"/>    
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
                    <?php
                        if ($fields->rowCount() > 1) {
                            echo '<h2>Filières</h2>';
                        while ($ligne = $fields->fetch()) {
                    ?>
                    <form action="listeEtudiant.php" method="post">
                        <input type="hidden" name="nouveauFiltre" value="<?php echo $ligne['field_id']; ?>">
                        <button class="bouton-filtre <?php echo in_array($ligne['field_id'], $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $ligne['name']; ?></button>
                    </form>
                    <?php } } else {
                            $_SESSION['filtre'] = [];
                            array_push($_SESSION['filtre'], $fields->fetch()['field_id']);
                        } ?>
                </div>
            </div>
        </div>
            <!-- Accordéon Bootstrap -->
            <div class="accordion" id="listeEntreprise">
            <?php
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
                            <div class="dp"><img src="../../../ressources/<?php echo $ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "no-photo.png"?>" alt=""></div>
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
                            try {
                                if ($ligne["excluded"] === 1) { ?>
                                <p class="fw-bold text-danger">Le planning de l'entreprise <?php echo $ligne["name"]; ?> ne peut pas être généré : trop d’étudiants souhaitent la rencontrer ! Ci-dessous, la liste des étudiants intéressés par <?php echo $ligne["name"]; ?>.</p>
                                <?php $stmtEtudiant = getStudentsPerCompanyWishList($pdo, $ligne['company_id']);
                                    while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                                    ?>
                                    <hr>
                                    <h2 class="student"><?php echo $ligneEtudiant["username"]?></h2>
                                    <p><?php echo $ligneEtudiant["name"]?></p>
                                    <?php }
                                    } else { 
                                        $speakers = getSpeakersPerCompany($pdo, $ligne['company_id']);
                                        while ($speaker = $speakers->fetch()) {
                                            
                                            echo '<hr><h5 class="fw-bold text-center">Intervenant : '.$speaker['name'].' - '.$speaker['role'].'</h5>';
                                            $stmtEtudiant = getAppointmentPerSpeaker($pdo, $speaker['speaker_id']);
                                            if ($stmtEtudiant->rowCount() === 0) {
                                                echo '<hr><p class="fw-bold text-danger">Aucun étudiant ne souhaite rencontrer cette entreprise avec cet intervenant.</p>';
                                            }
                                            while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                                            ?>
                                            <hr>
                                            <p class="text-center fw-bold fs-5"><?php echo $ligneEtudiant["start"].'-'.$ligneEtudiant["end"]?></p>
                                            <h2 class="student"><?php echo $ligneEtudiant["username"]?></h2>
                                            <p><?php echo $ligneEtudiant["name"]?></p>
                                        <?php } }  
                                    }
                            } catch (Exception $e) {
                                redirect("../../maintenance.php");
                            }?>
                        </div>
                    </div>
                </div>
            </div>
            <?php } 
            } ?>
        </div>

        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <!-- Si sur la liste des entreprises, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        Entreprises
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="listeEtudiant.php">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        Etudiants
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