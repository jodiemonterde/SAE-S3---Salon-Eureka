<?php
    session_start();
    $user = 1;
    $field_id = 1;
    include("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../../../lib/bootstrap-5.3.2-dist/css/bootstrap.css">
    <link rel="stylesheet" href="../../../lib/fontawesome-free-6.5.1-web/css/all.css">
    <link rel="stylesheet" href="./listeEntreprise.css">
    <title>Eureka - Liste des entreprises</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row d-flex align-items-center h-100">
            <div class="form-outline order-md-2 col-md-4 col-12 order-1 align-middle" data-mdb-input-init>
                <input type="search" id="recherche" class="form-control" placeholder="&#xf002 Rechercher une entreprise" aria-label="Search" />    
            </div>
            <div class="searchButton order-3 d-none d-md-block col-md-2"><button class="bouton">Rechercher</button></div>
            <div class="col-md-6 col-12 order-md-1 order-2">
                <h2> Prenez rendez-vous avec les entreprises qui vous correspondent. </h2>
                <p> Choisissez toutes les entreprises que vous souhaitez rencontrer au salon Eureka et prenez rendez-vous en un clic ! Dès le XX mois, vous pourrez venir consulter votre emploi du temps pour le salon créée à partir de vos demandes de rendez-vous. </p>
            </div>
        </div>
        <?php
            $stmt = getEntreprisesPerField($pdo, $field_id);
            while ($ligne = $stmt->fetch()) { 
        ?>
        <!-- Accordéon Bootstrap -->
        <div class="accordion" id=<?php echo '"companyAccordion'.$ligne['company_id'].'"'?>>
            <div class="card">
                <div class="card-header bg-white" id=<?php echo '"heading'.$ligne['company_id'].'"'?>>
                    <h2 class="mb-0 d-flex">
                        <button class="btn btn-link text-start d-flex flex-fill align-items-center justify-content-between" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $ligne['company_id']?>" aria-expanded="false" aria-controls="collapse<?php echo $ligne['company_id']?>">
                            <div class="profil-det-img d-flex text-start">
                                <div class="dp"><img src="../../../ressources/no-photo.png" alt=""></div>
                                <div class="pd">
                                    <h2><?php echo $ligne["name"]?></h2>
                                    <ul class="text-left">
                                        <li><i class="fa-solid fa-briefcase text-left"></i> <?php echo $ligne["sector"]?></li>
                                        <li><i class="fa-solid fa-location-dot"></i> <?php echo $ligne["address"]?></li>
                                    </ul>
                                </div>
                            </div>
                            <img src="../../../ressources/arrow.png" alt="Flèche" class="toggle-image d-none d-md-block">
                        </button> 
                    </h2>
                </div>
                <div id=<?php echo '"collapse'.$ligne['company_id'].'"'?> class="collapse" aria-labelledby=<?php echo '"heading'.$ligne['company_id'].'"'?> data-bs-parent=<?php echo '"#companyAccordion'.$ligne['company_id'].'"'?>>
                    <div class="card-body">
                        <!-- Contenu de l'accordéon -->
                        <div class="row">
                            <div class="description"><?php echo $ligne["description"]?></div>
                            <?php
                            $stmtEtudiant = getStudentsPerCompany($pdo, $ligne["company_id"]);
                            while ($ligneEtudiant = $stmtEtudiant->fetch()) { 
                            ?>
                            <hr>
                            <h2 class="student"><?php echo $ligneEtudiant["username"]?></h2>
                            <p><?php echo $ligneEtudiant["name"]?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
