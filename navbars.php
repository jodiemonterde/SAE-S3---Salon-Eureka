<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="./lib/fontawesome-free-6.2.1-web/css/all.css">
        <link rel="stylesheet" href="./navbars.css">
        <script src="./lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <title> Navbars </title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border bg-white">
            <div class="container-fluid">
                <div class="navbar-brand">
                    <img src="./ressources/logo_black.png" alt="Logo Eureka" class="d-inline-block align-text-top">
                    Eureka
                </div>
                <div class="navbar-right">
                    <ul class="navbar-nav">
                        <li class="nav-item nav-link p-2 d-none d-sm-block fond_actif_haut">
                            <!-- Si sur la liste des entreprises, mettre en jaune -->
                            <a class="lien couleur_actif_haut" href="#"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-2 d-none d-sm-block fond_inactif_haut">
                            <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                            <a class="lien couleur_inactif_haut" href="#"> Mes rendez-vous </a>
                        </li>
                        <li class="nav-item dropdown p-2 d-none d-sm-block fond_inactif_haut">
                            <a class="dropdown-toggle lien couleur_inactif_haut" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-sm-none">
                            <img src="./ressources/icone_deconnexion.png" alt="Se déconnecter">
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenu de test -->
        <div class="container">
            <h1> Titre </h1>
            <p> Paragraphe 1 </p>
            <p> Paragraphe 2 </p>
            <p> Paragraphe 3 </p>
            <p> Paragraphe 4 </p>
            <p> Paragraphe 5 </p>
            <p> Paragraphe 6 </p>
            <p> Paragraphe 7 </p>
            <p> Paragraphe 8 </p>
            <p> Paragraphe 9 </p>
            <p> Paragraphe 10 </p>
            <p> Paragraphe 11 </p>
            <p> Paragraphe 12 </p>
            <p> Paragraphe 13 </p>
            <p> Paragraphe 14 </p>
            <p> Paragraphe 15 </p>
            <p> Paragraphe 16 </p>
            <p> Paragraphe 17 </p>
            <p> Paragraphe 18 </p>
            <p> Paragraphe 19 </p>
            <p> Paragraphe 20 </p>
            <p> Paragraphe 21 </p>
            <p> Paragraphe 22 </p>
            <p> Paragraphe 23 </p>
            <p> Paragraphe 24 </p>
            <p> Paragraphe 25 </p>
        </div>

        <!-- Navbar du bas -->
        <nav class="navbar navbar-expand fixed-bottom d-sm-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <li class="nav-item actif_bas_texte">
                        <!-- Si sur la liste des entreprises, mettre l'icone blanche et le fond en jaune -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="#">
                            <img src="./ressources/entreprise_white.png" alt="Liste des entreprises">
                        </a>
                        <!-- Si sur la liste des entreprises, mettre en jaune -->
                        Entreprises
                    </li>
                    <li class="nav-item inactif_bas">
                        <!-- Si sur la liste des rendez-vous, mettre l'icone blanche et le fond en jaune -->
                        <a class="d-flex justify-content-center" href="#">
                            <img src="./ressources/rendez-vous_black.png" alt="Mes rendez-vous">
                        </a>
                        <!-- Si sur la liste des rendez-vous, mettre en jaune -->
                        Rendez-vous
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>