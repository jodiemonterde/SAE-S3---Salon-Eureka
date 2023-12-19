<?php if (isset($_SESSION)) {
    header('Location: pages/connexion.php');
} ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../fontawesome-free-6.2.1-web/css/all.css">
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
                    <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero. Ut commodo tempus lacus. Nunc suscipit, neque eget faucibus luctus, eros dolor ornare urna, non rutrum purus odio quis ex. Sed tempor lectus in est scelerisque semper. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse nec convallis tellus, non varius leo. Morbi nisl justo, pharetra eget dui quis, commodo finibus nisi. Cras ullamcorper dignissim tellus sit amet dictum. Vestibulum condimentum lorem sed augue elementum pulvinar. </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 p-2">
                    <h2> Participer au salon </h2>
                    <p> Sed volutpat semper consectetur. Mauris volutpat fermentum felis, at bibendum dui molestie sit amet. Morbi ut nisl auctor, vehicula dolor at, venenatis orci. Donec lacinia, ipsum quis ultrices mattis, orci diam maximus dui, at interdum nisl nisi eget sem. Cras mollis purus sit amet dapibus molestie. Quisque rutrum, neque vel dictum lacinia, nisi ex pharetra leo, vel dapibus eros arcu ac nunc. Aliquam id consectetur ipsum. Donec mauris lorem, elementum vitae mollis nec, vehicula id sem. Praesent semper risus nec odio pharetra pellentesque. Nam a semper magna, eget scelerisque ante. </p>
                </div>
            </div>
        </div>
    </body>
</html>