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

function getSpeakersPerCompany($pdo, $company_id) {
    $stmt = $pdo->prepare("SELECT Speaker.name, Speaker.speaker_id
                           FROM Company
                           JOIN Speaker
                           ON Company.company_id = Speaker.company_id
                           WHERE Company.company_id = $company_id;");
    $stmt->execute();
    return $stmt;
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

?>