<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="./lib/fontawesome-free-6.2.1-web/css/all.css">
        <script src="./lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <title> Navbars </title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <img src="./ressources/logo_black.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="navbar-right">
                    <ul class="navbar-nav">
                        <li class="nav-item p-2">
                            <!-- Si sur la liste des entreprises, mettre en jaune -->
                            <a class="d-none d-sm-block"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item p-2">
                            <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                            <a class="d-none d-sm-block"> Mes rendez-vous </a>
                        </li>
                        <li class="nav-item dropdown p-2">
                            <a class="d-none d-sm-block dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur Dropdown
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="d-sm-none"> Icone Bouton Déconnexion </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>



        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand sticky-bottom d-sm-none border">
            <div class="container-fluid">
                <ul class="navbar-nav justify-content-evenly">
                    <li class="nav-item">
                        <!-- Si sur la liste des entreprises, mettre en jaune -->
                        <a> Icone Entreprises </a>
                        <a> Entreprises </a>
                    </li>
                    <li class="nav-item">
                        <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                        <a> Icone Rendez-vous </a>
                        <a> Rendez-vous </a>
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>