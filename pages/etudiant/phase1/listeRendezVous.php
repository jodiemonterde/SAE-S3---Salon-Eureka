<?php
    try {
        session_start();
        require('../../../fonctions/baseDeDonnees.php');
        $pdo = connecteBD();
        if(!isset($_SESSION['idUtilisateur']) || getPhase($pdo) != 1 || $_SESSION['type_utilisateur'] != 'E'){
            header('Location: ../../connexion.php');
            exit();
        }
        if (isset($_POST["entreprise_id"])) {
            removeWishStudent($pdo, $_SESSION['idUtilisateur'], $_POST["entreprise_id"]);
        }
        $stmt = getEntreprisesPerStudent($pdo, $_SESSION['idUtilisateur']);
    }catch (Exception $e) {
        header('Location: ../../maintenance.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="../../../js/downloadPage.js"></script>
        <link rel="stylesheet" href="../../../css/navbars.css">
        <link rel="stylesheet" href="../../../css/all.css">
        <link  rel="stylesheet" href="../../../css/listeRendezVous.css">
        <title>Eureka - Liste des shouaits</title>
    </head>
    <body>
        <!-- barre de nvaigation du haut -->
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
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="listeEntreprises.php"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif et lien_inactif -->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center"> Mes rendez-vous </a>
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
        <!-- Contenu principal -->
        <div class="container">
            <div class="row mx-1">
                <div class="col-12">
                    <p><h2>Vos demandes de rendez-vous</h2></p>
                    <p>Pour le moment, au terme de la phase de prise de rendez-vous, Eureka vous proposera un planning avec les entreprises suivantes. Vous pouvez tout à fait changer d’avis !</P>
                </div>
            </div>
            <?php
            $vide = true;
            while ($ligne = $stmt->fetch()) { 
            $vide = false;?>
            <div class="row entreprise align-items-center ">
                <div class="col-2 col-md-1">
                    <img src="../../../ressources/<?php echo htmlspecialchars($ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "no-photo.png")?>" alt="logo" class="logoEntreprise" width="75px" height="75px"/>
                </div>
                <div class="col-8 col-md-6 col-lg-8 colEntreprise">
                    <span class="text-jaune"><?php echo htmlspecialchars($ligne["name"])?></span></br>
                    <i class="fa-solid fa-briefcase"></i>&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($ligne["sector"])?><br/>
                    <i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($ligne["address"])?>
                </div>
                <div class="col-2 d-none d-md-block">
                    <input type="button" class="bouton" value="supprimer l'entreprise" data-bs-toggle="modal" data-bs-target="#modal"/>
                </div>
                <div class="col-1 d-block d-md-none">
                    <input type="button" class="boutonSupprimerMd"data-bs-toggle="modal" data-bs-target="#modal"/>
                </div>
                <div class="modal fade" id="modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Est vous sûr de vouloir supprimer cette entreprise de votre liste de souhaits ?
                            </div>
                            <div class="modal-footer">
                                <form action="listeRendezVous.php" method="post">
                                    <input type="hidden" name="entreprise_id" value="<?php echo htmlspecialchars($ligne["company_id"])?>"/>
                                    <input type="submit" class="bouton confirmation" value="Oui"/>
                                    <input type="button" class="boutonNegatif confirmation" data-bs-dismiss="modal" value="Non"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php };
            if ($vide) { ?>
                <div class="row">
                    <div class="col-12">
                        <p class="erreur">Vous n'avez pas encore demandé de rendez-vous !</p>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- Barre de navigation du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item d-flex flex-column text-center inactif_bas_texte">
                        <a class="d-flex justify-content-center" href="listeEntreprises.php">
                            <img src="../../../ressources/icone_entreprise_black.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        <a class="d-flex justify-content-center lien_barre_basse" href="listeEntreprises.php">
                            Entreprises
                        </a>
                    </li>
                    <li class="nav-item d-flex flex-column text-center actif_bas lien_inactif">
                        <a class="d-flex justify-content-center actif_bas_icone">
                            <img src="../../../ressources/icone_rdv_white.svg" alt="Mes rendez-vous" class="icone">
                        </a>
                        Rendez-vous
                    </li>
                </ul>
            </div>
        </nav>
        <!-- Modal pour la deconnexion-->
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