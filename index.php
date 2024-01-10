<?php 
    session_start();
    if(isset($_SESSION['idUtilisateur']) && $_SESSION['idUtilisateur']!= null){
        header('Location: pages/connexion.php');
    } ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="./css/all.css">
        <link rel="stylesheet" href="./css/index.css">
        <title> Eureka accueil </title>
    </head>
    <body>
        <div class="container-flex">
            <div class="row" class="img-fluid" style="background-image: url('./ressources/homepage-background.png'); height: 800px; width:100%; background-size: cover;">
                <div class="col-12 d-none d-md-block eureka-pc">
                    <img src="./ressources/logo_white.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="col-12 d-md-none eureka-tel">
                    <img src="./ressources/logo_white.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="col-12"></div>
                <div class="col-12 d-none d-md-block">
                    <p class="texte-bleu-pc text-center"> L'alternance plus simple que jamais ! </p>
                    <p class="texte-blanc-pc text-center"> Etudiant ou professionnel, facilitez vos échanges grâce au forum Eureka </p>
                </div>
                <div class="col-12 d-md-none">
                    <p class="texte-bleu-tel text-center"> L'alternance plus simple que jamais ! </p>
                    <p class="texte-blanc-tel text-center"> Etudiant ou professionnel, facilitez vos échanges grâce au forum Eureka </p>
                </div>
                <div class="col-12 d-flex justify-content-center">
                    <form action="./pages/connexion.php">
                        <button type="submit" class="d-none d-sm-block bouton bouton-pc"> Se connecter </button>
                        <button type="submit" class="d-sm-none bouton bouton-tel"> Se connecter </button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-2">
                    <h2> Eureka, c'est quoi ? </h2>
                    <p> Eurêka est un salon organisé par l’IUT de Rodez afin de faciliter les échanges entre les étudiants des différentes filières de l'IUT et les entreprises. Ce salon regroupe différentes entreprises de l'Aveyron et des départements limitrophes qui sont à la recherche d'alternants. Le salon Eurêka permet aux étudiants de l'IUT en recherche d'une alternance de rencontrer et d'échanger avec des entreprises qui sont à la recherche d'alternants dans des domaines qui touchent leurs filières. </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-2">
                    <h2> Participer au salon </h2>
                    <p> Vous êtes une entreprise de l'Aveyron ou des départements limitrophes ? <br> Vous êtes à la recherche de votre futur alternant en informatique, gestion, droit, qualité, logistique, communication ou maintenance de l'industrie ? Alors le salon Eurêka est fait pour vous ! <br> Si vous souhaitez participer à ce salon, merci de bien vouloir contacter l’IUT de Rodez par téléphone (05 65 77 10 80) ou via le site de l’IUT.</p>
                </div>
            </div>
        </div>
    </body>
</html>