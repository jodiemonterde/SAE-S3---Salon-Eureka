<?php

function connecteBD() {
    // Database configuration
    $host = 'mysql-sae-nmms.alwaysdata.net';
    $dbName = 'sae-nmms_eureka';
    $username = 'sae-nmms';
    $password = 'NicolMonterdeMiquelSchardt';

    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $username, $password);
        
        // Set PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // You can now use the $pdo object to perform database operations
        
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
    return $pdo;
}

function getEntreprisesForStudent($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT DISTINCT Company.name, Company.description, Company.address, Company.sector
                           FROM Company
                           JOIN Speaker
                           ON Company.company_id = Speaker.company_id
                           JOIN AssignmentSpeaker
                           ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
                           JOIN AssignmentUser
                           ON AssignmentUser.field_id = AssignmentSpeaker.field_id
                           WHERE user_id = $user_id;");
    $stmt->execute();
    return $stmt;
}

function getEntreprises($pdo, $field_ids, $recherche) {
    // Utilisez la fonction implode pour convertir le tableau en une chaîne séparée par des virgules
    $field_ids_str = implode(', ', $field_ids);

    if ($field_ids_str == null) {
        return null;
    }

    // Requête SQL de base
    $sql = "SELECT c.company_id, c.name, c.description, c.address, c.sector, GROUP_CONCAT(DISTINCT CONCAT(s.name, ',', COALESCE(s.role, ''), ',', af.fields) SEPARATOR ';') AS intervenants_roles
            FROM Company c
            JOIN Speaker s ON c.company_id = s.company_id
            JOIN (SELECT a.speaker_id, GROUP_CONCAT(DISTINCT f.name SEPARATOR '/') AS fields
            FROM AssignmentSpeaker a
            JOIN Field f ON a.field_id = f.field_id
            WHERE a.field_id IN ($field_ids_str)
            GROUP BY a.speaker_id) af ON s.speaker_id = af.speaker_id";

    // Ajoutez la condition de recherche à la requête si elle est fournie
    if ($recherche != null) {
        $sql .= " WHERE c.name LIKE :recherche";
    }

    // Ajout de l'ordre de tri à la requête
    $sql .= " 
              GROUP BY c.company_id, c.name, c.description, c.address, c.sector
              ORDER BY c.name";

    // Préparation de la requête
    $stmt = $pdo->prepare($sql);

    // Si une recherche est fournie, liez le paramètre
    if ($recherche != null) {
        $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
    }

    // Exécution de la requête
    $stmt->execute();

    // Retourne le résultat de la requête
    return $stmt;
}

