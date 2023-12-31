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

    if (isset($_POST["modifyCompany"])) {
        modifyCompany($pdo, $_POST["companyID"], $_POST["nomEntreprise"], $_POST["descriptionEntreprise"], $_POST["secteurEntreprise"],$_POST["adresseEntreprise"], $_POST["codePostalEntreprise"], $_POST["villeEntreprise"], $_FILES["logoEntreprise"]);
        header("Location: detailEntreprise.php");
        exit();
    }

    if (isset($_POST["deleteCompany"])) {
        deleteCompany($pdo, $_POST["companyID"]);
        header("Location: detailEntreprise.php");
        exit();
    }

    if (isset($_POST["addCompany"])) {
        $intervenants_array = array();
    
        // Le premier intervenant est toujours présent
        $intervenant_1 = array(
            'nom' => $_POST['nomIntervenant'],
            'filieres' => $_POST['filieresIntervenant']
        );
        
        $intervenants_array[] = $intervenant_1;

        if (isset($_POST['nomIntervenant_2'])) {
            // Commencer à partir de l'indice 2
            $cpt = 2;
            
            while (isset($_POST['nomIntervenant_' . $cpt])) {
                $intervenant = array(
                    'nom' => $_POST['nomIntervenant_' . $cpt],
                    'filieres' => $_POST['filieresIntervenant_' . $cpt]
                );
            
                $intervenants_array[] = $intervenant;
                $cpt++;
            }
    
        }
        addCompany($pdo, $_POST["nomEntreprise"], $_POST["descriptionEntreprise"], $_POST["adresseEntreprise"], $_POST["codePostalEntreprise"], $_POST["villeEntreprise"], $_POST["secteurEntreprise"], $_FILES["logoEntreprise"], $intervenants_array);
        header("Location: detailEntreprise.php");
        exit();
    } 

    if (isset($_POST['modifySpeaker'])) {
        modifySpeaker($pdo, $_POST['nomIntervenantEdit'], $_POST['roleIntervenantEdit'], $_POST['filieresIntervenant'], $_POST['intervenantID']);
        header("Location: detailEntreprise.php");
        exit();
    }

    if (isset($_POST['deleteSpeaker'])) {
        deleteSpeaker($pdo, $_POST['intervenantID']);
        header("Location: detailEntreprise.php");
        exit();
    }

    if (isset($_POST['addSpeaker'])) {
        addSpeaker($pdo, $_POST['companyID'], $_POST['nomIntervenantAdd'], $_POST['roleIntervenantAdd'], $_POST['filieresIntervenant']);
        header("Location: detailEntreprise.php");
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
    <script src="../../../lib/bootstrap-5.3.2-dist/js/bootstrap.js"></script>
    <script src="../../../lib/jquery/jquery-3.3.1.js"></script>
    <script src="js.js"></script>
    <link rel="stylesheet" href="./listeEntreprise.css">
    <link rel="stylesheet" href="../../../css/navbars.css">
    <link rel="stylesheet" href="./filtre.css">
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
            <hr class="mb-4">
            <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddCompany">
                <i class="fa-solid fa-plus text-left justify-content-center"></i>
                <h2 class="text-center m-2">Ajouter une entreprise</h2>
            </button>
            
            <div class="modal fade" id="modalAddCompany" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                    <div class="modal-content  px-4 pb-4">
                        <div class="modal-header deco justify-content-start px-0">
                            <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                            <h2 class="modal-title" id="addCompanyModalLabel">Nouvelle entreprise</h2>
                        </div>
                        <form action="detailEntreprise.php" method="post" enctype="multipart/form-data">
                            <label for="nomEntreprise" class="modalLabel mb-0 mt-2">Nom</label>
                            <input class="zoneText" type="text" name="nomEntreprise" id="nomEntreprise" placeholder="Saisir le nom de l’entreprise" required/>
                            <label for="descriptionEntreprise" class="modalLabel mb-0 mt-2">Description</label>
                            <input class="zoneText" type="text" name="descriptionEntreprise" id="descriptionEntreprise" placeholder="Saisir un texte permettant de décrire en détail l'entreprise"/>
                            <label for="adresseEntreprise" class="modalLabel mb-0 mt-2">Adresse</label>
                            <input class="zoneText" type="text" name="adresseEntreprise" id="adresseEntreprise" placeholder="Saisir l'adresse complète de l'entreprise" required/>
                            <label for="codePostalEntreprise" class="modalLabel mb-0 mt-2">Code Postal</label>
                            <input class="zoneText" type="text" name="codePostalEntreprise" id="codePostalEntreprise" placeholder="Saisir le code postal de l'entreprise" required/>
                            <label for="villeEntreprise" class="modalLabel mb-0 mt-2">Ville</label>
                            <input class="zoneText" type="text" name="villeEntreprise" id="villeEntreprise" placeholder="Saisir la ville où se situe l'entreprise" required/>
                            <label for="secteurEntreprise" class="modalLabel mb-0 mt-2">Secteur d'activité</label>
                            <input class="zoneText" type="text" name="secteurEntreprise" id="secteurEntreprise" placeholder="Saisir le secteur d'activité" required/>
                            <label for="logo" class="modalLabel mb-0 mt-2 w-100">Logo de l'entreprise</label>
                            <input type="file" name="logoEntreprise" id="logo" accept="image/*">
                            <hr>

                            <div id="intervenantsContainer">
                                <div id="intervenantTemplate" style="display: block;">
                                    <div class="intervenantContainer">
                                       <div class="d-flex flex-wrap">
                                            <h2>Intervenant &nbsp;<h2 id="numeroIntervenant">1</h2></h2>
                                            <button type ="button" class="border-0 icon-title"><i class="fa-solid fa-trash icon"></i></button>
                                        </div>
                                        <label for="nomIntervenant" class="modalLabel mb-0 mt-2">Nom</label>
                                        <input class="zoneText" type="text" name="nomIntervenant" id="nomIntervenant" placeholder="Saisir le nom de l’intervenant" required/>
                                        <div class="rowForChecks d-flex flex-wrap">
                                            <?php
                                                $fields = getFields($pdo); 
                                                while ($ligne = $fields->fetch()) { ?>
                                                    <label class="buttonToCheck me-2">
                                                        <input type="checkbox" name="filieresIntervenant[]" value="<?php echo $ligne['field_id'];?>" />
                                                        <div class="icon-box">
                                                            <span><?php echo $ligne['name'];?></span>
                                                        </div>
                                                    </label>
                                            <?php } ?>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                                
                            <button type="button" class="addStudent d-flex w-100 text-center align-items-center justify-content-center" onclick="ajouterIntervenant(event)">
                                <i class="fa-solid fa-plus text-left justify-content-center"></i>
                                <h2 class="text-center m-2">Ajouter un intervenant</h2>
                            </button>
                            


                            <div class="row mt-3">
                                <div class="col-6">
                                    <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
                                </div>
                                <div class="col-6">
                                    <button type="submit" name="addCompany" class="bouton confirmation col-6" value="Valider">Valider</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Accordéon Bootstrap -->
            <div class="accordion" id="listeEntreprise">
            <?php
                $stmt = getEntreprisesAdministrateur($pdo, $_SESSION['filtre'], $_SESSION['recherche']);

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
                            <div class="dp"><img src="../../../ressources/logosentreprises/<?php echo $ligne['logo'] ?? 'no-photo.png'; ?>" alt=""></div>
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
                            $intervenants = getSpeakersPerCompanyAdministrateur($ligne["intervenants_roles"]);
                            ?>
                            <hr>
                            <?php foreach ($intervenants as $intervenant) { ?>
                                <div class="my-1">
                                    
                            <span class="speakerName m-0"><?php echo $intervenant["nom"];?></span>
                            <?php if ($intervenant["fonction"] != null) { ?>
                                <span class="speakerRole m-0"> <?php echo '- '.$intervenant["fonction"];?> </span>
                            <?php } ?>
                            <div class="d-flex">
                            <div class="d-flex flex-wrap w-100">
                                <?php foreach ($intervenant["fields"] as $field) {
                                    echo '<div class="tag text-center mb-2">'.$field.'</div>';
                                } ?>
                                </div>
                                <div class="d-flex">
                                    <button class="border-0 icon-title" data-bs-toggle="modal" data-bs-target="#edit<?php echo $intervenant['id']; ?>"><i class="fa-solid fa-pen icon m-0"></i></button>
                                    <button class="border-0 icon-title" data-bs-toggle="modal" data-bs-target="#delete<?php echo $intervenant['id']; ?>"><i class="fa-solid fa-trash icon m-0"></i></button>
                                </div>
                                </div>
                                <div class="modal fade" id="edit<?php echo $intervenant['id'];?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content  px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="editModalLabel"><?php echo $intervenant["nom"]; ?></h2>
                                            </div>
                                            <form action="detailEntreprise.php" method="post" enctype="multipart/form-data">
                                                <label for="nomIntervenantEdit" class="modalLabel mb-0 mt-2">Nom</label>
                                                <input class="zoneText" type="text" name="nomIntervenantEdit" id="nomIntervenantEdit" value="<?php echo $intervenant["nom"]; ?>" maxlength="100"/>
                                                <label for="roleIntervenantEdit" class="modalLabel mb-0 mt-2">Role</label>
                                                <input class="zoneText" type="textarea" name="roleIntervenantEdit" id="roleIntervenantEdit" value="<?php if (empty($intervenant["fonction"])) { echo '" placeholder="Saisir un rôle"'; } else { echo $intervenant["fonction"];} ?>" maxlength="100" />
                                                <div class="rowForChecks d-flex flex-wrap">
                                                    <?php
                                                        $fields = getFields($pdo); 
                                                        while ($ligne2 = $fields->fetch()) { ?>
                                                            <label class="buttonToCheck me-2">
                                                                <input type="checkbox" name="filieresIntervenant[]" value="<?php echo $ligne2['field_id'].'"';
                                                                foreach ($intervenant["fields"] as $field) {
                                                                    if ($field == $ligne2['name']) {
                                                                        echo 'checked';
                                                                    }
                                                                }
                                                                
                                                                ?> />
                                                                <div class="icon-box">
                                                                    <span><?php echo $ligne2['name'];?></span>
                                                                </div>
                                                            </label>
                                                    <?php } ?>
                                                </div>
                                                <input type="hidden" name="intervenantID" value="<?php echo $intervenant['id'];?>">

                                                <hr>                         

                                                <div class="row mt-3">
                                                    <div class="col-6">
                                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
                                                    </div>
                                                    <div class="col-6">
                                                        <button type="submit" name="modifySpeaker" class="bouton confirmation col-6" value="Valider">Valider</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="delete<?php echo $intervenant['id'];?>" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content  px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="editModalLabel"><?php echo $intervenant["nom"]; ?></h2>
                                            </div>
                                            <p>Êtes-vous sûr(e) de vouloir supprimer cet intervenant ?</p>
                                            <form action="detailEntreprise.php" method="post" enctype="multipart/form-data"> 
                                                <input type="hidden" name="intervenantID" value="<?php echo $intervenant['id'];?>">         
                                                <div class="row mt-3">
                                                    <div class="col-6">
                                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
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
                            
                            <button class="addStudent d-flex w-100 text-center align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#modalAddSpeaker<?php echo $ligne['company_id']?>">
                                <i class="fa-solid fa-plus text-left justify-content-center"></i>
                                <h2 class="text-center m-2">Ajouter un(e) intervenant(e)</h2>
                            </button>
                            <div class="modal fade" id="modalAddSpeaker<?php echo $ligne['company_id'];?>" tabindex="-1" aria-labelledby="addSpeakerModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                        <div class="modal-content  px-4 pb-4">
                                            <div class="modal-header deco justify-content-start px-0">
                                                <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                                <h2 class="modal-title" id="addSpeakerModalLabel">Nouvel intervenant</h2>
                                            </div>
                                            <form action="detailEntreprise.php" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="companyID" value="<?php echo $ligne['company_id']?>">
                                                <label for="nomIntervenantAdd" class="modalLabel mb-0 mt-2">Nom</label>
                                                <input class="zoneText" type="text" name="nomIntervenantAdd" id="nomIntervenantAdd" placeholder="Saisissez le nom de l'intervenant" maxlength="50" required/>
                                                <label for="roleIntervenantAdd" class="modalLabel mb-0 mt-2">Role</label>
                                                <input class="zoneText" type="textarea" name="roleIntervenantAdd" id="roleIntervenantAdd" placeholder="Saisir un rôle pour cet intervenant (facultatif)"  maxlength="80" />
                                                <div class="rowForChecks d-flex flex-wrap">
                                                    <?php
                                                        $fields = getFields($pdo); 
                                                        while ($ligne2 = $fields->fetch()) { ?>
                                                            <label class="buttonToCheck me-2">
                                                                <input type="checkbox" name="filieresIntervenant[]" value="<?php echo $ligne2['field_id'];?>" />
                                                                <div class="icon-box">
                                                                    <span><?php echo $ligne2['name'];?></span>
                                                                </div>
                                                            </label>
                                                    <?php } ?>
                                                </div>

                                                <hr>                         

                                                <div class="row mt-3">
                                                    <div class="col-6">
                                                        <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
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
                        <hr>
                        <div class="row d-flex justify-content-evenly">
                            <div class="col-4">
                                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#modification<?php echo $ligne['company_id'];?>"> Modifier </button>
                            </div>
                            <div class="col-4">
                                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#suppression<?php echo $ligne['company_id'];?>"> Supprimer </button>
                            </div>
                        </div>
                       

                        <div class="modal fade" id="modification<?php echo $ligne['company_id'];?>" tabindex="-1" aria-labelledby="modificationModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                <div class="modal-content  px-4 pb-4">
                                     <div class="modal-header deco justify-content-start px-0">
                                        <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                        <h2 class="modal-title" id="modificationModalLabel"><?php echo $ligne['name']; ?></h2>
                                    </div>
                                    <form action="detailEntreprise.php" method="post" enctype="multipart/form-data">
                                        <label for="nomEntreprise" class="modalLabel mb-0 mt-2">Nom</label>
                                        <input class="zoneText" type="text" name="nomEntreprise" id="nomEntreprise" placeholder="<?php echo $ligne["name"]; ?>"/>
                                        <label for="descriptionEntreprise" class="modalLabel mb-0 mt-2">Description</label>
                                        <input class="zoneText" type="textarea" name="descriptionEntreprise" id="descriptionEntreprise" placeholder="<?php if (empty($ligne["description"])) { echo 'Saisir une description'; } else { echo $ligne["description"];} ?>"/>
                                        <label for="adresseEntreprise" class="modalLabel mb-0 mt-2">Adresse</label>
                                        <input class="zoneText" type="text" name="adresseEntreprise" id="adresseEntreprise" placeholder="<?php echo $ligne["address"]; ?>"/>
                                        <label for="codePostalEntreprise" class="modalLabel mb-0 mt-2">Code Postal</label>
                                        <input class="zoneText" type="text" name="codePostalEntreprise" id="codePostalEntreprise" placeholder="<?php echo $ligne["address"]; ?>"/>
                                        <label for="villeEntreprise" class="modalLabel mb-0 mt-2">Ville</label>
                                        <input class="zoneText" type="text" name="villeEntreprise" id="villeEntreprise" placeholder="<?php echo $ligne["address"]; ?>"/>
                                        <label for="secteurEntreprise" class="modalLabel mb-0 mt-2">Secteur d'activité</label>
                                        <input class="zoneText" type="text" name="secteurEntreprise" id="secteurEntreprise" placeholder="<?php echo $ligne["sector"]; ?>"/>
                                        <label for="logo" class="modalLabel mb-0 mt-2 w-100">Logo de l'entreprise</label>
                                        <input type="file" name="logoEntreprise" id="logo" accept="image/*">
                                        <hr>                         
                                        
                                        <input type="hidden" name="companyID" value="<?php echo $ligne['company_id'];?>">
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
                                            </div>
                                            <div class="col-6">
                                                <button type="submit" name="modifyCompany" class="bouton confirmation col-6" value="Valider">Valider</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>


                        <div class="modal fade" id="suppression<?php echo $ligne['company_id'];?>" tabindex="-1" aria-labelledby="suppressionModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                                <div class="modal-content  px-4 pb-4">
                                     <div class="modal-header deco justify-content-start px-0">
                                        <button type="button" class="blanc border-0" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-arrow-left fa-2x"></i></button>
                                        <h2 class="modal-title" id="suppressionModalLabel"><?php echo $ligne['name']; ?></h2>
                                    </div>
                                    <p>Êtes-vous sûr(e) de vouloir supprimer cette entreprise ?</p>
                                    <form action="detailEntreprise.php" method="post" enctype="multipart/form-data">                  
                                        <input type="hidden" name="companyID" value="<?php echo $ligne['company_id'];?>">
                                        <div class="row mt-3">
                                            <div class="col-6">
                                                <input type="button" class="boutonNegatif confirmation col-6" data-bs-dismiss="modal" value="Annuler"/>
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



        
        
        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte lien_inactif">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
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
                    <!-- Si sur la liste des gestionnaires, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des gestionnaires, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur la liste des gestionnaires, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_gestionnaire_black.svg" alt="Liste des gestionnaires" class="icone">
                        </a>
                        Gestionnaires
                    </li>
                    <!-- Si sur les paramètres du forum, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur les paramètres du forum, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur les paramètres du forum, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="../../../ressources/icone_forum_black.svg" alt="Paramètres du forum" class="icone">
                        </a>
                        Forum
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>
