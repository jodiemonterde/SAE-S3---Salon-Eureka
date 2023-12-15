<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"> 
        <link rel="stylesheet" href="jsp.css">
        <title> Eureka - Liste des entreprises </title>
    </head>
    <body>
        <?php
            // Récupérer le nom de la page
            $currentPage = basename(__FILE__);
        ?>

        <div class="navbar navbar-default sticky-top d-none d-sm-block bg-secondary" role="navigation">
            <div class="container">
                <div class="navbar-brand">
                    Logo Eureka
                </div>
                <div class="navbar-right">
                    <input type="submit" class="btn btn-light" value="Logo Bouton Déconnexion">
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="form-outline order-md-2 col-md-4 col-12 order-1 align-middle" data-mdb-input-init>
                    <input type="search" id="recherche" class="form-control" placeholder="&#xf002 Rechercher une entreprise" aria-label="Search" />    
                </div>
                <div class="searchButton order-3 d-none d-md-block col-md-2""><button>Rechercher</button></div>
                <div class="col-md-6 col-12 order-md-1 order-2">
                    <h2> Prenez rendez-vous avec les entreprises qui vous correspondent. </h2>
                    <p> Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le XX mois, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. </p>
                </div>
            </div>
            <div class="row">
                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>
                


                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>


                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>


                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>


                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>


                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>


                <div class="col-12 company dl-search-result-title-container">
                    <div class="row">
                        <div>            
                            <div class="col-md-4">
                                <div class="profil-det-img d-flex">
                                    <div class="dp">
                                       <img src="../../../ressources/no-photo.png" alt="">
                                    </div>
                                    <div class="pd">
                                        <h2>Nom de l'entreprise</h2>
                                        <ul>
                                            <li><i class="fa-solid fa-briefcase"></i> Secteur d'activité</li>
                                            <li><i class="fa-solid fa-location-dot"></i> Adresse de l'entreprise</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum quis consectetur massa. Nullam eleifend magna id convallis eleifend. Sed in tempus magna, sed vestibulum nisi. Suspendisse rhoncus, lorem nec elementum feugiat, erat neque aliquet risus, quis semper eros ligula a dolor. Sed sit amet vulputate neque. Morbi at vehicula lectus. Mauris ullamcorper ac elit eget luctus. Proin in lorem libero.
                        </div>
                    </div>
                </div>
            
            </div>
        </div>
    </body>

    <div class="container">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-3">
                <input type="submit" class="btn btn-light" value="Logo Bouton Entreprises">
            </div>
            <div class="col-2"></div>
            <div class="col-3">
                <input type="submit" class="btn btn-light" value="Logo Bouton Rendez-vous">
            </div>
            <div class="col-2"></div>
        </div>
    </div>

    <div class="navbar navbar-default sticky-bottom d-sm-none bg-secondary" role="navigation">
            <div class="container">
                <div class="navbar-brand">
                    Logo Eureka
                </div>
                <div class="navbar-right">
                    <input type="submit" class="btn btn-light" value="Logo Bouton Déconnexion">
                </div>
            </div>
        </div>
</html>