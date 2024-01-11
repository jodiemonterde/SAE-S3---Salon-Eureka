<?php 
    session_start();
    require("../../../fonctions/baseDeDonnees.php");
    require("../../../fonctions/fonctionsImportationExportation.php");
    $pdo = connecteBD();
    if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A'){
        header('Location: ../../connexion.php');
    }
    if(isset($_POST['dateForum']) && isset($_POST['heureDebut']) && isset($_POST['heureFin']) && isset($_POST['duree']) && isset($_POST['secDuree']) && isset($_POST['dateLim'])){
        updateForum($pdo,$_POST['dateForum'],$_POST['heureDebut'],$_POST['heureFin'],$_POST['duree'],$_POST['secDuree'],$_POST['dateLim']);
    }
    $phase = getPhase($pdo);
    $filieres = array();
    $stmt = getFields($pdo);
    while ($ligne = $stmt->fetch())  {
        $filieres[$ligne["name"]] = $ligne["field_id"];
    }
    if(isset($_FILES['file'])) {
        $_SESSION["reponse import"] = importerEtudiants($_FILES['file']['tmp_name'], $filieres, $pdo);
        HEADER("Location: importExport.php");
        exit();
    }
    if (isset($_SESSION["reponse import"])) {
        $reponse = $_SESSION["reponse import"];
        unset($_SESSION["reponse import"]);
    }
    if(isset($_POST["listeEntreprise"]) && $_POST["listeEntreprise"] != 0 && $_POST["listeEntreprise"] != "T" ){
        exportEntreprise($_POST["listeEntreprise"],$pdo);
    }
    if(isset($_POST["listeEtudiant"]) && $_POST["listeEtudiant"] != 0 && $_POST["listeEtudiant"] != "T"){
        exportEtudiant($_POST["listeEtudiant"],$pdo);
    }
    if(isset($_POST["listeEntrepriseExclue"]) && $_POST["listeEntrepriseExclue"] != 0 && $_POST["listeEntrepriseExclue"] != "T"){
        exportEntrepriseExclu($_POST["listeEntrepriseExclue"],$pdo);
    }
    if(isset($_POST["listeEntreprise"]) && $_POST["listeEntreprise"] == "T" ){
        exportAllEntreprise($pdo);
    }
    if(isset($_POST["listeEtudiant"]) && $_POST["listeEtudiant"] == "T"){
        exportAllEtudiant($pdo);
    }
    if(isset($_POST["listeEntrepriseExclue"]) && $_POST["listeEntrepriseExclue"] == "T" ){
        exportAllEntrepriseExclu($pdo);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../../../css/all.css">
        <link rel="stylesheet" href="../../../css/navbars.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <link rel="stylesheet" href="../../../css/importExport.css">
        <title>informations eureka</title>
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
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeEntreprises.php"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeEtudiants.php"> Liste des étudiants </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeGestionnaires.php"> Liste des gestionnaires </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur les paramètres du forum, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Paramètres du forum </a>
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
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="../listeEntreprises.php">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="../listeEntreprises.php">
                        Entreprises
                        </a>
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="../listeEtudiants.php">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="../listeEtudiants.php">
                        Etudiants
                        </a>
                    </li>
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="../listeGestionnaires.php">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="../listeGestionnaires.php">
                        Gestionnaires
                        </a>
                    </li>
                    <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_forum_white.svg" alt="Paramètres du forum" class="icone">
                        </a>
                        Forum
                    </li>
                </ul>
            </div>
        </nav>
        <div class="container">
            <div class="row mx-1">
                <div class="col-1">
                    <a href="menu.php">
                        <button type="button" class="blanc mt-2"><i class="fa-solid fa-arrow-left fa-3x"></i></button>
                    </a>
                </div>
                <div class="col-11">
                    <h1 class="text-center">Importation / Exportation</h1>
                </div>
            </div>
            <?php if (isset($reponse)) { ?>
                <div class="row mx-1">
                    <div class="col-12">
                        <h2 class="text-center <?php echo $reponse == "Importation réussie" ? "text-accent" : "erreur";?>">Résultat de l'importation</h2>
                        <p class="text-center"><?php echo $reponse; ?></p>
                    </div>
                </div>
            <?php } ?>
            <?php if ($phase == 1) { ?>
            <div class="accordion" id="importEtudiants">
                <div class="accordion-item my-3">
                    <h2 class="accordion-header" id="headingImportEtudiants">
                        <button class="accordion-button collapsed text-accent" type="button" data-bs-toggle="collapse" data-bs-target="#collapseImportEtudiants" aria-expanded="false" aria-controls="collapseImportEtudiants">
                            Importer des étudiants
                        </button>
                    </h2>
                    <div id="collapseImportEtudiants" class="accordion-collapse collapse" aria-labelledby="headingImportEtudiants" data-bs-parent="#importEtudiants">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-12">
                                    <p>
                                    Cette section vous permet d'importer une grande quantité d'étudiants à l'aide d'un fichier csv.<br/>
                                    Ce fichier devra être formaté de la manière suivante :<br/>
                                    nom d'utilisateur;adresse mail;mot de passe;filière</br>
                                    Voici un exemple de contenu de fichier :<br/>
                                    Jean Dupont;jean.dupont@example.com;mot_de_passe_123;Informatique<br/>
                                    Marie Dupont;marie.dupont@example.com;mot_de_passe_456;GEA<br/>
                                    ATTENTION : le fichier ne doit pas contenir d'entête !<br/>
                                    RAPPEL : le mot de passe doit : <br/>
                                    - contenir au moins 8 caractères <br/>
                                    - contenir au moins un chiffre <br/>
                                    - contenir au moins un caractère spécial <br/>
                                    Les noms de filières doivent faire partie de cette liste : <?php 
                                    
                                    while ($ligne = $stmt->fetch())  {
                                        echo $ligne["name"].", "; }?>
                                    </p>
                                    
                                    <form action="importExport.php" method="post" enctype="multipart/form-data">
                                        <label for="file">Choisir un fichier :</label>
                                        <input type="file" name="file" id="file" accept=".csv" required>
                                        <input class="bouton" type="submit" value="Importer" name="submit">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php } else { ?>
                <div class="row">
                    <div class="col-12 text-center erreur">
                        <h4>Les options d'importations sont désormé indisponibles une fois le planning généré !</h4>
                    </div>
                </div>
            <?php } 
            if ($phase == 2) { ?>
            <div class="accordion" id="exporterPlanningEntreprise">
                <div class="accordion-item my-3">
                    <h2 class="accordion-header" id="headingPlanningEntreprise">
                        <button class="accordion-button collapsed text-accent" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePlanningEntreprise" aria-expanded="false" aria-controls="collapsePlanningEntreprise">
                            Exporter le planning d'une / des entreprise(s)
                        </button>
                    </h2>
                    <div id="collapsePlanningEntreprise" class="accordion-collapse collapse" aria-labelledby="headingPlanningEntreprise" data-bs-parent="#exporterPlanningEntreprise">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="importExport.php" method="post">
                                        Entreprise :
                                        <select name="listeEntreprise">
                                            <option value="0">Veuillez selectionner une entreprise</option>
                                            <option value="T">toutes</option>
                                            <?php
                                                $ListeEntrepriseNonExclue = getCompanyNotExcluded($pdo);
                                                while($row = $ListeEntrepriseNonExclue->fetch()){
                                            ?>
                                                    <option value=<?php echo $row["company_id"];?> 
                                                <?php
                                                    if(isset($_POST["listeEntreprise"]) && $_POST["listeEntreprise"] == $row["company_id"]){
                                                        echo ' selected';
                                                    }
                                                ?>
                                                    > <?php echo $row["name"];?> </option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                        <input class="bouton" type="submit" value="exporter" name="exporter">
                                    </form> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="exporterEtatEntreprises">
                <div class="accordion-item my-3">
                    <h2 class="accordion-header" id="headingEtatEntreprises">
                        <button class="accordion-button collapsed text-accent" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEtatEntreprises" aria-expanded="false" aria-controls="collapseEtatEntreprises">
                            Exporter un etat d'une / des entreprise(s) sans planning
                        </button>
                    </h2>
                    <div id="collapseEtatEntreprises" class="accordion-collapse collapse" aria-labelledby="headingEtatEntreprises" data-bs-parent="#exporterEtatEntreprises">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-12">
                                    <?php 
                                        $ListeEntrepriseExclue = getCompanyExcluded($pdo);
                                        if($ListeEntrepriseExclue->rowCount() > 0){
                                    ?>
                                        <form action="importExport.php" method="post">
                                        Entreprise exclue :
                                        <select name="listeEntrepriseExclue">
                                            <option value="0">Veuillez selectionner une entreprise</option>
                                            <option value="T">toutes</option>
                                            <?php
                                                while($row = $ListeEntrepriseExclue->fetch()){
                                            ?>
                                                    <option value=<?php echo $row["company_id"];?> 
                                                <?php
                                                    if(isset($_POST["listeEntrepriseExclue"]) && $_POST["listeEntrepriseExclue"] == $row["company_id"]){
                                                        echo ' selected';
                                                    }
                                                ?>
                                                    > <?php echo $row["name"];?> </option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                        <input class="bouton" type="submit" value="exporter" name="exporter">
                                    </form> 
                                    <?php
                                        } else {
                                    ?>
                                        <p class="erreur"> aucune entreprise n'est exclue du planning </p>
                                    <?php
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="accordion" id="exportEtudiant">
                <div class="accordion-item my-3">
                    <h2 class="accordion-header" id="headingExportEtudiant">
                        <button class="accordion-button collapsed text-accent" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExportEtudiant" aria-expanded="false" aria-controls="collapseExportEtudiant">
                            Exporter le planning d'un étudiant
                        </button>
                    </h2>
                    <div id="collapseExportEtudiant" class="accordion-collapse collapse" aria-labelledby="headingExportEtudiant" data-bs-parent="#exportEtudiant">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-12">
                                    <form action="importExport.php" method="post">
                                        Etudiant :
                                        <select name="listeEtudiant">
                                            <option value="0">Veuillez selectionner un etudiant</option>
                                            <option value="T">tous</option>
                                            <?php
                                                $listeEtudiant = getStudentsWithMeeting($pdo);
                                                while($row = $listeEtudiant->fetch()){
                                            ?>
                                                    <option value=<?php echo $row["user_id"];?> 
                                                <?php
                                                    if(isset($_POST["listeEtudiant"]) && $_POST["listeEtudiant"] == $row["user_id"]){
                                                        echo ' selected';
                                                    }
                                                ?>
                                                    > <?php echo $row["username"];?> </option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                        <input class="bouton" type="submit" value="exporter" name="exporter">
                                    </form> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php } else { ?>
            <div class="row">
                <div class="col-12 text-center erreur">
                    <h4>Les options d'exportations ne sont pas encore disponibles ! Il faut que le planning soit généré et validé pour cela !</h4>
                </div>
            </div>
        <?php } ?>
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