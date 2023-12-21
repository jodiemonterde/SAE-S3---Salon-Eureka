<?php 
    session_start();
    require("../../../fonctions/baseDeDonnees.php");
    $pdo = connecteBD();
    if(isset($_POST['dateForum']) && isset($_POST['heureDebut']) && isset($_POST['heureFin']) && isset($_POST['duree']) && isset($_POST['secDuree']) && isset($_POST['dateLim'])){
        updateForum($pdo,$_POST['dateForum'],$_POST['heureDebut'],$_POST['heureFin'],$_POST['duree'],$_POST['secDuree'],$_POST['dateLim']);
    }
    $infoForum = infoForum($pdo); 
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../../../outils/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="../../../../outils/fontawesome-free-6.5.1-web/css/all.css">
        <script src="../../../../outils/bootstrap-5.3.2-dist/js/bootstrap.js"></script>
        <link rel="stylesheet" href="forum.css">
        <title>informations eureka</title>
    </head>
    <body>
        <h1 class="text-center"> navbar </h1>
        <div class="container">
            <div class="row mx-1">
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center formulaire">
                    <form action="menu.php" method="POST">
                        <?php $ligne = $infoForum->fetch();?>
                        <div class="row p-0">
                            <div class="col-12">
                                <label for="dateForum">Date du forum :</label><br/>
                                <input type="date" value=<?php echo $ligne['date'];?>   name="dateForum">
                            </div>
                            <div class="col-12">
                                <label for="heureDebut">Heure de début du forum :</label><br/>
                                <input type="time" value=<?php echo $ligne['start'];?> name="heureDebut">
                            </div>
                            <div class="col-12">
                                <label for="heureFin">Heure de fin du forum :</label><br/>
                                <input type="time" value=<?php echo $ligne['end'];?> name="heureFin">
                            </div>
                            <div class="col-12">
                                <label for="duree">durée par défaut d'un rendez-vous :</label><br/>
                                <input type="time" value=<?php echo $ligne['primary_appointment_duration'];?> name="duree">
                            </div>
                            <div class="col-12">
                                <label for="secDuree">durée secondaire d'un rendez-vous :</label><br/>
                                <input type="time" value=<?php echo $ligne['secondary_appointment_duration'];?> name="secDuree">
                            </div>
                            <div class="col-12">
                                <label for="dateLim">Date limite avant la création du planning :</label><br/>
                                <input type="date" value=<?php echo $ligne['wish_period_end'];?> name="dateLim" >
                            </div>
                            <div class="row text-center p-0 m-0 ">
                                <div class="col-6">
                                    <button>Annuler</button>
                                </div>
                                <div class="col-6">
                                    <button type="submit">Valider</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                    <button class="bouton">Génerer le planning </button>
                </div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4 col-12 text-center">
                <button class="bouton" type="button" data-bs-toggle="modal" data-bs-target="#réinitialisation">Réinitialiser les données</button>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
        
        <!-- Modal de confirmation pour la réinitialisation des données -->
        <div class="modal fade " id="réinitialisation" tabindex="-1" aria-labelledby="réinitialiser" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header ">
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body  ">
                        <div class="container">
                            <div class = "row">
                                <div class="col-12">
                                    <h1 class="text-center" id="réinitialiser">Réinitialiser les données</h1>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-12">
                                    <P class="text-center">Êtes-vous sûr(e) de vouloir réinitialiser les données ?</P>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-dismiss="modal">Retour</button>
                                </div>
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#donnéeSupprimer" >Réinitialiser</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade " id="donnéeSupprimer" tabindex="-1" aria-labelledby="supression" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header ">
                        <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body  ">
                        <div class="container">
                            <div class = "row">
                                <div class="col-12">
                                    <h1 class="text-center" id="supression">Réinitialiser les données</h1>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-12">
                                    <P class="text-center">Les données suprimmées sont : la liste des entreprise, les données de planning 
                                                           et du forum ainsi que les etudiants et les intervenants</P>
                                </div>
                            </div>
                            <div class = "row">
                                <div class="col-6 d-flex justify-content-evenly">
                                    <button type="button" data-bs-dismiss="modal">Retour</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
</html>