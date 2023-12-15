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
        <title>Eureka - Liste des shouaits</title>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row ligneHaut">
                <div class="col-4 align-items-center justify-content-center">
                    <Button class="btn"><img src="../../../ressources/logo.png"/><span class="h2 texte">Eureka</spawn></Button>
                </div>
            </div>
        </div>
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
                <div class="col-2 col-lg-1">
                    <img src="../../../ressources/test.png" alt="logo" class="logoEntreprise" width="75px" height="75px"/>
                </div>
                <div class="col-8 col-md-6 col-lg-8">
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
    </body>
</html>