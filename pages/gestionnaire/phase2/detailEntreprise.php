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
    include("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
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
    <link rel="stylesheet" href="../../../css/listeEntreprise.css">
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
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Mes rendez-vous </a>
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
            <!-- Accordéon Bootstrap -->
            <div class="accordion" id="listeEntreprise">
            <?php
                $stmt = getEntreprisesPhase2($pdo, $_SESSION['filtre'], $_SESSION['recherche']);

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
                            <div class="dp"><img src="../../.../../../ressources/no-photo.png" alt=""></div>
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
                            <?php if ($ligne["excluded"] === 1) { ?>
                            <p class="fw-bold text-danger">Le planning de l'entreprise <?php echo $ligne["name"]; ?> ne peut pas être généré : trop d’étudiants souhaitent la rencontrer ! Ci-dessous, la liste des étudiants intéressés par <?php echo $ligne["name"]; ?>.</p>
                            <?php }
                            $stmtEtudiant = getStudentsAppointmentsPerCompany($pdo, $ligne["company_id"], $ligne["excluded"]);
                            if ($stmtEtudiant->rowCount() === 0) {
                                echo '<hr><p class="fw-bold text-danger">Aucun étudiant ne souhaite rencontrer cette entreprise.</p>';
                            }
                            while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                            ?>
                            <hr>
                            <?php if ($ligne["excluded"]!= 1) { ?>
                            <p class="text-center fw-bold fs-5"><?php echo $ligneEtudiant["start"].'-'.$ligneEtudiant["duration"]?></p>
                            <?php } ?>
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

        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <!-- Si sur la liste des entreprises, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="#">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        Entreprises
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        Etudiants
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>
