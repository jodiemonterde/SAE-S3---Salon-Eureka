<?php 
    // Démarrage d'une session
    session_start();

    // Redirection vers la page de connexion si l'utilisateur est déjà connecté
    if(isset($_SESSION['idUtilisateur']) && $_SESSION['idUtilisateur'] != null){
        header('Location: pages/connexion.php');
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Métadonnées et liens vers les feuilles de style -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./css/all.css">
        <link rel="stylesheet" href="./css/index.css">

        <title> Eureka accueil </title>
    </head>
    <body>
        <!-- Conteneur principal -->
        <div class="container-flex">
            <!-- Entête de la page d'accueil avec une image en fond -->
            <div class="row" class="img-fluid" style="background-image: url('./ressources/homepage-background.png'); height: 800px; width:100%; background-size: cover;">
                <!-- Logo et nom du site en haut à droite -->
                <div class="col-12 eureka-entete">
                    <img src="./ressources/logo_white.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>

                <div class="col-12">
                    <p class="texte-bleu text-center"> L'alternance plus simple que jamais ! </p>
                    <p class="texte-blanc text-center"> Etudiant ou professionnel, facilitez vos échanges grâce au forum Eureka </p>
                </div>

                <!-- Formulaire de connexion au site (bouton de redirection vers la page de connexion) -->
                <div class="col-12 d-flex justify-content-center">
                    <form action="./pages/connexion.php">
                        <button type="submit" class="bouton bouton-connexion"> Se connecter </button>
                    </form>
                </div>
            </div>

            <!-- Explication du forum sous forme de sous-parties -->
            <div class="row">
                <!-- Explication générale -->
                <div class="row px-2 py-4">
                    <div class="col-md-6 d-flex justify-content-center flex-column"> 
                        <h4 class="m-0">Le forum</h4>
                        <h2 class="m-0">EUREKA, C'EST QUOI ?</h2> 
                        <hr class="my-2">
                        <span class="subtitle">Venez découvrir un lieu d'échange entre professionnel et étudiant</span>
                    </div>
                    <p class="col-md-6 d-flex align-items-center paragraphe my-3"> 
                        Vous êtes une entreprise de l'Aveyron ou des départements limitrophes ? 
                        Vous êtes à la recherche de votre futur alternant en informatique, gestion, droit, qualité, logistique, 
                        communication ou maintenance de l'industrie ? Alors le salon Eurêka est fait pour vous ! 
                        Il s'agit d'un salon organisé par l’IUT de Rodez afin de faciliter les échanges entre les étudiants des différentes 
                        filières de l'IUT et les entreprises. Ce salon regroupe différentes entreprises de l'Aveyron et des départements 
                        limitrophes qui sont à la recherche d'alternants. Le salon Eurêka permet aux étudiants de l'IUT en recherche 
                        d'une alternance de rencontrer et d'échanger avec des entreprises qui sont à la recherche d'alternants dans des 
                        domaines qui touchent leurs filières.
                    </p>    
                </div>
            </div>

            <!-- Explication de l'utilité -->
            <div class="row pt-4 background">
                <div class="col-12 p-2">
                    <div class="text-center">
                        <h4 class="m-0">en quoi</h4>
                        <h2 class="m-0 text-white">eureka peut vous servir ?</h2>
                        <div class="d-flex justify-content-center"><hr class="my-2 text-center"></div>
                        
                    </div>
                    <div class="row pt-4 text-white">
                        <div class="col-md-4 text-center px-3">
                            <p class="m-0 fw-bolder paragraphe">RESEAUTAGE</p>
                            <p class="paragraphe">Que vous soyez une entreprise ou un étudiant, étendre son réseau est primordial. Rencontrer des professionnels ou des futurs professionnels et faire une bonne impression aura toujours un intérêt.</p>
                        </div>
                        <div class="col-md-4 text-center px-3">
                            <p class="m-0 fw-bolder paragraphe">TROUVER UNE ALTERNANCE/STAGE</p>
                            <p class="paragraphe">En tant qu'étudiant, il peut être difficile de trouver un stage ou une alternance. Simplifiez-vous la vie et rencontrer des entreprises dont vous êtes sûrs d'avoir l'attention !</p>
                        </div>
                        <div class="col-md-4 text-center px-3">
                            <p class="m-0 fw-bolder paragraphe">TROUVER UN ALTERNANT/STAGIAIRE</p>
                            <p class="paragraphe">Rencontrer des étudiants en vue d'un partenariat peut être chronophage. Grâce à ce forum, vous aller non seulement gagner du temps, mais également rencontrer plus d'étudiants : choisissez parmi les plus convainquant !</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Explication de comment participer -->
            <div class="row">
                <div class="col-12 p-2">
                    <div class="text-center">
                        <h4 class="m-0">Comment</h4>
                        <h2 class="m-0"> Participer</h2>
                        <div class="d-flex justify-content-center"><hr class="my-2 text-center"></div>
                        
                    </div>
                    <div class="row pt-4">
                        <div class="col-md-4 text-center px-3">
                            <img src="ressources/icone_pin_position.svg" alt="icone de position" class="presentation-icone">
                            <p class="m-0 fw-bolder paragraphe">En se rendant sur place</p>
                            <p class="paragraphe">L'IUT de Rodez se situe au 50 Av. de Bordeaux à Rodez. Rendez-vous sur place pour demander des renseignements !</p>
                        </div>
                        <div class="col-md-4 text-center px-3">
                            <img src="ressources/icone_phone.svg" alt="icone de téléphone" class="presentation-icone">
                            <p class="m-0 fw-bolder paragraphe">Par téléphone</p>
                            <p class="paragraphe">Vous pouvez contacter l'IUT de Rodez par téléphone au <a href="tel:+33565771080">05 65 77 10 80</a> du lundi au vendredi de 8h à 18h.</p>
                        </div>
                        <div class="col-md-4 text-center px-3">
                            <img src="ressources/icone_link.svg" alt="icone de lien hypertexte" class="presentation-icone">
                            <p class="m-0 fw-bolder paragraphe">Via le site de l'IUT</p>
                            <p class="paragraphe">Le <a href="https://www.iut-rodez.fr/fr/contact">site de l'IUT</a> dispose d'une rubrique 'nous contacter', via laquelle vous pouvez soumettre votre participation au forum.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>