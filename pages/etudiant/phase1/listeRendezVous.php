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
        <nav class="navbar navbar-expand sticky-top border bg-white">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <img src="../../../ressources/logo_black.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="navbar-right">
                    <ul class="navbar-nav">
                        <li class="nav-item nav-link p-2 d-none d-sm-block fond_inactif_haut">
                            <!-- Si sur la liste des entreprises, mettre en jaune -->
                            <a class="lien couleur_inactif_haut" href="listeEntreprises.php"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-2 d-none d-sm-block fond_actif_haut">
                            <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                            <a class="lien couleur_actif_haut" href="#"> Mes rendez-vous </a>
                        </li>
                        <li class="nav-item dropdown p-2 d-none d-sm-block fond_inactif_haut">
                            <a class="dropdown-toggle lien couleur_inactif_haut" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item" href="../../deconnexion.php"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-sm-none">
                            <a href="../../deconnexion.php">
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
        <nav class="navbar navbar-expand fixed-bottom d-sm-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item inactif_bas">
                        <!-- Si sur la liste des entreprises, mettre l'icone blanche et le fond en jaune -->
                        <a class="d-flex justify-content-center actif_bas" href="listeEntreprises.php">
                            <img src="../../../ressources/entreprise_black.png" alt="Liste des entreprises">
                        </a>
                        <!-- Si sur la liste des entreprises, mettre en jaune -->
                        Entreprises
                    </li>
                    <li class="nav-item actif_bas_texte">
                        <!-- Si sur la liste des rendez-vous, mettre l'icone blanche et le fond en jaune -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="">
                            <img src="../../../ressources/rendez-vous_white.png" alt="Mes rendez-vous">
                        </a>
                        <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                        Rendez-vous
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>