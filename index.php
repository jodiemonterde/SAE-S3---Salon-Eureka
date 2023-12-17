<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../fontawesome-free-6.2.1-web/css/all.css">
        <title>Eureka accueil</title>
    </head>
    <body>
        <?php 
            
            // // Récupérer le nom de la page
            // $currentPage = basename(__FILE__);


            // // Afficher le nom de la page
            // echo "<h1>Page: $currentPage</h1>";

            // echo "<p>Cette page n'est pas développer.</p>";


            // // Afficher les variables de session
            // echo "<h2>Variables de session :</h2>";
            // var_dump($_SESSION);

            // // Afficher les variables POST
            // echo "<h2>Variables POST :</h2>";
            // var_dump($_POST);

            // // Afficher les variables GET
            // echo "<h2>Variables GET :</h2>";
            // var_dump($_GET);
        ?> 
        <div class="container-flex">
            <div class="row" class="bg-image img-fluid" style="background-image: url('./ressources/homepage-background.png'); height: 800px; width:100%; background-size: cover;">
                <div class="col-12 header">
                    <img src="./ressources/logo.png" alt="logo du site Eureka"><span class="text-white">Eureka</span>
                </div>
                <div class="col-10"></div>
                <div class="col-12">
                    <div class="col-12 text-primary text-center">L'alternance plus simple que jamais !</div>
                    <div class="col-12 text-white text-center">Etudiant ou professionnel, facilitez vos échanges grâce au forum Eureka</div>
                </div>
                <div class="col-12 text-center"> <a href="pages/connexion.php"><button>Se connecter</button></div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h2 >Eureka, c'est quoi ?</h2>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero. Ut commodo tempus lacus. Nunc suscipit, neque eget faucibus luctus, eros dolor ornare urna, non rutrum purus odio quis ex. Sed tempor lectus in est scelerisque semper. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse nec convallis tellus, non varius leo. Morbi nisl justo, pharetra eget dui quis, commodo finibus nisi. Cras ullamcorper dignissim tellus sit amet dictum. Vestibulum condimentum lorem sed augue elementum pulvinar.</p>
                    <h2>Participer au salon</h2>
                    <p>Sed volutpat semper consectetur. Mauris volutpat fermentum felis, at bibendum dui molestie sit amet. Morbi ut nisl auctor, vehicula dolor at, venenatis orci. Donec lacinia, ipsum quis ultrices mattis, orci diam maximus dui, at interdum nisl nisi eget sem. Cras mollis purus sit amet dapibus molestie. Quisque rutrum, neque vel dictum lacinia, nisi ex pharetra leo, vel dapibus eros arcu ac nunc. Aliquam id consectetur ipsum. Donec mauris lorem, elementum vitae mollis nec, vehicula id sem. Praesent semper risus nec odio pharetra pellentesque. Nam a semper magna, eget scelerisque ante.</p>
                </div>
            </div>
        </div>
    </body>
</html>