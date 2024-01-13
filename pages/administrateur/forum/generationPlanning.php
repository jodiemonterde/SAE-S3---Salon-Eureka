<?php 
    session_start();
    require("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
    if(!isset($_SESSION['idUtilisateur']) || $_SESSION['type_utilisateur'] != 'A' || getPhase($pdo) == 2){
        header('Location: ../../connexion.php');
        exit();
    }
    if(isset($_POST['dateForum']) && isset($_POST['heureDebut']) && isset($_POST['heureFin']) && isset($_POST['duree']) && isset($_POST['secDuree']) && isset($_POST['dateLim'])){
        updateForum($pdo,$_POST['dateForum'],$_POST['heureDebut'],$_POST['heureFin'],$_POST['duree'],$_POST['secDuree'],$_POST['dateLim']);
    }

    if (isset($_POST['action']) && isset($_POST['entreprise'])) {
        setSpecificationCompany($pdo,$_POST['action'],$_POST['entreprise']);
        HEADER('Location: generationPlanning.php');
        exit();
    }

    if (isset($_POST['action2'])) {
        switch ($_POST['action2']) {
            case 'genererPlanning':
                $resultatGeneration = genererPlanning($pdo);
                if ($resultatGeneration === "Génération réussite !") {
                    setPlanningGenerated($pdo, 1);
                } else {
                    $resultatGeneration = "Impossible de générer le planning, l'entreprise ".$resultatGeneration." ne peut pas accepter autant de rendez-vous !";
                }
                $_SESSION['resultatGeneration'] = $resultatGeneration;
                break;
            case 'acceptPlanning':
                launchPhase2($pdo);
                HEADER('Location: menu.php');
                exit();
                break;
            case 'refusePlanning':
                cancelPlanning($pdo);
                break;
        }
        HEADER('Location: generationPlanning.php');
            exit();
    }

    $isGenerated = isPlanningGenerated($pdo);
    $tmp = [];
    $entreprisesReduites = getSpecificationCompany($pdo,'entrepriseReduite', 1);
    while ($entreprise = $entreprisesReduites->fetch()) {
        $tmp[$entreprise['company_id']] = $entreprise['name'];
    }
    $entreprisesReduites = $tmp;
    $entreprisesExclues = getSpecificationCompany($pdo,'entrepriseExclusion', 1);
    $tmp = [];
    while ($entreprise = $entreprisesExclues->fetch()) {
        $tmp[$entreprise['company_id']] = $entreprise['name'];
    }
    $entreprisesExclues = $tmp;

    $entreprisesPasExclues = getSpecificationCompany($pdo,'entrepriseExclusion', 0);
    $entreprisesPasReduites = getSpecificationCompany($pdo,'entrepriseReduite', 0);
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
        <link rel="stylesheet" href="../../../css/generationPlanning.css">
        <title>informations eureka</title>
    </head>
    <body>
    <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
                <div class="container-fluid h-100">
                    <div class="navbar-brand d-flex align-items-center h-100">
                        <img src="../../../ressources/logo_black.svg" alt="Logo Eureka" class="logoDisplay me-2">
                        <span class="logoDisplay">Eureka</span>
                    </div>
                    <div class="navbar-right h-100">
                        <ul class="navbar-nav d-flex h-100 align-items-center">
                            <li class="nav-item nav-item-haut nav-link p-0 d-none d-md-block h-100">
                                <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                                <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeEntreprises.php"> Entreprises </a>
                            </li>
                            <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                                <!-- Si sur la liste des étudiants, mettre en actif et lien_inactif -->
                                <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeEtudiants.php"> Étudiants </a>
                            </li>
                            <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                                <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                                <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="../listeAdministrateurs.php"> Administrateurs </a>
                            </li>
                            <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block">
                                <!-- Si sur la liste des gestionnaires, mettre en actif et lien_inactif -->
                                <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"  href="../listeGestionnaires.php"> Gestionnaires </a>
                            </li>
                            <li class="nav-item nav-item-haut nav-link p-0 h-100 d-none d-md-block lien_inactif">
                                <!-- Si sur les paramètres du forum, mettre en actif et lien_inactif -->
                                <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Forum </a>
                            </li>
                            <li class="nav-item nav-item-haut dropdown p-0 h-100 d-none d-md-block">
                                <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php echo $_SESSION['prenom_utilisateur'] . ' ' . $_SESSION['nom_utilisateur']; ?>
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li> <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#deconnexion"> Se déconnecter </a> </li>
                                </ul>
                            </li>
                            <li class="nav-item nav-item-haut d-md-none d-flex justify-content-end">
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
                    <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center inactif_bas_icone" href="../listeEntreprises.php">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone_admin">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeEntreprises.php">
                            Entreprises
                        </a>
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center inactif_bas_icone" href="../listeEtudiants.php">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone_admin">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeEtudiants.php">
                            Etudiants
                        </a>
                    </li>
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="../listeAdministrateurs.php">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeAdministrateurs.php">
                            Admins
                        </a>
                    </li>
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="../listeGestionnaires.php">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeGestionnaires.php">
                            Gestionnaires
                        </a>
                    </li>
                    <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                    <li class="nav-item nav-item-bas d-flex flex-column text-center actif_bas_texte actif_bas_texte_admin">
                        <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_forum_white.svg" alt="Paramètres du forum" class="icone_admin">
                        </a>
                        Forum
                    </li>
                </ul>
            </div>
        </nav>
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
        <div class="container-fluid">
            <ul class="navbar-nav w-100 justify-content-evenly">
                <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone" href="../listeEntreprises.php">
                        <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeEntreprises.php">
                        Entreprises
                    </a>
                </li>
                <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone" href="../listeEtudiants.php">
                        <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeEtudiants.php">
                        Etudiants
                    </a>
                </li>
                <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="../listeAdministrateurs.php">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeAdministrateurs.php">
                        Admins
                    </a>
                </li>
                <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center inactif_bas">
                    <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center inactif_bas_icone lien_barre_basse lien_barre_basse_admin" href="../listeGestionnaires.php">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone_admin">
                    </a>
                    <a class="d-flex justify-content-center lien_barre_basse lien_barre_basse_admin" href="../listeGestionnaires.php">
                        Gestionnaires
                    </a>
                </li>
                <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                <li class="nav-item nav-item-bas d-flex flex-column text-center actif_bas_texte actif_bas_texte_admin">
                    <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                    <a class="d-flex justify-content-center actif_bas_icone">
                        <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                        <img src="../../../ressources/icone_forum_white.svg" alt="Paramètres du forum" class="icone_admin">
                    </a>
                    Forum
                </li>
            </ul>
        </nav>
        <div class="container">
            <div class="row mx-1">
                <div class="col-1">
                    <a href="menu.php">
                        <button type="button" class="blanc mt-2"><i class="fa-solid fa-arrow-left fa-3x"></i></button>
                    </a>
                </div>
                <div class="col-11">
                    <h1 class="text-center">Génération du planning</h1>
                </div>
            </div>
            <div class="row p-2">
                <div class="col-12 mt-1 ligne">
                    <p class="text-center text-accent titreLigne ">Génération de l'emploi du temps</p>
                    <p> Etapes de la génération d'un emploi du temps : </p>
                    <ol>
                        <li class="listeInfos"> <p> Cliquer sur "généré le planning" </p> </li>
                        <li class="listeInfos"> <p> Verifier si dans la boite de dialog la génération c'est bien passé </p> </li>
                        <li class="listeInfos"> <p> Si la génération c'est bien passé, vous pouvez aller le consulter dans les autres pages </p> </li>
                        <li class="listeInfos"> <p> Si la génération ne c'est pas bien passé, vous pouvez prendre des actions adéquates dans la section inferieur de la page </p> </li>
                        <li class="listeInfos"> <p> Une fois les actions prises, vous pouvez relancer la génération </p> </li>
                        <li class="listeInfos"> <p> Une fois satisfait du planning généré cliquer sur validé, ou refuser le planning sinon </p> </li>
                    </ol>
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center">
                            <form action="generationPlanning.php" method="post">
                                <input type="hidden" name="action2" value="genererPlanning">
                                <button type="submit" class="bouton" <?php echo $isGenerated ? "disabled" : "" ?>>généré le planning</button>
                            </form>
                        </div>
                        <div class="col-12 d-flex justify-content-center">
                            <textarea name="listeEntreprise" id="listeEntreprise" class="listeConsole mb-2" rows="2" readonly><?php echo "Statut : &#13;&#10;".(isset($_SESSION['resultatGeneration']) ? $_SESSION['resultatGeneration'].($_SESSION['resultatGeneration'] === "Génération réussite !" ? " Veuillez annuler ou valider le planning générée" : "") : ($isGenerated ? "Veuillez annuler ou valider le planning générée" :"Aucune génération n'a encore été lancée !")); unset($_SESSION['resultatGeneration']) ?></textarea>
                        </div>
                        <div class="col-6 d-flex justify-content-center">
                            <form action="generationPlanning.php" method="post">
                                <input type="hidden" name="action2" value="refusePlanning">
                                <button type="submit" class="boutonNegatif" <?php echo !$isGenerated ? "disabled" : "" ?>>Refuser le planning</button>
                            </form>
                        </div>
                        <div class="col-6 d-flex justify-content-center">
                            <button type="button" class="bouton" <?php echo !$isGenerated ? "disabled" : "" ?> data-bs-toggle="modal" data-bs-target="#modal">Valider le planning</button>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 mt-3 ligneGauche">
                    <p class="text-center text-accent titreLigne"> Temps de réunion réduite</p>
                    <div class="col-12 sous-ligne">
                        <p class="text-center text-accent titreLigne"> Ajouter une entreprise à la liste</p>
                        <form action="generationPlanning.php" method="post" class="col-12">
                            <input type="hidden" name="action" value="ajouterEntrepriseReduite">
                            <div class="row">
                                <div class="col-8">
                                    <select name="entreprise" id="entreprise" class="form-control">
                                        <option value="0" selected disabled>Selectionnez une entreprise</option>
                                        <?php while ($ligne = $entreprisesPasReduites->fetch()) { ?>
                                            <option value="<?php echo $ligne['company_id'] ?>"><?php echo $ligne['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="bouton">Ajouter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 sous-ligne">
                        <p class="text-center text-accent titreLigne"> Retirer une entreprise à la liste</p>
                        <form action="generationPlanning.php" method="post" class="col-12">
                            <input type="hidden" name="action" value="retirerEntrepriseReduite">
                            <div class="row">
                                <div class="col-8">
                                    <select name="entreprise" id="entreprise" class="form-control">
                                        <option value="0" selected disabled>Selectionnez une entreprise</option>
                                        <?php foreach ($entreprisesReduites as $key => $value) { ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="bouton">Retirer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <p class="text-center text-accent titreLigne"> Liste des entreprise concernées : </p>
                    <textarea name="listeEntreprise" id="listeEntreprise" class="liste" readonly><?php foreach ($entreprisesReduites as $value) { echo $value."&#13;&#10;"; } ?></textarea>
                </div>
                <div class="col-12 col-md-6 mt-3 ligneDroite">
                    <p class="text-center text-accent titreLigne">Exclus du planning</p>
                    <div class="col-12 sous-ligne">
                        <p class="text-center text-accent titreLigne"> Ajouter une entreprise à la liste</p>
                        <form action="generationPlanning.php" method="post" class="col-12">
                            <input type="hidden" name="action" value="ajouterEntrepriseExclusion">
                            <div class="row">
                                <div class="col-8">
                                    <select name="entreprise" id="entreprise" class="form-control">
                                        <option value="0" selected disabled>Selectionnez une entreprise</option>
                                        <?php while ($ligne = $entreprisesPasExclues->fetch()) { ?>
                                            <option value="<?php echo $ligne['company_id'] ?>"><?php echo $ligne['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="bouton">Ajouter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-12 sous-ligne">
                        <p class="text-center text-accent titreLigne"> Retirer une entreprise à la liste</p>
                        <form action="generationPlanning.php" method="post" class="col-12">
                            <input type="hidden" name="action" value="retirerEntrepriseExclusion">
                            <div class="row">
                                <div class="col-8">
                                    <select name="entreprise" id="entreprise" class="form-control">
                                        <option value="0" selected disabled>Selectionnez une entreprise</option>
                                        <?php foreach ($entreprisesExclues as $key => $value) { ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="bouton">Retirer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <p class="text-center text-accent titreLigne"> Liste des entreprise concernées : </p>
                    <textarea name="listeEntreprise" id="listeEntreprise" class="liste" readonly><?php foreach ($entreprisesExclues as $value) { echo $value."&#13;&#10;"; } ?></textarea>
                </div>
        </div>
        <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Est vous sûr de vouloir valider l'emploi du temps ? <br><span class="erreur">ATTENTION : CETTE ACTION EST IRREVERSIBLE</span>
                    </div>
                    <div class="modal-footer">
                        <form action="generationPlanning.php" method="post">
                            <input type="hidden" name="action2" value="acceptPlanning"/>
                            <input type="submit" class="bouton confirmation" value="Oui"/>
                            <input type="button" class="boutonNegatif confirmation" data-bs-dismiss="modal" value="Non"/>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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