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

function addNewStudent($pdo, $nom, $prenom, $email, $mdp, $filiere) {
    $nom = htmlspecialchars($nom);
    $prenom = htmlspecialchars($prenom);
    $email = htmlspecialchars($email);
    $mdp = htmlspecialchars($mdp);
    $filiere = htmlspecialchars($filiere);

    $username = $prenom.' '.$nom;
    $stmt = $pdo->prepare("INSERT INTO User (username, password, responsibility, email)
                           VALUES (:username, :password, 'E', :email)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $mdp);
    $stmt->bindParam(':email', $email);
    $stmt->execute(); 

    // Récupérer l'ID généré automatiquement
    $user_id = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO AssignmentUser (field_id, user_id)
                           VALUES (:field_id, :user_id)");
    $stmt->bindParam(':field_id', $filiere);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
}

function getEntreprisesPerStudent($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT Company.company_id,name,logo_file_name,address,sector
                           FROM Company
                           JOIN WishList ON Company.company_id = WishList.company_id
                           WHERE user_id = $user_id");
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
    $sql = "SELECT DISTINCT Company.company_id, Company.name, Company.description, Company.address, Company.sector
            FROM Company
            JOIN Speaker ON Company.company_id = Speaker.company_id
            JOIN AssignmentSpeaker ON AssignmentSpeaker.speaker_id = Speaker.speaker_id
            WHERE field_id IN ($field_ids_str)";

    // Ajoutez la condition de recherche à la requête si elle est fournie
    if ($recherche != null) {
        $sql .= " AND Company.name LIKE :recherche";
    }

    // Ajout de l'ordre de tri à la requête
    $sql .= " ORDER BY Company.name";

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

function getInfoStudents($pdo, $recherche, $field_ids) {
    $field_ids_str = implode(', ', $field_ids);

    if ($field_ids_str == null) {
        return null;
    }

    $sql = "SELECT u.username, f.name AS filiere, COUNT(w.company_id) AS nbSouhait, u.user_id
            FROM User u
            JOIN AssignmentUser au
            ON u.user_id = au.user_id
            JOIN Field f
            ON au.field_id = f.field_id
            LEFT JOIN WishList w
            ON u.user_id = w.user_id
            WHERE u.responsibility = 'E'
            AND f.field_id IN ($field_ids_str)
            GROUP BY u.username, filiere";

    if ($recherche != null) {
        $sql .= " HAVING u.username LIKE :recherche";
    }

    $sql .= " ORDER BY u.username";

    $stmt = $pdo->prepare($sql);

    if ($recherche != null) {
        $stmt->bindValue(':recherche', '%' . $recherche . '%', PDO::PARAM_STR);
    }

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

?>