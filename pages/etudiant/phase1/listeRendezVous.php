<?php 
    session_start();
    $user = 1;
    include("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
    if (isset($_POST["entreprise_id"])) {
        removeWishStudent($pdo, $user, $_POST["entreprise_id"]);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link  rel="stylesheet" href="../../../css/listeRendezVous.css">
        <link rel="stylesheet" href="../../../lib/fontAwesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="../../../lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <link rel="stylesheet" href="../../../css/navbars.css">
        <title>Eureka - Liste des shouaits</title>
    </head>
    <body>
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="../../../ressources/logo_black.png" alt="Logo Eureka" class="logo me-2">
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
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center lien_inactif"> Mes rendez-vous </a>
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
                                <img src="../../../ressources/icone_deconnexion.png" alt="Se déconnecter">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container">
            <div class="row mx-1">
                <div class="col-12">
                    <p><h2>Vos demandes de rendez-vous</h2></p>
                    <p>Pour le moment, au terme de la phase de prise de rendez-vous, Eureka vous proposera un planning avec les entreprises suivantes. Vous pouvez tout à fait changer d’avis !</P>
                </div>
            </div>
            <!-- Button trigger modal -->
            <?php
            $pdo = connecteBD();
            $stmt = getEntreprisesPerStudent($pdo, $user);
            $vide = true;
            while ($ligne = $stmt->fetch()) { 
            $vide = false;?>
            <div class="row entreprise align-items-center mx-1">
                <div class="col-2 col-md-1">
                    <img src="../../../ressources/<?php echo $ligne["logo_file_name"] != "" ? $ligne["logo_file_name"] : "companyDefault.png"?>" alt="logo" class="logoEntreprise" width="75px" height="75px"/>
                </div>
                <div class="col-8 col-md-6 col-lg-8 colEntreprise">
                    <span class="nomEntreprise"><?php echo $ligne["name"]?></span></br>
                    <i class="fa-solid fa-briefcase"></i>&nbsp;&nbsp;&nbsp;<?php echo $ligne["sector"]?><br/>
                    <i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $ligne["address"]?>
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
                                <input type="hidden" name="entreprise_id" value="<?php echo $ligne["company_id"]?>"/>
                                <input type="submit" class="bouton confirmation" value="Oui"/>
                                <input type="button" class="boutonNegatif confirmation" data-bs-dismiss="modal" value="Non"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php };
            if ($vide) {
                echo '<p class="rouge">Vous n\'avez pas encore demandé de rendez-vous !</p>';
            } ?>
        </div>
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas_texte">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <img src="../../../ressources/entreprise_black.png" alt="Liste des entreprises">
                        </a>
                        Entreprises
                    </li>
                    <!-- Si sur la liste des rendez-vous, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas lien_inactif">
                        <!-- Si sur la liste des rendez-vous, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="#">
                            <img src="../../../ressources/rendez-vous_white.png" alt="Mes rendez-vous">
                        </a>
                        Rendez-vous
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>