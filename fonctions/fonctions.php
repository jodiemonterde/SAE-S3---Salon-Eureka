<?php
    // Fonction de redirection en utilisant JavaScript
    function redirect($url)
    {
        // Crée une balise script pour rediriger vers l'URL spécifiée
        $string = '<script type="text/javascript">';
        $string .= 'window.location = "' . $url . '"';
        $string .= '</script>';

        // Affiche la balise script, effectuant ainsi la redirection
        echo $string;
    }
?>