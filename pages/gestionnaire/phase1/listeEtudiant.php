<?php
    // Démarrage d'une session
    session_start();

    // Stocke la valeur de $_POST['recherche'] dans $_SESSION['recherche'] si définie
    $_SESSION['recherche'] = isset($_POST['recherche']) ? htmlspecialchars($_POST['recherche']) : ($_SESSION['recherche'] ?? null);

    // $_SESSION['filtre'] est un tableau qui contient les id des filtres selectionnes
    if (!isset($_SESSION['filtre']) || $_SESSION['filtre'] == null) {
        $_SESSION['filtre'] = array();
    }

    // Gère l'affichage des entreprises en fonction des filtres
    if (isset($_POST['nouveauFiltre'])) {
        if (in_array(htmlspecialchars($_POST['nouveauFiltre']), $_SESSION['filtre'])) {
            $index = array_search(htmlspecialchars($_POST['nouveauFiltre']), $_SESSION['filtre']);
            unset($_SESSION['filtre'][$index]);
        } else {
            array_push($_SESSION['filtre'], htmlspecialchars($_POST['nouveauFiltre']));
        }
        
        header("Location: listeEtudiant.php");
        exit();
    }

    // Attribue comme valeur par défaut l'ordre alphabétique comme tri
    if (!isset($_SESSION['triPar'])) {
        $_SESSION['triPar'] = "alpha";
    }

    // Change l'ordre d'affichage des étudiants selon l'option de tri choisie par l'utilisateur
    if (isset($_POST['triPar'])) {
        $_SESSION['triPar'] = htmlspecialchars($_POST['triPar']);
        header("Location: listeEtudiant.php");
        exit();
    }

    try {
        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require("../../../fonctions/baseDeDonnees.php");
        require("../../../fonctions/fonctions.php");

        $pdo = connecteBD(); // accès à la Base de données
        $fields = getFieldsPerUsers($pdo, $_SESSION['idUtilisateur']); // Obtention des filières liées à l'utilisateur
        if ($fields->rowCount() === 1) { // Vérifie si le gestionnaire n'a qu'une seule filière, puis attribue la valeur de cette filière si cela est le cas
            $_SESSION['filtre'] = [];
            array_push($_SESSION['filtre'], $fields->fetch()['field_id']);
        }
        $stmt = getInfoStudentsSort($pdo, $_SESSION['recherche'], $_SESSION['filtre'], $_SESSION['triPar']); // Obtention des étudiants à afficher en fonction des filières selectionnées mais également de la recherche potentielle de l'utilisateur et du tri
        $phase = getPhase($pdo); // Obtention de la phase actuelle dans laquelle se trouve la base de donnée
        // Empêche l'accès à cette page et redirige vers la page de connexion si l'utilisateur n'est pas un étudiant correctement identifié.
        if(!isset($_SESSION['idUtilisateur']) || $phase == 2 || $_SESSION['type_utilisateur'] != 'G'){
            header('Location: ../../connexion.php');
            exit();
        }
    } catch (Exception $e) { // En cas d'erreur, redirige vers la page de site en maintenance
        header('Location: ../../maintenance.php');
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
    <link rel="stylesheet" href="../../../css/listeEtudiantGestionnaire1.css">
    <link rel="stylesheet" href="../../../css/all.css">
    <link rel="stylesheet" href="../../../css/filtre.css">
    <link rel="stylesheet" href="../../../css/navbars.css">

    <title>Eurêka - Liste des étudiants</title>
</head>
<body>
    <!-- Navbar du haut -->
    <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    <span class="logo">Eureka</span>
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-item-haut nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEntreprise.php"> Entreprises </a>
                        </li>
                        <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Étudiants </a>
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
                        <li class="nav-item d-md-none d-flex justify-content-end">
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
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des entreprises, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="listeEntreprise.php">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="listeEntreprise.php">
                            Entreprises
                        </a>
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_etudiant_white.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        Etudiants
                    </li>
                </ul>
            </div>
        </nav>

    <!-- Container principal de la page -->    
    <div class="container mt-2">
        <div class="row d-flex align-items-center h-100">
            <div class="col-12 col-md-6">
                <h2>Liste des étudiants</h2>
                <p>Voici tous les étudiants inscrits au forum Eurêka de cette année. Cliquez sur l’un d’eux pour voir la liste des entreprises auprès desquels il souhaite obtenir un rendez-vous !</p>
            </div>
            <!-- Formulaire permettant d'entrer une recherche personnalisé qui filtrera l'affichage selon celle-ci -->
            <form action="listeEtudiant.php" method="post" class="col-12 col-md-6 my-2">
                <div class="row">
                    <div class="col-12 col-md-7 p-0">
                        <input type="search" name="recherche" value="<?php echo $_SESSION['recherche']; ?>" placeholder=" &#xf002 Rechercher un etudiant" class="entreeUtilisateur"/>    
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
                    <?php
                        // Affichage des boutons de filtres uniquement s'il y en a plusieurs
                        if ($fields->rowCount() > 1) {
                            echo '<h2>Filières</h2>';
                        while ($ligne = $fields->fetch()) {
                    ?>

                    <form action="listeEtudiant.php" method="post">
                        <input type="hidden" name="nouveauFiltre" value="<?php echo $ligne['field_id']; ?>">
                        <button class="bouton-filtre <?php echo in_array($ligne['field_id'], $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $ligne['name']; ?></button>
                    </form>
                    <?php } } ?>
                </div>
            </div>
        </div>
        <hr class="m-0">

        <!-- Formulaire permettant de trier les étudiants : par ordre alphabétique, par ordre de souhait croissant, par ordre de souhait décroissant -->
        <div class="d-flex flex-row-reverse">
            <form action="listeEtudiant.php" method="post">
                <select id="triPar" name="triPar" class="form-control sort text-end" onchange="this.form.submit()">
                    <option value="default" disabled selected>&#x21C5; TRIER PAR</option>
                    <option value="alpha">Ordre alphabétique</option>
                    <option value="croissant">Nombre de <?php echo $phase == 1 ? "souhaits" : "rencontres" ?> croissant</option>
                    <option value="decroissant">Nombre de <?php echo $phase == 1 ? "souhaits" : "rencontres" ?> décroissant</option>
                </select>
            </form>
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
                        <div class="pd listeEtudiant">
                            <h2 class="title"><?php echo $ligne["firstname"] . ' ' . $ligne['lastname'];?></h2>
                            <?php echo $ligne["filiere"]?></br>
                            <span class="<?php echo $ligne["nbSouhait"] < 1 ? "erreur" : ""?>"> <?php echo $ligne["nbSouhait"]?> souhaits </span>
                        </div>
                    </div>
                </button>
            </h2>
            <div id="collapse<?php echo $ligne['user_id']?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $ligne['user_id']?>" data-bs-parent="#listeEntreprise">
                <div class="accordion-body pb-1">
                    <div class="row m-0">
                        <?php
                            try {
                                $stmt2 = getEntreprisesPerStudent($pdo, $ligne['user_id']);
                            } catch (Exception $e) {
                                redirect("../../maintenance.php");
                            }
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
                                        <div class="dp"><img src="../../../ressources/logosentreprises/<?php echo $ligne2["logo_file_name"] != "" ? $ligne2["logo_file_name"] : "no-photo.png"?>" alt="Logo de l'entreprise"></div>
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