function getStudentsPerCompany($pdo, $company_id) {
    $stmt = $pdo->prepare("SELECT Field.name, User.username
                           FROM Company
                           JOIN Speaker
                           ON Company.company_id = Speaker.company_id
                           JOIN Appointment
                           ON Speaker.speaker_id = Appointment.speaker_id
                           JOIN User
                           ON Appointment.user_id = User.user_id
                           JOIN AssignmentUser
                           ON User.user_id = AssignmentUser.user_id
                           JOIN Field
                           ON AssignmentUser.field_id = Field.field_id
                           WHERE Company.company_id = $company_id;");
    $stmt->execute();
    return $stmt;
}

function getSpeakersPerCompany($tableau_intervenants) {
    // Séparer la chaîne principale en utilisant ';' comme délimiteur pour obtenir les intervenants
    $intervenants_array = explode(';', $tableau_intervenants);
        
    // Initialiser un tableau pour stocker les données finales
    $final_array = array();

    // Parcourir chaque intervenant
    foreach ($intervenants_array as $intervenant) {
        // Exploser les différents éléments de l'intervenant en utilisant ',' comme délimiteur
        $intervenant_elements = explode(',', $intervenant);

        // Vérifier si les éléments attendus existent avant d'y accéder
        $nom = isset($intervenant_elements[0]) ? $intervenant_elements[0] : '';
        $fonction = isset($intervenant_elements[1]) ? $intervenant_elements[1] : '';
        
        // Exploser les filières (fields) de l'intervenant en utilisant '/' comme délimiteur
        $fields_array = isset($intervenant_elements[2]) ? explode('/', $intervenant_elements[2]) : array();

        // Ajouter les éléments au tableau final
        $final_array[] = array(
            'nom' => $nom,
            'fonction' => $fonction,
            'fields' => $fields_array
        );
    }

    // Utiliser $final_array comme nécessaire
    return $final_array;
}

function getFieldsPerSpeakers($pdo, $speaker_id) {
    $stmt = $pdo->prepare("SELECT Field.name
                           FROM Speaker
                           JOIN AssignmentSpeaker
                           ON Speaker.speaker_id = AssignmentSpeaker.speaker_id
                           JOIN Field
                           ON AssignmentSpeaker.field_id = Field.field_id
                           WHERE Speaker.speaker_id = $speaker_id;");
    $stmt->execute();
    return $stmt;
}

function getFields($pdo) {
    $sql = "SELECT * FROM `Field`";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt;
}

function search($query) {
    $query = htmlspecialchars($query);
    $stmt = $pdo->prepare("SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector
                           FROM Company
                           WHERE (Company.name LIKE '%".$query."%')
                           ORDER BY Company.name");
    $stmt->execute();
    return $stmt;
}

function supprimerEntreprise($pdo, $company_id) {
    $stmt = $pdo->prepare("DELETE FROM Company
                           WHERE company_id = $company_id");
    return $stmt->execute();
}

//TODO faire la requête pour mettre à jour et ajouter les données
function modifierEntreprise($pdo, $company_id, $nom_entreprise, $secteur_activite, $lieu, $description) {
    $stmt = $pdo->prepare("");
    return $stmt->execute();
}

function addCompany($pdo, $nom, $description, $adresse, $codePostal, $ville, $secteur, $logo) {
    if ($logo != null) {
        $targetDirectory = "../../../../ressources/logosentreprises/";
        var_dump(pathinfo($logo["name"]));
        $imageFileType = strtolower(pathinfo($logo["name"], PATHINFO_EXTENSION));
        var_dump($imageFileType);

        // Générer un nom de fichier unique basé sur le nom de l'entreprise
        // Supression des caractères non autorisés
        $cleanedFileName = preg_replace('/[^\w\d\-_.]/', '', $nom);
    
        // Limiter la longueur du nom de fichier si nécessaire
        $cleanedFileName = substr($cleanedFileName, 0, 80);
        $newFileName = $cleanedFileName . '_' . uniqid() . '.' . $imageFileType;
        $targetFile = $targetDirectory . $newFileName;
        var_dump($targetFile);
        $uploadOk = 1;
    
        // Vérifier si le fichier est une image réelle
        $check = getimagesize($logo["tmp_name"]);
        if ($check === false) {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }
    
        // Vérifier si le fichier existe déjà
        if (file_exists($targetFile)) {
            echo "Désolé, le fichier existe déjà.";
            $uploadOk = 0;
        }
    
        // Vérifier la taille du fichier
        if ($logo["size"] > 500000) {
            echo "Désolé, votre fichier est trop volumineux.";
            $uploadOk = 0;
        }
    
        // Autoriser certains formats de fichiers
        $allowedFormats = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
            $uploadOk = 0;
        }
    
        // Vérifier si $uploadOk est défini à 0 par une erreur
        if ($uploadOk == 0) {
            echo "Désolé, votre fichier n'a pas été téléchargé.";
        } else {
            // Si tout est correct, essayez de télécharger le fichier
            if (move_uploaded_file($_FILES["logoEntreprise"]["tmp_name"], $targetFile)) {
                echo "Le fichier " . htmlspecialchars(basename($_FILES["logoEntreprise"]["name"])) . " a été téléchargé.";
    
                // Maintenant, vous pouvez utiliser $targetFile comme chemin à stocker dans la base de données
                $pathToLogo = $targetFile;
    
                // Insérez le chemin dans la base de données
                $query = "INSERT INTO entreprises (nom, description, adresse, secteur, logo) VALUES ('$nom', '$description', '$adresse', '$secteur', '$pathToLogo')";
                // ... exécutez la requête ...
    
            } else {
                echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.";
            }
        }
    } else {
        $targetFile = "../../../../ressources/logosentreprises/no-photo.png";
    }

    $nom = htmlspecialchars($nom);
    $description = htmlspecialchars($description);
    $adresse = htmlspecialchars($adresse . ', ' . $codePostal . ' ' . $ville);
    $secteur = htmlspecialchars($secteur);

    $stmt = $pdo->prepare("INSERT INTO Company (name, logo_file_name, description, address, sector, excluded)
                           VALUES (:nom, :logo, :description, :adresse, :secteur, 0)");
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':adresse', $adresse);
    $stmt->bindParam(':secteur', $secteur);
    $stmt->bindParam(':logo', $pathToLogo);
    $stmt->execute(); 
}

?>