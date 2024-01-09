<?php
    function importationEtudiantsCorrecte($contenu, $filieres) {
        $message = "";
        foreach ($contenu as $key => $etudiant) {
            echo "|".$etudiant[3]."|";
            if (count($etudiant) != 4) {
                $message .= "Le fichier ne contient pas le bon nombre de colonnes. Ligne ".($key + 1)."<br>";
            } elseif ($etudiant[0] == '' || $etudiant[1] == '' || $etudiant[2] == '' || $etudiant[3] == '') {
                $message .= "L'étudiant ".($key + 1)." n'est pas correct, certaines informations sont vides.<br>";
            }
            if (!preg_match("/^(?=.*\d)(?=.*[_\W]).{8,}$/", $etudiant[2])) {
                $message .= "Le mot de passe de l'étudiant ".($key + 1)." n'est pas correct.<br>";
            }
            if (!in_array($etudiant[3], $filieres)) {
                $message .= "La filière de l'étudiant ".($key + 1)." n'est pas correcte.<br>";
            }
            
        }
        return $message; 
    }
?>