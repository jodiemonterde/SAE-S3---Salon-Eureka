<?php 
    session_start(); 
    require("../fonctions/baseDedonnees.php");
    $pdo = connecteBD();
    $tentative = false;
    $_SESSION['connexion'] = false;

    if(isset($_GET["motDePasse"]) && isset($_GET["identifiant"])){
        $_SESSION['connexion'] = verifUtilisateur($pdo, $_GET["motDePasse"], $_GET["identifiant"]);
        if($_SESSION['connexion'] == false){
            $tentative = true;
        }
    }

    if($_SESSION['connexion']==true){
        infoUtilisateur($pdo, $_GET["motDePasse"], $_GET["identifiant"]);
        if($_SESSION['typeUtilisateur'] == 'E'){
            header('Location: etudiant/phase1/listeEntreprises.php');
        }elseif($_SESSION['typeUtilisateur'] == 'G'){
            header('Location: gestionnaire/phase1/detailEntreprise.php');
        }else{
            header('Location: administrateur/entreprises/detailEntreprise.php');
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../outils/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../outils/fontawesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="../monStyleSaePhp.css">
        <title>pas dev</title>
    </head> 

    <body>
        <div class="container">
            <div class="row mx-1">
                <div class="col-md-4 "></div>
                <div class="col-12 col-md-4 centrer">
                    <form action="connexion.php" method="get">
                        <?php
                        if(isset($_GET['oublie'])){
                        ?>
                        <p>Si vous avez oublié votre mot de passe veuiller contacter un administrateur à l'aide de l'adresse mail suivante,afin qu'il vous le remplace</p>
                        <p> exemple@gmail.com </p>
                        <div class="col-6 text-center">
                            <button type="submit" formaction="connexion.php"> Retour </button>
                        </div>
                        <?php
                        } else {
                        ?>
                        <?php 
                            if($tentative){
                                echo  '<h1 class="erreur"> identifiant ou mot de passe invalide </h1>';
                            }
                        ?>

                        <div class="row">
                            <div class="col-12">
                                <h1 class="text-center"> CONNEXION </h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="identifiant"> Votre identifiant : </label>
                                <input type="text" name="identifiant" value="" placeholder="&#xf007 Saisir votre identifiant" class="form-control zoneText"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="motDePasse"> Votre mot de passe : </label>
                                <input type="password" name="motDePasse" value="" placeholder="&#xf023 Saisir mot de passe" class="form-control zoneText"/>
                                <p class="w-100 d-flex justify-content-end souligner"><a  name="oublie" href="connexion.php?oublie=true" >Mot de passe oublié ?</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-center">
                                <button type="submit" formaction="../index.php"> Retour </button>
                            </div>
                            <div class="col-6 text-center">
                                <button> Se connecter </button>
                            </div>
                        </div>
                        <?php } ?>
                    </form>
                </div>
                <div class="col-md-4 "></div>
            </div>
        </div>
    </body>
</html>