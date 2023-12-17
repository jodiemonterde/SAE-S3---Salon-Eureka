<?php session_start(); 
// $_SESSION['filtre'] est un tableau qui contient les id des filtres selectionnes
if ($_SESSION['filtre'] == null) {
    $_SESSION['filtre'] = array();
}
if (isset($_POST['nouveauFiltre'])) {
    if (in_array($_POST['nouveauFiltre'], $_SESSION['filtre'])) {
        $index = array_search($_POST['nouveauFiltre'], $_SESSION['filtre']);
        unset($_SESSION['filtre'][$index]);
    } else {
        array_push($_SESSION['filtre'], $_POST['nouveauFiltre']);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="lib/bootstrap-5.3.2-dist/css/bootstrap.css">
        <link rel="stylesheet" href="lib/fontawesome-free-6.5.1-web/css/all.css">
        <link rel="stylesheet" href="filtre.css">
        <title>pas dev</title>
    </head>
    <body>
        <div class="container">
            <h1 class="titre">Filtres</h1>
            <div class="row">
                <div class="col-12">
                    <?php
                        include 'fonctions/baseDeDonnees.php';
                        $pdo = connecteBD();
                        $fields = getFields($pdo);
                        $longestTextLength = 0;
                        while ($ligne = $fields->fetch()) {
                    ?>
                            <form action="filtre.php" method="post">
                                <input type="hidden" name="nouveauFiltre" value="<?php echo $ligne['field_id']; ?>">
                                <button class="bouton-filtre <?php echo in_array($ligne['field_id'], $_SESSION['filtre']) ? "bouton-filtre-selectionner" : "bouton-filtre-deselectionner"?>"><?php echo $ligne['name']; ?></button>
                            </form>
                        <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>