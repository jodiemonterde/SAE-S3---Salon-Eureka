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
        
        header("Location: detailEtudiant.php");
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
    <link rel="stylesheet" href="../../../../outils/bootstrap-5.3.2-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../../../../outils/fontawesome-free-6.5.1-web/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../../../outils/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <link rel="stylesheet" href="listeEtudiant.css">
    <link rel="stylesheet" href="../../../css/navbars.css">
    <link rel="stylesheet" href="filtre.css">
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
                        <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                        <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des étudiants </a>
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
                <h2>Liste des étudiants</h2>
                <p>Voici tous les étudiants inscrits au forum Eureka de cette année. Cliquez sur l’un d’eux pour voir la liste des entreprises auprès desquels il souhaite obtenir un rendez-vous !</p>
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
            <!--<div id="collapse<?php echo $ligne['user_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['user_id']?>" data-bs-parent="#listeEntreprise">
                <div class="accordion-body pb-1">
                    <div class="row m-0">
                        <?php
                           /* $stmt2 = getEntreprisesPerStudent($pdo, $ligne['user_id']);
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
                        */?>
                    </div>
                </div>
            </div>-->
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