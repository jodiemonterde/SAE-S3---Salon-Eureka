<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="./lib/fontawesome-free-6.2.1-web/css/all.css">
        <link rel="stylesheet" href="./css/navbars.css">
        <script src="./lib/bootstrap-5.3.2-dist/js/bootstrap.bundle.js"></script>
        <title> Navbars </title>
    </head>
    <body>
        <!-- Navbar du haut -->
        <nav class="navbar navbar-expand sticky-top border-bottom bg-white p-0">
            <div class="container-fluid h-100">
                <div class="navbar-brand d-flex align-items-center h-100">
                    <img src="./ressources/logo_black.svg" alt="Logo Eureka" class="logo me-2">
                    Eureka
                </div>
                <div class="navbar-right h-100">
                    <ul class="navbar-nav d-flex h-100 align-items-center">
                        <li class="nav-item nav-link p-0 d-none d-md-block h-100">
                            <!-- Si sur la liste des entreprises, mettre en actif et lien_inactif-->
                            <a class="actif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Liste des entreprises </a>
                        </li>
                        <li class="nav-item nav-link p-0 h-100 d-none d-md-block">
                            <!-- Si sur la liste des rendez-vous, mettre en actif et lien_inactif -->
                            <a class="inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" href="#"> Mes rendez-vous </a>
                        </li>
                        <li class="nav-item dropdown p-0 h-100 d-none d-md-block">
                            <a class="dropdown-toggle inactif_haut d-flex align-items-center h-100 px-2 justify-content-center text-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Pseudo Utilisateur
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li> <a class="dropdown-item" href="#"> Se déconnecter </a> </li>
                            </ul>
                        </li>
                        <li class="nav-item d-md-none d-flex justify-content-end">
                            <a href="#">
                                <img src="./ressources/icone_deconnexion.svg" alt="Se déconnecter">
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Contenu de test -->
        <div class="container-fluid">
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
        <nav class="navbar navbar-expand fixed-bottom d-md-none border bg-white">
            <div class="container-fluid">
                <ul class="navbar-nav w-100 justify-content-evenly">
                    <!-- Si sur la liste des entreprises, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center actif_bas_texte">
                        <!-- Si sur la liste des entreprises, mettre l'icone en actif et lien_inactif -->
                        <a class="d-flex justify-content-center actif_bas_icone" href="#">
                            <!-- Si sur la liste des entreprises, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="./ressources/icone_entreprise_white.svg" alt="Liste des entreprises" class="icone">
                        </a>
                        Entreprises
                    </li>
                    <!-- Si sur la liste des étudiants, mettre le texte en actif -->
                    <li class="nav-item d-flex flex-column text-center inactif_bas">
                        <!-- Si sur la liste des étudiants, mettre l'icône en actif et lien_inactif -->
                        <a class="d-flex justify-content-center" href="#">
                            <!-- Si sur la liste des étudiants, mettre l'icône blanche, sinon mettre l'icône en noir -->
                            <img src="./ressources/icone_etudiant_black.svg" alt="Liste des étudiants" class="icone">
                        </a>
                        Etudiants
                    </li>
                </ul>
            </div>
        </nav>
    </body>
</html>