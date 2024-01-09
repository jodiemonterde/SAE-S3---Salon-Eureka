<?php
    try {
        session_start(); 
        require("../fonctions/baseDeDonnees.php");
        $pdo = connecteBD();
        $tentative = false;
        $_SESSION['connexion'] = false;
        $phase = getPhase($pdo);
        if(isset($_SESSION['idUtilisateur']) && $_SESSION['idUtilisateur']!= null){
            if($_SESSION['type_utilisateur'] == 'E'){
                if ($phase == 1) {
                    header('Location: etudiant/phase1/listeEntreprises.php');
                } else {
                    header('Location: etudiant/phase2/emploiDuTemps.php');
                }
            }elseif($_SESSION['type_utilisateur'] == 'G'){
                if ($phase == 1) {
                    header('Location: gestionnaire/phase1/listeEntreprise.php');
                } else {
                    header('Location: gestionnaire/phase2/listeEntreprise.php');
                }
            }else{
                header('Location: administrateur/listeEntreprises.php');
            }
            exit();
        }

        if(isset($_POST["motDePasse"]) && isset($_POST["identifiant"])){
            $_SESSION['connexion'] = verifUtilisateur($pdo, htmlspecialchars($_POST["motDePasse"]), htmlspecialchars($_POST["identifiant"]));
            if($_SESSION['connexion'] == false){
                $tentative = true;
            }
        }

        if($_SESSION['connexion']==true){
            $info = infoUtilisateur($pdo, htmlspecialchars($_POST["motDePasse"]), htmlspecialchars($_POST["identifiant"]));
            $ligne = $info->fetch();
            $_SESSION['idUtilisateur'] = $ligne['user_id'];
            $_SESSION['type_utilisateur'] = $ligne['responsibility'];	
            $_SESSION['nom_utilisateur'] = $ligne['username'];
        }

        if($_SESSION['connexion']==true){
            if($_SESSION['type_utilisateur'] == 'E'){
                header('Location: etudiant/phase1/listeEntreprises.php');
            }elseif($_SESSION['type_utilisateur'] == 'G'){
                header('Location: gestionnaire/phase1/listeEntreprise.php');
            }else{
                header('Location: administrateur/listeEntreprises.php');
            }
        }
    } catch (Exception $e) {
        header('Location: maintenance.php');
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
        <link rel="stylesheet" href="../css/all.css">
        <link rel="stylesheet" href="../css/connexionDeconnexion.css">
        <title>Eureka - Connexion</title>
    </head>
    <body>
        <div class="container">
            <div class="row mx-1">
                <div class=" col-md-2 "></div>
                <div class="col-12 col-md-8 centrer">
                    <form action="connexion.php" method="post">
                        <?php
                        if(isset($_GET['oublie'])){
                        ?>
                        <h2 class="text-center"> Mot de passe oublié </h2>
                        <p>Si vous avez oublié votre mot de passe veuiller contacter un administrateur à l'aide de l'adresse mail suivante, afin qu'il vous le remplace</p>
                        <p> exemple@gmail.com </p>
                        <div class="text-center d-flex justify-content-end">
                            <button type="submit" formaction="connexion.php" class="bouton"> Retour </button>
                        </div>
                        <?php
                        } else {
                        ?>
                        <?php 
                            if($tentative){
                                echo  '<p class="erreur text-center"> identifiant ou mot de passe invalide </p>';
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
                                <input type="text" name="identifiant" value="<?php echo isset($_POST["identifiant"]) ? $_POST["identifiant"] : ""?>" placeholder="&#xf007 Saisir votre identifiant" class="form-control zoneText"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="motDePasse"> Votre mot de passe : </label>
                                <input type="password" name="motDePasse" value="" placeholder="&#xf023 Saisir mot de passe" class="form-control zoneText"/>
                                <p class="w-100 d-flex justify-content-end"><a  name="oublie" href="connexion.php?oublie=true" >Mot de passe oublié ?</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-center">
                                <a href="../index.php"><button type="button" class="bouton"> Retour </button></a>
                            </div>
                            <div class="col-6 text-center">
                                <button type="submit" class="bouton"> Se connecter </button>
                            </div>
                        </div>
                        <?php } ?>
                    </form>
                </div>
                <div class="col-md-2"></div>
            </div>
        </div>
    </body>
</html>