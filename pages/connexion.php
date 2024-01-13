<!-- Page de connexion à laquelle on accède depuis la page d'accueil du site. Permet de s'identifier. -->
<?php
    try {
        session_start(); 

        /* 
         * Fichier indispensable au bon fonctionnement du site, contenant toutes les fonctions utilisés notamment pour se
         * connecter à la base de donnée et interagir avec celle-ci.
         */
        require("../fonctions/baseDeDonnees.php");
        $pdo = connecteBD(); // accès à la Base de données
        $tentative = false; // boolean à false par défaut qui empêche l'accès aux autres pages du site tant qu'il n'est pas passé à true
        $_SESSION['connexion'] = false;

        /* 
         * Permet d'obtenir la phase dans laquelle se trouve actuellement le site : 
         *     - phase 1 s'il est possible de prendre des rendez-vous
         *     - phase 1.5 si un administrateur est en train d'essayer de générer un planning
         *     - phase 2 s'il est possible de consulter les plannings
          */
        $phase = getPhase($pdo);
        
        /* Redirige l'utilisateur selon la phase et le type d'utilisateur (étudiant, gestionnaire, administrateur) */
        if ((isset($_SESSION['idUtilisateur']) && $_SESSION['idUtilisateur'] != null) || $_SESSION['connexion'] == true) {
            if ($_SESSION['type_utilisateur'] == 'E'){ // L'utilisateur est un étudiant
                if ($phase == 1) {
                    header('Location: etudiant/phase1/listeEntreprises.php');
                } else if ($phase == 1.5) {
                    header('Location: etudiant/phase1/listeRendezVous.php');
                } else {
                    header('Location: etudiant/phase2/emploiDuTemps.php');
                }
            } elseif ($_SESSION['type_utilisateur'] == 'G') { // L'utilisateur est un gestionnaire
                if ($phase == 1) {
                    header('Location: gestionnaire/phase1/listeEntreprise.php');
                } else {
                    header('Location: gestionnaire/phase2/listeEntreprise.php');
                }
            } else { // L'utilisateur est un administrateur
                header('Location: administrateur/listeEntreprises.php');
            }
            exit();
        }

        // L'utilisateur a soumis le formulaire de connexion
        if (isset($_POST["motDePasse"]) && isset($_POST["identifiant"])) {
            // Vérification de si l'utilisateur existe et de si son mot de passe est correct
            $_SESSION['connexion'] = verifUtilisateur($pdo, htmlspecialchars($_POST["motDePasse"]),
                                                            htmlspecialchars($_POST["identifiant"]));
            if ($_SESSION['connexion'] == false) {
                $tentative = true; // Permettra d'affiche un message expliquant que la connexion a échoué.
            }
        }

        /* 
         * L'utilisateur a entré un mot de passe et un identifiant qui correspondent à un utilisateur : 
         * on stocke donc ces informations dans des variables de sessions
         */
        if($_SESSION['connexion']==true){
            $info = infoUtilisateur($pdo, htmlspecialchars($_POST["motDePasse"]),
                                          htmlspecialchars($_POST["identifiant"]));
            $ligne = $info->fetch();
            $_SESSION['idUtilisateur'] = $ligne['user_id'];
            $_SESSION['type_utilisateur'] = $ligne['responsibility'];
            $_SESSION['prenom_utilisateur'] = $ligne['firstname'];	
            $_SESSION['nom_utilisateur'] = $ligne['lastname'];
        }

    } catch (Exception $e) {
        header('Location: maintenance.php'); // En cas d'erreur, redirige vers la page de site en maintenance
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Métadonnées et liens vers les feuilles de style -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/all.css">
        <link rel="stylesheet" href="../css/connexionDeconnexion.css">
        
        <title>Eurêka - Connexion</title>
    </head>
    <body>
        <div class="container">
            <div class="row mx-1">
                <div class=" col-md-2 "></div>
                <div class="col-12 col-md-8 centrer">
                    <!-- Formulaire de connexion au site -->
                    <form action="connexion.php" method="post">
                        <!-- Affichage du contenu de 'mot de passe oublié' uniquement si la variable oubli 
                             est set, du formulaire de connexion sinon -->
                        <?php
                        if (isset($_GET['oubli'])) {
                        ?>
                        <h2 class="text-center"> Mot de passe oublié </h2>
                        <p>Votre mot de passe se compose d'à minima 8 caractères, dont un symbole et un chiffre. Si vous l'avez oublié, veuillez contacter un le service informatique de l'IUT, afin qu'il ne remplace celui-ci.</p>
                        <div class="text-center d-flex justify-content-end">
                            <button type="submit" formaction="connexion.php" class="bouton"> Retour </button>
                        </div>
                        <?php
                        } else {
                        ?>
                        <!-- Si une tentative a déjà été effectué, affiche un message d'erreur -->
                        <?php 
                            if ($tentative) {
                                echo  '<p class="erreur text-center"> Identifiant ou mot de passe invalide </p>';
                            }
                        ?>

                        <div class="row">
                            <div class="col-12">
                                <h1 class="text-center"> CONNEXION </h1>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="identifiant"> Votre identifiant (e-mail): </label>
                                <input type="text" name="identifiant" value="<?php echo isset($_POST["identifiant"]) ? $_POST["identifiant"] : ""?>" placeholder="&#xf007 Saisir votre identifiant" class="form-control zoneText"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label for="motDePasse"> Votre mot de passe (8 caractères dont un symbole et un chiffre): </label>
                                <input type="password" name="motDePasse" value="" placeholder="&#xf023 Saisir mot de passe" class="form-control zoneText"/>
                                <p class="w-100 d-flex justify-content-end"><a  name="oubli" href="connexion.php?oubli=true" >Mot de passe oublié ?</a></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 text-center">
                                <!-- Redirection vers la page d'accueil -->
                                <a href="../index.php"><button type="button" class="bouton"> Retour </button></a>
                            </div>
                            <div class="col-6 text-center">
                                <!-- Tentative de connexion, formulaire soumi -->
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