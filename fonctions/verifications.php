<?php
    // Fonction de vérification de l'importation des étudiants
    function importationEtudiantsCorrecte($contenu, $filieres) {
        $message = "";

        // Parcours chaque étudiant dans le contenu du fichier
        foreach ($contenu as $key => $etudiant) {
            // Affiche temporairement la valeur de la troisième colonne (à des fins de débogage)
            echo "|".$etudiant[3]."|";

            // Vérifie le nombre correct de colonnes dans le fichier
            if (count($etudiant) != 4) {
                $message .= "Le fichier ne contient pas le bon nombre de colonnes. Ligne ".($key + 1)."<br>";
            } 
            // Vérifie si des informations importantes sont manquantes
            elseif ($etudiant[0] == '' || $etudiant[1] == '' || $etudiant[2] == '' || $etudiant[3] == '') {
                $message .= "L'étudiant ".($key + 1)." n'est pas correct, certaines informations sont vides.<br>";
            }
            // Vérifie la validité du mot de passe de l'étudiant
            if (!preg_match("/^(?=.*\d)(?=.*[_\W]).{8,}$/", $etudiant[2])) {
                $message .= "Le mot de passe de l'étudiant ".($key + 1)." n'est pas correct.<br>";
            }
            // Vérifie si la filière de l'étudiant est parmi celles autorisées
            if (!in_array($etudiant[3], $filieres)) {
                $message .= "La filière de l'étudiant ".($key + 1)." n'est pas correcte.<br>";
            }
        }

        // Retourne le message d'erreur (s'il y en a un, une chaine vide sinon)
        return $message; 
    }
?>
