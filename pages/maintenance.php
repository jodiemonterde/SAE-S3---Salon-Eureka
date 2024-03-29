<!-- Page de maintenance sur laquelle est redirigée l'utilisateur en cas d'erreur liée à la base de données. -->
<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Métadonnées et liens vers les feuilles de style -->
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="../css/all.css">
        <link rel="stylesheet" href="../css/maintenance.css">
        
        <title>Eurêka en cours de maintenance</title>
    </head> 
    <body>
        <div class="container">
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                        <img src="../ressources/logo_black.svg" alt="logo" class="img-fluid logo">
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <h1 class="text-center">Eurêka</h1>
                    </div>
                    <div class="col-12 d-flex justify-content-center mt-3">
                        <h5 class="text-center">Le site est actuellement en cours de maintenance, veuillez revenir plus tard.</br> Si le problème persiste, veuillez contacter le service informatique à l'IUT</h5>
                    </div>

                    <!-- Bouton permmettant de retourner sur la page d'accueil du site - qui reste disponible même lorsque la base de données est indisponible. -->
                    <div class="col-12 d-flex justify-content-center mt-3">
                        <a href="../fonctions/deconnecter.php">
                            <button type="button" class="bouton">Retour à l'accueil</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>