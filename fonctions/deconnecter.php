<?php
    // Démarre une session PHP existante ou en crée une nouvelle
    session_start();

    // Détruit toutes les données de la session en cours
    session_destroy(); 

    // Redirige l'utilisateur vers la page d'accueil (index.php) après la déconnexion
    header('Location: ../index.php'); 

    // Termine l'exécution du script après la redirection, assurant que rien n'est exécuté après cette ligne
    exit();
?>