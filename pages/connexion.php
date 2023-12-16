<?php 
    session_start(); 
    require("../fonctions/baseDedonnees.php");
    $pdo = connecteBD();
    $tentative = false;
   

    if(isset($_POST("motDePasse")) && isset($_POST("identifiant"))){
        $_SESSION['connexion'] = verifUtilisateur($pdo, $_POST("motDePasse"), $_POST("identifiant"));
        if($_SESSION['connexion'] == false){
            $tentative = true;
        }
    }

    if($_SESSION['connexion']==true){
        infoUtilisateur($pdo, $_POST("motDePasse"), $_POST("identifiant"));
        header("../index.php");
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
                    <form action="connexion.php" method="post">
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
                                <input type="text" name="identifiant" value="" placeholder=" &#xf007 Saisir votre identifiant" class="form-control zoneText"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="motDePasse"> Votre mot de passe : </label>
                                <input type="password" name="motDePasse" value="" placeholder=" &#xf023 Saisir mot de passe" class="form-control zoneText"/>
                                <label for="motDePasse" class="w-100 d-flex justify-content-end souligner"> Mot de passe oublié ? </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-center">
                                <button> Retour </button>
                            </div>
                            <div class="col-6 text-center">
                                <button> Se connecter </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 "></div>
            </div>
        </div>
    </body>
</html>